# bc-mcp のスコープと方針

作成日: 2026-08-17

## 1. 背景と目的

bc-mcp は MCP 2026-07-28 対応（[SDK 移植設計書](2026-08-12-bc-mcp-sdk-migration-design.md)）を完了したが、**プラグインとして何を提供し何を提供しないか**が文書化されていなかった。そのため機能追加の可否を判断する軸がなく、次の状態が生じていた。

- `search` / `fetch`（`SearchIndexesTool`）が無効化されたまま放置されている
- `sendFileChunk`（`FileUploadTool`）が無効化される一方、受け取り側の `processChunkFile()` だけが残っている
- 認証と権限を通らない stdio 経路が、HTTP 経路と同じツール群を公開している
- ツールが読み取り専用か破壊的かをクライアントへ伝える手段を持たない

本書はスコープを定義し、上記を解消する。実装計画は別途作成する。

## 2. 位置づけと原則

**bc-mcp は、baserCMS を運営する人のための、認証付き MCP サーバーである。**

### 原則1: 客層は運営者に限定する

一般ユーザー（サイト訪問者）向けの機能は扱わない。運営者向けと一般ユーザー向けでは、必要な認証も、見せてよいデータも、権限の考え方も異なる。1つのエンドポイントに混在させると、運営用ツールが公開側へ漏れる事故を構造的に防げなくなる。

判断基準は「この機能は、ログインした運営者が権限の範囲で行う操作か？」である。

### 原則2: 権限は Admin Web API に委ねる

独自の権限体系は作らない。各ツールは対応する Admin Web API の URL を `getPermissionUrl()` で宣言し、baserCMS のアクセスルールで制御する。**管理画面でできないことは MCP でもできない**という対応関係を保つ。

この原則から、**認証と権限チェックを通らない経路を設けない**ことが導かれる。

### 原則3: 特定クライアントの都合に合わせない

単一ベンダーが要求するツール名・レスポンス形式には追従しない。MCP 標準の範囲で表現し、解釈はクライアントに委ねる。

ただし**標準化された拡張には追従する**。複数ベンダーが参加する仕様（ツール注釈、SEP-2631 の `x-mcp-file` など）は対象とする。

### 原則4: 機能追加は要望ベース

Admin Web API には40以上のリソースがあるが、網羅を目的にしない。現在の11リソース（固定ページ・ブログ・カスタムコンテンツ）を起点に、実際の要望に応じて広げる。

## 3. ツール構成の変更

### 3.1 削除するもの

| 対象 | 根拠 |
|---|---|
| `SearchIndexesTool`（`search` / `fetch`）とテスト | 原則1（客層が違う）、原則3（単一ベンダー形式） |
| `FileUploadTool`（`sendFileChunk`）とテスト | チャンク方式は標準化の議論でも見送られた。ホストが未対応（5.2 参照） |
| `BaseMcpTool::processChunkFile()` | 上記の受け取り側。送る手段が消えるため対で削除 |
| `BaserCoreServer` のコメントアウト行 | 死んだ選択肢を残さない |

**公開ツール数は51のまま変わらない。** これらは元々 `BaserCoreServer::getToolClasses()` に登録されていないため、消えるのは「無効なのに残っているコード」である。

### 3.2 stdio トランスポートの削除

`PermissionManager` を呼ぶのは `McpProxyController` のみで、**HTTP 経路にしか権限チェックが存在しない**。`bin/cake bc_mcp.server` は次を全て素通りする。

- OAuth 認証
- 権限チェック
- Origin 検証
- ログインユーザーの設定（`McpContext` が空のまま実行される）

シェルに触れる者が、誰の権限でもない状態で全ツールを実行できる経路であり、原則2 と矛盾する。SDK 移植設計書では「ローカルの stdio クライアント用途として残す」としていたが、本書で判断を覆す。

削除対象:

| ファイル | 内容 |
|---|---|
| `src/Command/McpServerCommand.php` | 全体削除 |
| `tests/TestCase/Command/McpServerCommandTest.php` | 全体削除 |
| `src/BcMcpPlugin.php` の `console()` | `bc_mcp.server` の登録を削除。`Oauth2CleanupCommand` は自動探索されるため `console()` 自体が不要になる |
| `src/Mcp/McpServer.php::runStdio()` | 削除 |
| `src/Mcp/McpServer.php` の `available_transports` | `['stdio', 'http']` → `['http']` |
| `src/Mcp/BaseMcpTool.php` の stdio に言及するコメント | 実態に合わせる |

管理画面の「MCPサーバー管理」は `McpRequestHandler` をプロセス内で呼んでツール一覧を取得しているため、影響を受けない。

### 3.3 ファイルアップロードの整理

