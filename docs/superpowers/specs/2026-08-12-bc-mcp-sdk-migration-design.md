# bc-mcp を MCP 2026-07-28（Dual-era）対応させる設計

- 作成日: 2026-08-12
- 改訂日: 2026-08-12（SDK の実地調査により常駐プロセス廃止へ方針変更。第11章に経緯）
- 対象: `plugins/bc-mcp`（BcMcp / baserCMS コアプラグイン）
- ブランチ: `dev-mcp-2026-07-28`（`dev-agentic` から分岐）
- 前提調査: [2026-08-12-mcp-2026-07-28-bc-mcp-impact.md](2026-08-12-mcp-2026-07-28-bc-mcp-impact.md)

## 1. 目的とゴール

MCP 仕様 `2026-07-28`（ステートレスコア）に対応し、**Modern（`2026-07-28`）と Legacy（`initialize` 方式）の両世代を同時に提供する Dual-era サーバー**にする。あわせて、常駐 MCP サーバープロセスを廃止し、CakePHP のリクエスト内で処理を完結させる。

現状 bc-mcp は完全な Legacy 世代サーバーであり、Claude が Modern 専用クライアントに切り替えた時点で通信不能になる。現時点で通信不能になるリスクはないが（Claude は Dual-era として動作していることを実測で確認済み）、依存している `php-mcp/server` が `2025-03-26` 止まりでリリースも停滞しているため、上流の更新では解決しない。

### スコープ

**含む**

- MCP SDK を `php-mcp/server` から `logiscape/mcp-sdk-php` v2 へ移植し、Dual-era 対応を得る
- **常駐 MCP サーバープロセスの廃止（in-process 化）** — 移植先 SDK が listen 型サーバーを提供しないため必然（第11章）
- SDK では解決しない独立項目の対応 — `Origin` ヘッダ検証、`iss` パラメータ付与、ネゴシエーションのロギング
- **固定ページ（Pages）ツールの新規追加** — 取得・作成・編集・削除の5ツール（第13章）
- 上記に対する自動テスト（Modern / Legacy 両世代の疎通を含む）

**含まない**

- **`league/oauth2-server` の 9系アップデート** — MCP 対応と OAuth のメジャー更新を混ぜない。`iss` 付与は 9系でも自前実装が必要なため、8.5.5 のままで対応可能
- **Client ID Metadata Documents 対応** — DCR は非推奨化されたが12ヶ月以上の猶予があり、互換のため残置してよい
- **自社プラグイン CuMcp（baserplugin リポジトリ）への反映** — 別途判断する
- **stdio トランスポートの廃止** — ローカルの stdio クライアント用途として `bc_mcp.server --transport=stdio` は残す

## 2. 到達点のアーキテクチャ

常駐プロセスと内部 HTTP 転送を廃止し、CakePHP のリクエスト内で SDK を実行する。

```
Claude / ChatGPT / MCP Inspector
  → POST /bc-mcp            McpProxyController
                              ・OAuth2 トークン検証（既存）
                              ・権限チェック（既存）
                              ・Origin 検証（新規）
                              ・ネゴシエーションのロギング（新規）
                              ・CakePHP ServerRequest → HttpMessage 変換（新規）
  → McpRequestHandler       HttpServerRunner::handleRequest() をプロセス内で実行（新規）
  → Mcp\Server\McpServer    各 *Tool → baserCMS Service 層
  ← HttpMessage → CakePHP Response 変換
```

プロトコルの世代判定・`server/discover`・必須ヘッダ検証・`resultType` / `ttlMs` / `cacheScope` の付与はすべて SDK が担う。bc-mcp 側の責務は「ツールの定義と登録」「認証・認可」「CakePHP と SDK の間の変換」に純化する。

**廃止するもの**

- `McpServerManger`（PID ファイル管理・`ps` による死活監視・起動/停止/再起動）
- 管理画面の MCP サーバー起動・停止 UI
- `McpServerCommand` の HTTP（`sse`）モード
- 内部サーバーへの HTTP 転送（`Cake\Http\Client` による `127.0.0.1:{port}` への POST）

