# bc-mcp OAuth2 重大脆弱性の修正 設計書

- 作成日: 2026-08-27
- 対象: `plugins/bc-mcp`（baserCMS 5.4.x）
- 位置づけ: セキュリティ監査（2026-08-27 実施）で「重大」と判定した3件の修正設計

## 背景

`bc-mcp` は baserCMS を MCP（Model Context Protocol）サーバーとして公開し、AI エージェントから
コンテンツを操作させるためのコアプラグインである。認証・認可には OAuth 2.0（league/oauth2-server 8.x）
を用い、RFC 7591 の動的クライアント登録（DCR）で Claude / ChatGPT などのクライアントを受け入れる。

ソース精読による監査の結果、認可フローの根幹に3件の重大な欠陥を確認した。本設計はこの3件のみを対象とする。

`bc-mcp` は未リリースのため、後方互換は考慮しない。既存の登録済みクライアントおよび発行済みトークンは
無効化されてよい。

## 対象とする問題

### ① 同意（authorize）エンドポイントに CSRF 対策が無い

`config/setting.php` の `BcApp.skipCsrfUrl` が `/bc-mcp/oauth2/*` をワイルドカードで登録しているため、
`CsrfProtectionMiddleware` が同意エンドポイントに対しても検証をスキップする。加えて
`Admin\Oauth2Controller::initialize()` が `FormProtection` の `validate` を `false` にしている。
結果として、同意フォームの POST は管理画面セッションのみに依存し、CSRF トークンを一切持たない。

攻撃は次の手順で成立する。

1. 無認証の DCR で、`redirect_uri` を攻撃者のサーバーに向けたクライアントを登録する
2. 管理画面にログイン中の運用者を罠ページへ誘導し、
   `POST /bc-mcp/oauth2/authorize?client_id=…&response_type=code&redirect_uri=攻撃者&code_challenge=…`
   を `action=approve` 付きで自動送信させる
3. 302 で認可コードが攻撃者のサーバーへ渡る。PKCE の verifier は攻撃者が保持しているため、
   トークン交換が成立する

これにより、その運用者の権限で MCP 経由の全操作が可能になる。現状 Chrome の SameSite=Lax
デフォルトが偶発的な緩和になっているのみで（SameSite の明示設定は無い）、設計上は無防備である。

### ② 暗号化キーがソースにハードコードされている

`OAuth2Service::getEncryptionKey()` が `env('OAUTH2_ENC_KEY', 'j6eyb4o…')` と固定値へフォールバックする。
この鍵は league/oauth2-server が認可コードとリフレッシュトークンを暗号化するために使う。
公開リポジトリに含まれるため、フォールバックが効いた環境では**全サイト共通の既知鍵**で動作する。

認可コードとリフレッシュトークンは DB にも保存され、`isAuthCodeRevoked()` /
`isRefreshTokenRevoked()` が「DB に無ければ無効」を返すため、現状ゼロからの偽造は成立しない。
しかし機密性は失われており、DB 照合という一枚の板だけで支えられている状態である。

`install()` は `putEnv()` で鍵を生成・保存するため、正規のインストール経路では設定される。
フォールバックが効くのは「`.env` に書き込めなかった」「`.env` を配らず環境変数注入で運用している」
「サイトを複製して `.env` を作り直した」といった経路であり、事故った環境でだけ静かに既知鍵になる。

### ③ 無認証の動的クライアント登録と client_credentials の併用

クライアント登録は無認証で開いており、登録可能なグラントに `client_credentials` が含まれ、
サーバー側でも `ClientCredentialsGrant` が有効になっている。したがって第三者が、
ユーザーの同意を一切経ずに有効なアクセストークンを取得できる。

このトークンは `McpProxyController` の認証を通過する。`tools/call` は
`McpContext::getLoginUserId()` が `0` になり `UsersService::get(0)` が例外を投げて 500 になるため
実データ操作は止まっているが、`initialize` / `tools/list` は通り、ツール一覧が無認証で読み取れる。
また設計上「ユーザー不在のトークンが存在しうる」こと自体が、後続の実装変更で容易に致命化する。

## 設計方針

MCP クライアント（Claude / ChatGPT）は DCR を前提に接続してくるため、**登録は開けたまま維持し、
登録できる内容を厳格化する**方針を採る。実害の根源であるユーザー不在トークンを絶ち、
同意フローを構造的に堅くする。

## 変更設計

### 変更が当たる層