`BaseMcpTool::processFileUpload()` は3分岐である。

```php
if (strpos($fileData, 'data:') === 0)        → processBase64File()  // インライン
if (preg_match('/^https?:\/\//', $fileData)) → processUrlFile()     // URL
else                                          → processChunkFile()  // チャンク
```

`data:` URI は SEP-2631 が `transferModes` の一つとして認める inline 方式そのものであり、標準の方向と一致するため残す。削除するのはチャンク分岐のみで、結果は **URL と `data:` URI の2方式**となる。

将来 SEP-2631 が確定した際は、この2方式に `x-mcp-file` の宣言を被せる形で移行できるため、手戻りにならない。

### 3.4 揃えるもの

`CustomEntriesTool` に `keyword` を追加する。`PagesTool` と `BlogPostsTool` には存在し、ここだけ欠けている。横断検索を作らない判断（5.1）の前提として、各ツールの絞り込みが揃っている必要がある。

## 4. ツール注釈

クライアントが読み取り専用ツールと破壊的ツールを区別できるよう、全ツールに MCP のツール注釈を宣言する。Claude の Research ではツール呼び出しに都度承認が入らないため、区別できる情報を提供する意味がある。

SDK の `ToolAnnotations` は `title` / `readOnlyHint` / `destructiveHint` / `idempotentHint` / `openWorldHint` を持つ。

| 接頭辞 | readOnly | destructive | idempotent | openWorld | 根拠 |
|---|---|---|---|---|---|
| `get*` / `serverInfo` | true | — | — | false | 読み取りのみ。他のヒントは意味を持たない |
| `add*` | false | false | false | false | 追加のみ。繰り返すと重複が増えるため冪等でない |
| `edit*` | false | true | true | false | 既存データを上書きする。同じ引数なら同じ結果 |
| `delete*` | false | true | true | false | 破壊的。削除済みを再度消しても結果は同じ |

`openWorldHint` は全て `false` とする。操作対象が自サイトのデータに閉じているため。

実装は `BaseMcpTool` に定数として持たせ、各 `tool()` 呼び出しで明示指定する。

```php
protected const ANNOTATION_READ   = ['readOnlyHint' => true,  'openWorldHint' => false];
protected const ANNOTATION_CREATE = ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => false, 'openWorldHint' => false];
protected const ANNOTATION_UPDATE = ['readOnlyHint' => false, 'destructiveHint' => true,  'idempotentHint' => true,  'openWorldHint' => false];
protected const ANNOTATION_DELETE = ['readOnlyHint' => false, 'destructiveHint' => true,  'idempotentHint' => true,  'openWorldHint' => false];
```

ツール名から自動判定はしない。命名規則から挙動を推測する仕組みは、規則を外れたツールが増えたときに静かに間違うため。代わりに全数走査のテスト（6.1）で付け忘れを検出する。

## 5. 将来構想

今回は実装しない。**何が揃ったら再検討するか**を記録する。

### 5.1 一般ユーザー向け公開エンドポイント

**着手条件**: 発見規約の確定。

MCP 2026-07-28 のステートレス化により、公開コンテンツサイトが未認証の MCP エンドポイントを出すことが現実的になった。仕様も公開層と保護層の2モデル併存を前提としており、認証は任意である（管理操作には強く推奨）。

一方で、**サイトの公開エンドポイントをエージェントが自動発見する規約は複数案が並走中**である。

| 提案 | 状態（2026-08 時点） |
|---|---|
| SEP-2127（`.well-known/mcp.json` の Server Cards） | PR |
| Issue #1960（`.well-known/mcp`） | 提案 |
| IETF `draft-serra-mcp-discovery-uri` | Draft 04 |

現時点で公開エンドポイントを出しても発見される手段がない。ChatGPT Apps のような消費者向け MCP は既に大規模に稼働しているが、それはアプリのディレクトリ経由であり、エージェントが任意のサイトを巡回しているわけではない。

なお **2026-07-28 対応で土台は既にある**。ステートレス化とキャッシュヒント（`ttlMs` / `cacheScope`）は SDK 経由で利用できる。着手時に作るのは認証なし経路と公開データに限定したツール群のみとなる。

CMS 同業（WordPress の MCP プラグイン群）も運営者向けに寄せており、公開エンドポイントを置かないことを利点として掲げる製品もある。

### 5.2 ファイルアップロード

**着手条件**: SEP-2631 の確定 **かつ** ホスト（Claude / ChatGPT）のファイルピッカー対応。

根本のボトルネックは MCP でも SDK でもなく、**ファイルの生バイトをサーバーへ渡す手段をホストが持っていない**ことである。claude.ai に添付した画像はモデルへの視覚入力であり、モデルが同じバイト列を再出力できるわけではない。ローカルファイルを読める環境でも、100KB の画像で数万トークンとなり現実的でない。