これにより次の問題が同時に解消する。

| 現状の問題 | 解消理由 |
|---|---|
| 起動忘れでプロキシが 503 を返す | 常駐プロセスが存在しない |
| ツール定義や設定の変更が常駐プロセスに反映されない | リクエストごとにサーバーを組み立てる |
| `shell_exec("ps -p …")` による脆い死活監視 | 不要になる |
| 常駐プロセスの DB 接続切り替え（`--connection` オプション） | CakePHP のリクエストコンテキストをそのまま使う |
| サーバー再起動・デプロイごとの手動起動 | 不要になる |

## 3. SDK 移植の設計

### 3.1 ツール登録 API の対応

SDK の `tool()` は `inputSchema` / `outputSchema` を明示的に受け取れる。

```php
public function tool(
    string $name, string $description, callable $callback,
    ?string $title = null, ?array $icons = null,
    ?array $outputSchema = null, ?array $inputSchema = null,
    string $taskSupport = TaskSupport::FORBIDDEN,
    array|ToolAnnotations|null $annotations = null,
    string $taskInputMode = TaskInputMode::IN_TASK,
): self
```

したがって**既存の `inputSchema` 定義（description 付きの詳細な JSON Schema）はそのまま流用できる**。ツールの定義内容を書き直す必要はなく、登録の呼び出し方だけを変える。

SDK にはクラス単位の一括登録機構（`#[Tool]` 属性やクラススキャン）が存在しないため、「各 `*Tool` クラスが自分のツールを登録する」という現在の構造を維持する。

### 3.1.1 `outputSchema` の宣言（必須）

**SDK はツールのコールバックの戻り値を、`string` または `CallToolResult` に限って受け付ける。** bc-mcp の全ツールは配列（エンティティを配列化したものや一覧）を返すため、そのままでは次のエラーになる。

```
Invalid tool handler result: expected string or CallToolResult, got array
```

`outputSchema` を宣言したツールに限り、SDK は戻り値を任意の JSON 値として扱い（SEP-2106）、`structuredContent` に載せつつ JSON を `TextContent` にも出力する。**したがって全ツールの登録に `outputSchema` を指定する。**

個々のツールの戻り値の構造はエンティティの構成に依存するため、`BaseMcpTool::OUTPUT_SCHEMA` に型のみを宣言した共通のスキーマを置き、各登録から参照する。

```php
protected const OUTPUT_SCHEMA = ['type' => ['object', 'array']];
```

この方式には、`content[0].text` に JSON が載るため**既存のツールテストと従来のクライアントの互換が保たれる**という利点もある（戻り値を文字列化する方式では、配列を期待している既存テストを大量に書き換える必要が生じる）。

### 3.2 変更対象

| ファイル | 変更方針 |
|---|---|
| `src/Mcp/McpServer.php` | `PhpMcp\Server\ServerBuilder` → `Mcp\Server\McpServer` へ。ロガーはコンストラクタ第2引数で渡す（`__construct(string $name, ?LoggerInterface $logger = null, string $version = '1.0.0')`）。`withCapabilities()` の明示指定は廃止（SDK が登録実態から算出するため、`resources` / `prompts` の虚偽申告が解消される）。`runSse()` を削除 |
| `src/Mcp/McpRequestHandler.php`（新規） | `HttpServerRunner` を組み立ててプロセス内で1リクエストを処理する。本番とテストで共有する唯一の実行経路 |
| `src/Mcp/McpContext.php`（新規） | リクエストスコープのログインユーザー ID を保持する |
| `src/Mcp/BaseMcpTool.php` | `addToolsToBuilder(ServerBuilder $builder)` → `registerTools(McpServer $server)` へシグネチャ変更。`resolveLoginUserId()` を追加。ファイルアップロード・画像処理などの共通処理は無変更 |
| `src/Mcp/BaserCore/*Tool.php`<br>`src/Mcp/BcBlog/*Tool.php`<br>`src/Mcp/BcCustomContent/*Tool.php` | `->withTool(handler:, name:, description:, inputSchema:)` → `->tool(name:, description:, callback:, inputSchema:)` へ置換。`inputSchema` の中身とビジネスロジックは無変更 |
| `src/Mcp/*/*Server.php`（`getToolClasses()`） | 変更なし |
| `src/Controller/McpProxyController.php` | 内部 HTTP 転送を廃止し `McpRequestHandler` を呼ぶ。応答の偽装を削除。`Origin` 検証とロギングを追加 |
| `src/Command/McpServerCommand.php` | stdio モードのみ残す。`--transport=sse` / `http` と `--host` / `--port` オプションを削除 |
| `src/Mcp/McpServerManger.php` | **削除** |
| `src/Mcp/McpLogger.php` | PSR-3 実装（`Psr\Log\AbstractLogger` 継承）なのでそのまま流用 |
| `templates/Admin/McpServerManager/*` | 起動/停止 UI を廃止。画面の残し方は第10章 |