| 層 | ファイル | 変更の性質 |
| --- | --- | --- |
| CSRF 設定 | `config/setting.php` | ワイルドカードを個別列挙へ |
| 同意フロー | `src/Controller/Admin/Oauth2Controller.php` | GET 検証 → セッション保持 → POST 決定へ再構成 |
| 鍵管理・グラント | `src/OAuth2/Service/OAuth2Service.php` | env 初期値削除、client_credentials 削除 |
| 登録検証 | `src/OAuth2/Service/OAuth2ClientRegistrationService.php` | 受け付けるメタデータの厳格化 |
| PSR-7 変換 | `src/Lib/OAuth2Util.php` | client_credentials 専用分岐の削除 |
| 例外 | `src/OAuth2/Exception/OAuth2ConfigurationException.php`（新設） | 設定不備の表現 |
| 受け口 | `src/Controller/Oauth2Controller.php`, `src/Controller/McpProxyController.php` | 設定不備時の 503 応答 |
| 管理画面 | `src/Controller/Admin/McpServerManagerController.php`, `templates/Admin/McpServerManager/index.php` | 停止中の警告表示 |
| 掃除 | `src/Command/Oauth2CleanupCommand.php` | 未使用クライアントの削除 |

### ① 同意フローの再構成

**CSRF ゲートを戻す。** `skipCsrfUrl` の `/bc-mcp/oauth2/*` を廃止し、Cookie を使わないエンドポイント
だけを個別に列挙する。

- `/bc-mcp`（MCP 本体）
- `/bc-mcp/oauth2/token`
- `/bc-mcp/oauth2/register`
- `/bc-mcp/oauth2/register/*`
- `/bc-mcp/oauth2/verify`
- `/bc-mcp/oauth2/client-info`

`/bc-mcp/oauth2/authorize` は列挙から外し、`CsrfProtectionMiddleware` の対象へ戻す。併せて
`Admin\Oauth2Controller::initialize()` の `FormProtection->setConfig('validate', false)` を削除する。
同意画面は自前レンダのフォームであり、トークンは素直に載る。

**GET（同意画面の表示）。** 認証チェックの後、現在は POST 時にしか呼んでいない
`AuthorizationServer::validateAuthorizationRequest()` をこの時点で実行する。`client_id` /
`redirect_uri` / `response_type` / PKCE / scope の妥当性は league に一本化されるため、コントローラに
手書きされている `in_array($redirectUri, $client->getRedirectUri())` と
`$responseType !== 'code'` のチェックは削除する。検証済みの `AuthorizationRequest` をセッションに
保存し、その内容（クライアント名・スコープ）だけを画面に描画する。

**POST（許可／拒否）。** CSRF トークン検証を通過した上で、クエリもボディも読まず、セッションに
保存された `AuthorizationRequest` のみを `completeAuthorizationRequest()` に渡す。許可なら
`setAuthorizationApproved(true)`、拒否なら `false` を設定し、拒否時のリダイレクト生成も league に
委ねて現在の手書き分岐を削除する。処理後はセッションから破棄する（ワンショット）。
RFC 9207 の `iss` 付与は現行どおり維持する。

セッションに認可リクエストが存在しない状態で POST された場合は、リダイレクトせず 400 を返す。

この構成により、CSRF が塞がるだけでなく「同意画面で見せた権限」と「実際に発行される権限」を
すり替える余地も無くなる。

### ② 暗号化キーの必須化

**取得の一本化。** `OAuth2Service::getEncryptionKey()` から第2引数の固定値を削除し、
`env('OAUTH2_ENC_KEY')` が空なら `OAuth2ConfigurationException`（新設）を投げる。
検証は「空でないこと」のみとし、長さや base64 形式の検証は行わない。`install()` が
`base64_encode(random_bytes(32))` を書く前提では、形式チェックは誤検知のほうが害になる。

**投げる場所。** `OAuth2Service` のコンストラクタ。認可コードを暗号化できない状態で ResourceServer
（既存トークンの検証）だけが生き残ると、「新規認可はできないが既存トークンは通る」という半端な
状態になるため、OAuth2 機能全体を一括で停止させる。

**止め方。** 例外は `Oauth2Controller` / `Admin\Oauth2Controller` / `McpProxyController` の初期化時に
捕捉する。

- OAuth2 エンドポイント: `503` ＋ `{"error":"server_error","error_description":"…"}`
- `/bc-mcp`: `503` ＋ JSON-RPC 形式のエラー（`code: -32603`）

サイト本体と管理画面は稼働を維持する。

**気付ける導線。** 「MCPサーバー管理」画面の先頭に、未設定時だけ警告パネルを表示する
（`config/.env` に `OAUTH2_ENC_KEY` を設定する旨と、現在 MCP が停止中である旨）。ツール一覧の取得は
暗号化キーに依存しないため、警告と併せて通常表示は維持できる。

**発生経路を潰す。** 実際的な最大の発生経路は「`install()` の `putEnv()` が `.env` に書けなかった」で
ある。`BcMcpPlugin::install()` が `putEnv()` の結果を見ていないため、書き込み失敗時はインストールを
失敗として扱う。