MCP 公式に File Uploads Working Group が 2026-04-23 に発足しており（リード: Anthropic、メンバーに OpenAI）、憲章はこの穴を明示している。

> Today, servers that need a file from the user resort to prose instructions asking for base64 strings or local paths, which produces inconsistent UX and pushes encoding details onto end users.

標準化の現在地:

| SEP | 内容 | 状態 |
|---|---|---|
| SEP-2356 | 宣言的ファイル入力 | クローズ |
| **SEP-2631** | File Objects and Transfer | オープン（draft） |
| SEP-2532 | Resource Streaming（サーバー→クライアント） | オープン |

SEP-2631 は `x-mcp-file` による宣言、`files/authorizeUpload` による帯域外転送、`transferModes` による方式指定の3本柱で、**チャンク分割は仕様から外されている**（完了マーキングと GC の仕組みが別途必要なため）。

`FileUploadTool` の30KBチャンク方式が破綻したのは実装の問題ではなく、迂回しようとした壁がホスト側にあったためである。

### 5.3 対象範囲の拡張

**着手条件**: 実際の要望。

Admin Web API のうち bc-mcp がカバーするのは11リソース。候補として `Contents` / `ContentFolders`（サイト構造・フォルダ・ゴミ箱）が最有力である。baserCMS がコンテンツをツリーで管理する以上、固定ページを作れてもサイト構造を操作できない点は空白として認識しておく。

## 6. テストとドキュメント

### 6.1 テスト

| 対象 | 内容 |
|---|---|
| 削除に伴う調整 | `SearchIndexesToolTest` / `FileUploadToolTest` / `McpServerCommandTest` を削除。`BlogPostsToolTest` の `mcp_uploads` を参照する箇所を URL / `data:` URI 方式へ置き換え |
| 注釈 | `tools/list` の応答に `annotations` が載ることを検証する。**種別ごとに1件ではなく全ツールを走査**し、接頭辞と注釈の対応が崩れていないことを確認する |
| `keyword` | `CustomEntriesTool` の絞り込みを検証 |

注釈のテストを全数走査にするのは、ツール追加時の付け忘れを検出するためである。明示指定を選んだ以上、抜けを検出する仕組みが要る。

### 6.2 ドキュメント

- **README**: アップロードの節を実態（URL と `data:` URI の2方式）に合わせる。Cloudflare Tunnel の節に Quick Tunnel の制約（同時200リクエスト、SSE 非対応、本番非推奨、URL が毎回変わる）を追記し、固定ホスト名が必要な場合の Named Tunnel（Cloudflare アカウントと所有ドメインが必要）を併記する
- **設計書**: 本書

## 7. スコープ外

以下は扱わない。要望があれば本書を改訂して判断する。

- 一般ユーザー（サイト訪問者）向けの機能
- 横断検索（各一覧ツールの `keyword` で代替する）
- チャンク分割によるファイルアップロード
- MCP 独自の権限体系
- Admin Web API の網羅
- **HTTP 以外のトランスポート** — 認証と権限を通らない経路は設けない（原則2）

## 付録: 出典

- [Understanding Authorization in MCP](https://modelcontextprotocol.io/docs/2026-07-28/tutorials/security/authorization) — 認証は任意だが管理操作には強く推奨
- [MCP 2026-07-28: the spec catches up with the static web](https://joost.blog/mcp-goes-stateless/) — ステートレス化と公開コンテンツ配信
- [File Uploads Charter](https://modelcontextprotocol.io/community/working-groups/file-uploads) — WG の憲章と問題設定
- [SEP-2631: File Objects and Transfer](https://github.com/modelcontextprotocol/modelcontextprotocol/pull/2631)
- [SEP-2127: MCP Server Cards — HTTP Server Discovery via .well-known](https://github.com/modelcontextprotocol/modelcontextprotocol/pull/2127)
- [The "mcp" URI Scheme and MCP Server Discovery Mechanism](https://datatracker.ietf.org/doc/draft-serra-mcp-discovery-uri/04/)
- [Building MCP servers for ChatGPT and API deep research](https://developers.openai.com/api/docs/mcp) — `search` / `fetch` の要求形式
- [Get started with custom connectors using remote MCP](https://support.claude.com/en/articles/11175166-get-started-with-custom-connectors-using-remote-mcp) — Research はツール呼び出しに承認を挟まない
- [TryCloudflare](https://developers.cloudflare.com/cloudflare-one/networks/connectors/cloudflare-tunnel/do-more-with-tunnels/trycloudflare/) — Quick Tunnel の制約