### 3.3 プロセス内実行の設計

SDK の HTTP トランスポートは「1リクエストを処理して終わる」モデルであり、必要な部品はすべて public API として公開されている。

- `Mcp\Server\McpServer::getServer(): Mcp\Server\Server`
- `Mcp\Server\Server::createInitializationOptions(?NotificationOptions, ?array): InitializationOptions`
- `new Mcp\Server\HttpServerRunner(Server, InitializationOptions, array $httpOptions, ?LoggerInterface, ?SessionStoreInterface, ?HttpIoInterface)`
- `HttpServerRunner::handleRequest(?HttpMessage $request = null): HttpMessage`
- `new Mcp\Server\Transport\Http\HttpMessage(?string $body)` に `setMethod()` / `setUri()` / `setHeader()`
- `Mcp\Server\Transport\Http\BufferedIo` — 出力を SAPI へ書き出さずバッファに捕捉する `HttpIoInterface` 実装

`McpRequestHandler` はこれらを組み立て、**リクエストを渡してレスポンスを受け取る純粋な処理**として実装する。SAPI へ直接出力しないため、CakePHP のレスポンスに載せられ、テストからも同じ経路を呼べる。

### 3.4 Legacy セッションの保持

Modern リクエストは self-contained なのでセッション状態を必要としない。Legacy 世代はセッションを要するため、`sessionStore(SessionStoreInterface $store)` に同梱の `FileSessionStore` を渡し、保存先を baserCMS の一時ディレクトリ配下（`TMP . 'bc_mcp_sessions'`）とする。

なお移植前は `stateless: true` でセッションを作らない設定で運用しており、その状態で Claude の Legacy クライアントが正常に動作していた（実測済み）。したがってセッションは実質使われていないが、仕様準拠のため用意する。将来 CakePHP の Cache ベース実装に差し替える余地を残す。

## 4. プロキシの責務整理

### 4.1 リクエストの変換

内部 HTTP 転送が消えるため、外部から受けたリクエストを `HttpMessage` に変換して `McpRequestHandler` へ渡す。

Modern の必須ヘッダ（`MCP-Protocol-Version` / `Mcp-Method` / `Mcp-Name` / `Mcp-Param-*`）は、SDK がヘッダとボディの一致を検証する。**外部クライアントが送ってきたヘッダをそのまま `HttpMessage` に載せ、ボディも改変しない**ことで整合性を保つ。`Authorization` は SDK へ渡さない（認証はプロキシで完結している）。

### 4.2 プロトコル応答の偽装の削除

`initialize` 応答の `protocolVersion` を `2025-06-18` に書き換え、capabilities を `resources` / `prompts` ともに `listChanged: true` と申告している処理を削除する。SDK が Dual-era を正しく処理し、実態どおりの capabilities を返す。

GET に対してダミー JSON を 200 で返している処理も削除する。Modern では GET / DELETE は `405 Method Not Allowed` が期待される挙動である。

### 4.3 通知の 202 応答

現状 `$this->request->getData('method')` で通知を判定しているが JSON ボディを読めておらず機能していない。SDK が通知に対して 202 を返すため、**プロキシは SDK が返したステータスコードをそのまま CakePHP のレスポンスに反映する**だけでよい。独自の判定を持たない。