### ③ クライアント登録とグラントの厳格化

**ユーザー不在トークンを消す。** `OAuth2Service` から `ClientCredentialsGrant` を削除し、有効な
グラントを authorization_code（PKCE 必須）と refresh_token だけにする。登録サービスの
`supportedGrantTypes` からも `client_credentials` を外し、要求された場合は 400 で拒否する。
これにより `tools/call` が `user_id=0` で 500 になっていた歪みも解消する。

**宣言と実態の食い違いを直す。** メタデータは `token_endpoint_auth_methods_supported: ['none']` と
宣言しているのに、登録サービスの既定値は `client_secret_basic` である。PKCE 必須のパブリック
クライアントに一本化し、`token_endpoint_auth_method` は `none` のみを受け付ける。無認証 DCR で
誰でも「機密クライアント」を作れる状態自体を無くす意味も持つ。

**`redirect_uris` の検証。** authorization_code に一本化する以上、登録時に必須とする。現状は
`FILTER_VALIDATE_URL` を通すだけなので、次を追加する。

- スキームは `https`、加えて RFC 8252 のループバック（`http://127.0.0.1` / `http://[::1]`）のみ許可
- フラグメント付きは拒否（RFC 6749）
- 登録できる件数の上限は 5 件

**存在しないスコープを消す。** `supportedScopes` の `admin` は `OAuth2ScopeRepository` に実体が無く、
登録は通るのに機能しない。削除する。

**濫用の抑制。** baser-core にレート制限機構が無いため自前で用意する。`register` に Cache ベースの
単純なカウンタ（同一 IP から1時間あたり10件を上限とし、超過時は 429 を返す）を入れ、既存の
`Oauth2CleanupCommand` に「一度も認可に使われないまま登録から30日経過したクライアントの削除」を
追加する。いずれの閾値も `config/setting.php` の `BcMcp` 配下で変更可能にする。

## テスト

### 新規に固定する振る舞い

| 観点 | 期待 |
| --- | --- |
| CSRF トークン無しの同意 POST | 拒否される |
| セッションに認可リクエストが無い POST | 400（リダイレクトしない） |
| GET とは異なる `client_id` / `redirect_uri` を POST に混ぜる | 無視され、セッションの内容で処理される |
| `OAUTH2_ENC_KEY` 未設定時の `token` / `authorize` / `/bc-mcp` | 503 |
| `OAUTH2_ENC_KEY` 未設定時の管理画面 | 警告が表示される |
| `client_credentials` を含む登録要求 | 400 |
| `client_credentials` でのトークン要求 | 拒否される |
| `http://example.com` など不正スキームの `redirect_uri` 登録 | 400 |
| `redirect_uris` を省略した登録 | 400 |

### 既存テストの改修

`client_credentials` に依存しているのは次の4本であり、仕様変更に伴う正当な修正として書き換える。
「テストが通らないから期待値を緩める」形にはしない。

- `tests/TestCase/Controller/OAuth2ControllerTest.php`
- `tests/TestCase/Controller/OAuth2ControllerDynamicClientRegistrationTest.php`
- `tests/TestCase/Service/OAuth2ServiceTest.php`
- `tests/TestCase/Service/OAuth2ClientRegistrationServiceTest.php`

`client_secret_basic` を期待している登録系テスト2本と、`is_confidential => true` のフィクスチャは
`none` / パブリッククライアント前提へ書き換える。

### その他

`OAuth2Util::createPsr7Request()` の「POST の `client_id` / `client_secret` を Basic 認証ヘッダへ
変換する」分岐は client_credentials 専用のため削除する。

実行環境は `basercms-unittest` スキルの Docker 経路とし、`plugins/bc-mcp` のテスト全体がグリーンに
なることを完了条件とする。

自動テストで担保できないのは、実際の MCP クライアント（Claude / ChatGPT）からの接続確認である。
DCR は開けたまま維持するため接続手順自体は変わらない想定だが、`token_endpoint_auth_method` を
`none` に一本化した影響は実機で確認する。

## スコープ外

監査で挙げた以下の項目は、本設計には含めない。別途 spec を起こす。

- ツール引数 URL のフェッチによる SSRF（`BaseMcpTool::processUrlFile()`）
- スコープ（`mcp:read` / `mcp:write`）が強制されていない問題
- `loginUserId` をツール引数で偽装できる問題
- `client_secret` / `registration_access_token` の平文保存とタイミング比較
- 例外メッセージ・スタックトレースの外部露出
- JWKS が公開する鍵とアクセストークン署名鍵の不一致
- デバッグ時に全リクエストボディをログへ書き出す挙動
- セッションストアのパーミッション（0777）、秘密鍵ファイルのパーミッション
- Host ヘッダ由来の issuer / エンドポイント生成