## 5. `loginUserId` の伝達

**方式を変更する。** 移植前はプロキシが OAuth トークンから解決した user_id を `params.arguments.loginUserId` に上書き注入していた。

```php
$mcpRequest['params']['arguments']['loginUserId'] = $this->request->getAttribute('oauth_user_id');
```

in-process 化により、この注入は不要かつ有害になる。

- **有害な理由** — Modern ではヘッダとボディの一致が検証される。ボディを改変しない方が整合性の担保が単純で確実になる。また `inputSchema` に無い余剰プロパティがコールバック引数へマップされるかは SDK 実装依存であり、そこに賭ける理由がない
- **不要な理由** — 同一プロセス内で処理するため、CakePHP のリクエストコンテキストからツールが直接取得できる

### 採用する方式

`BcMcp\Mcp\McpContext` にリクエストスコープの user_id を保持し、`BaseMcpTool::resolveLoginUserId()` 経由で取得する。

```php
// McpProxyController（認証後）
McpContext::setLoginUserId((int)$this->request->getAttribute('oauth_user_id'));

// 各ツール（引数の $loginUserId は互換のため残す）
$userId = $this->resolveLoginUserId($loginUserId);
```

各ツールの `?int $loginUserId = null` 引数は残し、`resolveLoginUserId()` が「引数が渡されていればそれを使い、無ければコンテキストから取る」形にする。これにより既存のツール実装への変更を最小に留めつつ、ボディ改変をやめられる。

**`loginUserId` を `inputSchema` に公開してはならない。** AI クライアントに他ユーザーの ID を指定する余地を与えることになる。`McpContext` は必ずプロキシの認証後に設定し、リクエストの終わりにクリアする。

## 6. 独立項目

### 6.1 Origin ヘッダ検証

Streamable HTTP の MUST 要件（`2025-03-26` 以来）であり、DNS リバインディング攻撃対策として現状未実装。

プロキシで検証し、`Origin` が存在して許可リストに無い場合は **403 Forbidden** を返す。検証は**認証より前**に行う（transport レベルの要件であり、認証前に効かせるべきもの）。

許可オリジンは `config/setting.php` に設定項目（`BcMcp.allowedOrigins`）を追加する。既定は自サイトのオリジンのみを許可し、`Origin` ヘッダ自体が無いリクエスト（サーバー間通信）は通す。

なお SDK も `httpOptions(['allowed_origins' => [...]])` による同等の保護を持つため、同じ許可リストを SDK にも渡して二重に効かせる。

### 6.2 `iss` パラメータ（RFC 9207）

Modern のクライアントは、認可レスポンスに `iss` があれば検証が MUST。認可サーバー側の付与は SHOULD だが、付与しておく方が安全側に立てる。`league/oauth2-server` 8.5 / 9系ともに機能を持たないため自前で実装する。

- `Admin/Oauth2Controller::authorize()` の approve / deny 双方のリダイレクト URL に `iss`（= issuer 識別子 `{baseUrl}/bc-mcp`）を付与する
- `Oauth2Controller::authorizationServerMetadata()` に `authorization_response_iss_parameter_supported: true` を追加する

issuer 識別子は既存のメタデータの `issuer` と**同一の値**でなければならない。両者を `OAuth2Util::getIssuer()` という同じ導出処理から得る。

### 6.3 ネゴシエーションのロギング

現状 `logs/mcp.log` にはリクエスト URL と POST ボディだけが記録され、MCP のネゴシエーション内容が残らない。**「Claude がいつ Modern に切り替えたか」を検知する手段が存在しない**ため、これを作る。

プロキシで次を `mcp` スコープのログに記録する。

- 世代（Modern / Legacy）の判定結果
- プロトコルバージョン（`MCP-Protocol-Version` ヘッダ、または `_meta` / `params.protocolVersion`）
- クライアント情報（`_meta` の `io.modelcontextprotocol/clientInfo`、または `initialize` の `clientInfo`）
- メソッド名

トークンや引数の中身は記録しない（機密情報の混入を避ける）。

## 7. テスト戦略

`plugins/bc-mcp/tests/` に配置し、ローカル Docker（`basercms` コンテナ）で実行する。本番と同じ `McpRequestHandler` を経由するため、テストが実装の実経路を検証する。

| テスト | 内容 |
|---|---|
| `McpTestTraitTest` | プロセス内実行ヘルパ自身の疎通（`tools/list`） |
| `McpServerTest` | 全ツールが `tools/list` に並ぶ。`ttlMs` / `cacheScope` / `resultType` が付与される |
| `McpServerToolCallTest`（既存・書き換え） | `addBlogPost` が通り、`user_id` にログインユーザーが反映される（第5章の方式の検証を兼ねる） |
| `DualEraTest` | `server/discover`（Modern）と `initialize`（Legacy）が同一サーバーで応答する。`-32022`（未対応バージョン）と `-32020`（HeaderMismatch）が返る |
| `McpProxyControllerTest` | リクエスト変換、SDK のステータスコードの反映、GET / DELETE で 405、`Origin` 不正で 403 |
| `OAuth2UtilTest` | `iss` の付与と issuer の一致 |
| `NegotiationLoggerTest` | 世代判定とログ内容（引数が記録されないこと） |

既存の `BaseMcpToolTest` / `OAuth2ControllerTest` / `OAuth2ControllerDynamicClientRegistrationTest` / `McpServerCommandTest` が回帰なく通ることも確認する。

## 8. 依存関係の切り替え

**完了済み（2026-08-12）**

1. `plugins/bc-mcp/composer.json` の `php-mcp/server: ^3.3` を `logiscape/mcp-sdk-php: ^2.0` に差し替えた
2. `monorepo-builder merge` は**既存の**バージョン不一致（`nyholm/psr7` の `^1.8` vs `~1.8.2`、`symfony/psr-http-message-bridge` の `^2.3` vs `~2.3.1`、`psr/http-message` の `^1.0` vs `~1.1`）で失敗するため、ルート `composer.json` を直接編集した。**この不一致は移植前から存在するもので、本件とは無関係**
3. `composer update` により `logiscape/mcp-sdk-php v2.0.0` が導入され、`php-mcp/server` と依存17パッケージ（`react/*` 6件、`opis/*` 3件、`phpdocumentor/*` 3件、`evenement` / `fig/http-message-util` / `webmozart/assert` ほか）が削除された

`nyholm/psr7` は OAuth2 側（`OAuth2Service` / `Lib/OAuth2Util` / `Controller/Oauth2Controller`）が PSR-7 の生成に使用しているため残す。`ext-openssl` も OAuth2 の鍵処理で使用しているため残す。`symfony/psr-http-message-bridge` は直接参照されていないが、依存整理は本スコープ外として据え置く。

PHP 要件は問題ない。SDK は `php: >=8.1` / `ext-curl` / `ext-json` / `psr/log` のみを要求し、baserCMS 側（ルート `>=8.1`、`config.platform.php: 8.1`）と一致する。`ext-pcntl` と `monolog/monolog` は `suggest` 扱いで必須ではない。

## 9. 実装の順序

1. **依存関係の切り替え**（第8章）— 完了
2. **プロセス内実行の基盤** — `McpRequestHandler` / `McpContext` とテストヘルパ
3. **ツール登録の移植** — `McpServer` → `BaseMcpTool` → 各 `*Tool`。`inputSchema` は流用
4. **既存テストの移植** — `McpServerToolCallTest` を通す
5. **プロキシの移植**（第4・5章）— 内部 HTTP 転送の廃止、偽装削除、`McpContext` の設定
6. **常駐プロセス関連の削除** — `McpServerManger`、コマンドの HTTP モード、管理画面 UI
7. **Dual-era 疎通テストの追加**（第7章）
8. **独立項目**（第6章）— `Origin` 検証 → `iss` → ロギング
9. **全体テストの実行** — `plugins/bc-mcp` 単体 → フルスイートで回帰確認

## 10. 管理画面の再構成

常駐プロセスが無くなるため、管理画面「MCPサーバー管理」の起動・停止・再起動ボタンと死活表示は意味を失う。**情報表示画面として再構成する。**

現状の4ブロックの扱い。

| 現状のブロック | 扱い |
|---|---|
| MCPサーバー状態（稼働中/停止中・PID・内部URL・設定用URL） | 死活表示・PID・内部URL を削除し、接続情報のブロックへ再構成 |
| サーバー操作（起動/停止/再起動ボタン） | 削除 |
| AIエージェントでの設定方法（手順1〜3） | 手順1「起動ボタンで起動してください」を削除し2手順にする |
| 利用可能な機能（手書き3行） | 登録済みツールからの自動生成に置き換える。**現状の手書きは実態とずれており、40件以上あるツールが3行しか書かれていない** |

再構成後の4ブロック。

1. **接続情報** — MCP エンドポイント URL（コピーボタン付き）、`.well-known/oauth-authorization-server` と `.well-known/oauth-protected-resource` の URL、対応プロトコルバージョン（Modern `2026-07-28` と旧世代の両対応であること）
2. **利用可能なツール** — 登録済みツールの名前と説明をプラグイン単位でグループ化して一覧表示。ツールを追加すれば自動で反映される
3. **AIエージェントでの設定方法** — URL 登録から始まる2手順
4. **直近の接続状況** — `NegotiationLogger::readRecent()` から日時・世代・プロトコルバージョン・クライアント名を表示。**Claude が Modern に切り替わったことを管理画面から気づけるようにする。常駐プロセスの死活監視を失う代わりに、これが運用時の主要な確認手段になる**

`configure` アクションとテンプレート（ポート番号などの設定画面）は削除する。

## 11. 方針変更の経緯（2026-08-12）

当初は「常駐プロセス方式を維持し、その中身の SDK だけを入れ替える。in-process 化は次のブランチ」という計画だった。SDK 導入後に実物のソースを確認した結果、**この計画は技術的に成立しないことが判明したため方針を変更した**。

**判明した事実**

- `logiscape/mcp-sdk-php` v2 の `runHttp()` は `StandardPhpAdapter::handle()` を呼ぶだけで、内容は `HttpMessage::fromGlobals()` により**現在の HTTP リクエストを1件処理して終了する**もの。ポートを bind して待ち受ける機能はない
- `php-mcp/server` は ReactPHP のイベントループで listen していた（今回削除された `react/*` 6パッケージがその実体）。logiscape v2 は「PHP/Apache/cPanel のような 1リクエスト 1プロセス環境」を前提に設計されており、listen 型サーバーを提供しない
- ソース全体に `stream_socket_server` 相当は存在せず、ReactPHP への参照はクライアント側の OAuth コールバック受信のみ

**常駐であることに意味がなかったことの確認**

移植前の `runSse()` は `enableJsonResponse: true` かつ `stateless: true` でトランスポートを起動していた。すなわち移植前から既に、セッションを持たず・SSE ストリームも使わず・「1 POST → 1 JSON 応答」で完結する使い方をしていた。プロキシも毎回 `POST http://127.0.0.1:{port}/` に JSON を投げるだけで、GET の SSE ストリームは使っていない（ダミー JSON を返していた）。

この構成で claude.ai の Legacy クライアントが正常に動作していた実測もあり、**セッションも常駐も実質的に使われていなかった**ことが確認できた。`php-mcp/server` が listen 型トランスポートしか提供していなかったため、それに合わせて常駐化されていたにすぎない。

したがって in-process 化は機能を落とさず、第2章の表に挙げた運用上の問題を解消する。

## 12. 固定ページ（Pages）ツールの追加

bc-mcp には現在、ブログ・カスタムコンテンツ・検索インデックス・ファイルアップロードのツールはあるが、**固定ページを操作するツールが無い**。SDK 移植の完了後に追加する（新 SDK の作法で最初から書けるため、移植との二重作業を避ける）。

### 12.1 データ構造の注意点

固定ページは `pages` テーブルと `contents` テーブルの複合構造であり、**名前が紛らわしい**点に注意する。

| 保存先 | 意味 |
|---|---|
| `pages.contents` | **ページ本文**（HTML） |
| `pages.content`（`Contents` アソシエーション） | **コンテンツ情報**（タイトル・URL・公開状態・親フォルダ） |

`PagesService::create()` に渡す構造は次のとおり（`baser-core` の `PagesControllerTest::testAdd()` で確認済み）。

```php
[
    'contents' => '<p>本文</p>',
    'page_template' => '',
    'content' => [
        'title' => 'ページタイトル',
        'name' => 'about',
        'parent_id' => 1,
        'site_id' => 1,
        'plugin' => 'BaserCore',
        'type' => 'Page',
        'self_status' => true,
    ],
]
```

`plugin` は `'BaserCore'`、`type` は `'Page'` の固定値であり、ツール側で自動的に補う（AI クライアントに指定させない）。

### 12.2 提供するツール

`src/Mcp/BaserCore/PagesTool.php` を新規作成し、`BaserCoreServer::getToolClasses()` に登録する。

| ツール | 対応するサービス | 権限チェック用 URL |
|---|---|---|
| `getPages` | `PagesService::getIndex()` | `GET /baser-core/pages/index.json` |
| `getPage` | `PagesService::get()` | `GET /baser-core/pages/view/{id}.json` |
| `addPage` | `PagesService::create()` | `POST /baser-core/pages/add.json` |
| `editPage` | `PagesService::update()` | `POST /baser-core/pages/edit/{id}.json` |
| `deletePage` | `PagesService::delete()` | `POST /baser-core/pages/delete/{id}.json` |

### 12.3 引数設計

既存ツールと同様に、AI クライアントが扱いやすいフラットな引数にし、内部で上記の入れ子構造へ組み立てる。

`addPage` の引数。

| 引数 | 対応先 | 説明 |
|---|---|---|
| `title`（必須） | `content.title` | ページタイトル |
| `content` | `pages.contents` | ページ本文（HTML） |
| `name` | `content.name` | URL のスラッグ（省略時は baserCMS が自動採番） |
| `parentId` | `content.parent_id` | 親フォルダのコンテンツID（省略時はサイトルート） |
| `siteId` | `content.site_id` | サイトID（省略時は 1） |
| `status` | `content.self_status` | 公開状態（0: 非公開, 1: 公開。省略時は 0） |
| `description` | `content.description` | 説明 |
| `publishBegin` / `publishEnd` | `content.publish_begin` / `publish_end` | 公開期間 |
| `pageTemplate` | `pages.page_template` | ページテンプレート |
| `eyeCatch` | `content.eyecatch` | アイキャッチ画像（外部画像 URL を直接指定） |

引数名の `content`（本文）と保存先の `content`（コンテンツ情報）が紛らわしいため、**実装時のコメントで対応関係を明示する**。

`editPage` は `id`（必須）＋上記の任意項目。`deletePage` は `id` のみ。`getPages` は `keyword` / `siteId` / `status` / `limit` / `page`。`getPage` は `id`。

**`loginUserId` は `inputSchema` に公開せず、`McpContext` から取得する**（他ツールと同じ方針）。

## 13. 完了条件

- Modern（`2026-07-28`）と Legacy の両世代で `tools/list` → `tools/call` が通ることが自動テストで検証されている
- 常駐プロセスを起動しなくても `/bc-mcp` が応答する
- 既存の bc-mcp テストがすべて通り、フルスイートに回帰がない
- `logs/mcp.log` から接続クライアントの世代とプロトコルバージョンが判別できる
- 許可外 `Origin` からのリクエストが 403 で拒否される
- 認可レスポンスに `iss` が含まれ、メタデータの `issuer` と一致する
- `vendor/php-mcp` への依存が残っていない
- `McpServerManger` と管理画面の起動/停止 UI が削除されている
- 固定ページの取得・作成・編集・削除がツールとして提供され、`tools/list` に並んでいる
