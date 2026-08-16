# BcMcp plugin for baserCMS

baserCMS用のMCP（Model Context Protocol）サーバープラグインです。  
外部のAIツールやアプリケーションからbaserCMSのデータを操作することができます。

## 機能

- 固定ページの作成、取得、編集、削除
- ブログ関連データの作成、取得、編集、削除
- カスタムコンテンツ関連データの作成、取得、編集、削除
- サーバー情報の取得
- HTTP トランスポートサポート

## 動作要件
PHP 8.1 以降
baserCMS 5.1.10 以降

## インストール

### Composerを使用したインストール

```bash
composer require ecatchup/bc-mcp --with-all-dependencies
```

### 手動インストール

1. [baserマーケット](https://market.basercms.net) からダウンロード
2. `plugins/` ディレクトリ配下に配置

※ baserマーケット配布版は、依存しているパッケージを梱包いていますので、コマンドの実行が不要です。

## 設定
### configフォルダの権限設定
ルート直下の `config` フォルダと `.env` に書き込み権限が必要です。

```bash
chmod 777 config
chmod 666 config/.env
```

### プラグインの有効化
baserCMSの管理画面から BcMcp プラグインを有効化してください。

## MCPサーバーの起動
起動操作は不要です。MCPサーバーはbaserCMSのリクエスト内で動作するため、常駐プロセスを立てる必要はありません。

メニューの「MCPサーバー管理」では、接続用のURL・提供しているツールの一覧・直近の接続状況を確認できます。

## クライアント連携

### ChatGPT
ChatGPT Plus 以上の契約が必要です。  
※ 2025年9月19日現在、ChatGPT Business プランでは利用できません。

1. 「MCPサーバー管理」より、AIエージェント設定用URLをコピーします。
2. 「設定」→「コネクタ」→「高度な設定」→「開発者モード」をオン
3. 「コネクタ」に戻り、「作成する」から以下のように設定します。

- **名前**: 任意の名前
- **説明**: 任意の説明
- **MCPサーバーのURL**: AIエージェント設定用URL
- **認証**: OAuth
- わたしはこのアプリケーションを信頼しますにチェック

3. 「作成する」をクリック
4. 設置しているbaserCMSの画面に移動するので、「許可」をクリック

チャット画面にて、開発者モードをオンにして、作成したコネクタを選択します。

### Claude
Claude Pro 以上の契約が必要です。

1. 「MCPサーバー管理」より、AIエージェント設定用URLをコピーします。
2. 「設定」→「コネクタ」→「カスタムコネクタを追加」から以下のように設定します。

- **名前**: 任意の名前
- **リモートMCPサーバーURL**: AIエージェント設定用URL

3. 「連携/連携させる」をクリック
4. 設置しているbaserCMSの画面に移動するので、「許可」をクリック

### Visual Studio Code
` ~/Library/Application Support/Code/User/mcp.json`、または、プロジェクト内の `.vscode/mcp.json` に以下のように設定します。
```json
{
    "servers": {
        "ryuring": {
            "url": "AIエージェント設定用URL",
            "type": "http"
        }
    }
}
```

### その他のMCPクライアント

HTTPトランスポートをサポートする任意のMCPクライアントで使用できます。

## ローカル環境をHTTPSで公開して動作確認する

ClaudeなどのMCPクライアントは、**自己署名証明書のサーバーには接続できません**。
ローカル開発環境で実クライアントとの連携を確認するには、正式な証明書を持つHTTPSのURLが必要です。

ここでは [Cloudflare Tunnel](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/)
を使い、ローカル環境を一時的にインターネットへ公開する手順を示します。独自ドメインもCloudflareアカウントも不要です。

> **注意**: この手順を実行すると、管理画面を含むサイト全体が一時的にインターネットへ公開されます。
> 動作確認が終わったら必ずトンネルを停止してください。

### 1. cloudflaredのインストール

```bash
brew install cloudflared
```

### 2. トンネルの起動

ローカル環境が `https://localhost` で動作している場合：

```bash
cloudflared tunnel --url https://localhost --no-tls-verify
```

`--no-tls-verify` は、接続先（ローカル環境）が自己署名証明書のために必要です。

起動すると `https://<ランダムな文字列>.trycloudflare.com` というURLが発行されます。
このURLは**トンネルを再起動するたびに変わります**。確認が終わるまでトンネルは起動したままにしてください。

### 3. SITE_URLの変更

`config/.env` の `SITE_URL` を、発行されたURLに変更します。

```
export SITE_URL="https://<発行されたURL>/"
```

`SITE_URL` はアクセストークン（JWT）の発行者・対象者や、動的クライアント登録のレスポンスに使われるため、
公開URLと一致していないとクライアント側の検証に失敗します。

あわせて `TRUST_PROXY` が `true` であることを確認してください。トンネル経由のリクエストは
`X-Forwarded-Proto` でHTTPSを伝えるため、これが有効でないとHTTPと判定されます。

```
export TRUST_PROXY="true"
```

変更後はキャッシュをクリアします。

```bash
bin/cake cache clear_all
```

### 4. リバースプロキシを使っている場合

`nginx-proxy` などのリバースプロキシでホスト名ごとに振り分けている環境では、
発行されたURLのホスト名を振り分け対象に追加する必要があります。追加しないと、
プロキシが転送先を判断できず **503 Service Temporarily Unavailable** になります。

`docker-compose.yml` の該当サービスの `VIRTUAL_HOST` に追記し、そのコンテナを再作成します。

```yaml
- VIRTUAL_HOST=localhost,<発行されたURLのホスト名>
```

```bash
docker compose up -d --no-deps <サービス名>
```

`LETSENCRYPT_HOST` への追加は不要です。証明書はCloudflare側が用意します。

### 5. 接続の確認

```bash
# サイトが表示されるか
curl -o /dev/null -w "%{http_code}\n" https://<発行されたURL>/

# 認可サーバーのメタデータを取得し、issuerが公開URLになっているか
curl https://<発行されたURL>/.well-known/oauth-authorization-server/bc-mcp
```

`-k` を付けずに成功すれば、正式な証明書で接続できています。

### 6. クライアントへの登録

`https://<発行されたURL>/bc-mcp` を、各クライアントのMCPサーバーURLとして登録します。
登録手順は「クライアント連携」の各項目を参照してください。

### 7. 確認後の後片付け

1. 動作確認で作成したコンテンツを削除する
2. `config/.env` の `SITE_URL` を元に戻し、キャッシュをクリアする
3. `VIRTUAL_HOST` に追加したホスト名を削除し、コンテナを再作成する
4. `cloudflared` のプロセスを停止する

### MCP Inspectorでの確認

クライアントに登録する前に、[MCP Inspector](https://github.com/modelcontextprotocol/inspector)
で確認することもできます。ローカル環境（自己署名証明書）に対して直接実行できます。

```bash
NODE_TLS_REJECT_UNAUTHORIZED=0 npx -y @modelcontextprotocol/inspector
```

`NODE_TLS_REJECT_UNAUTHORIZED=0` は自己署名証明書を許可するための指定です。
起動後、表示されるURLをブラウザで開き、次を設定して接続します。

- **Transport Type**: `Streamable HTTP`
- **URL**: `https://localhost/bc-mcp`

CLIから直接実行することもできます。

```bash
NODE_TLS_REJECT_UNAUTHORIZED=0 \
  npx -y @modelcontextprotocol/inspector --cli https://localhost/bc-mcp \
  --transport http --method tools/list
```

## 利用可能なツール

最新の一覧は「MCPサーバー管理」画面で確認できます（実際に登録されているツールを表示するため、
常に実態と一致します）。

### 固定ページ関連

- `getPages`: 固定ページ一覧を取得
- `getPage`: 単一の固定ページを取得
- `addPage`: 固定ページを追加
- `editPage`: 固定ページを編集
- `deletePage`: 固定ページを削除

### ブログ関連

- `getBlogPosts` / `getBlogPost` / `addBlogPost` / `editBlogPost` / `deleteBlogPost`: ブログ記事
- `getBlogContents` / `getBlogContent` / `addBlogContent` / `editBlogContent` / `deleteBlogContent`: ブログ
- `getBlogCategories` / `getBlogCategory` / `addBlogCategory` / `editBlogCategory` / `deleteBlogCategory`: ブログカテゴリ
- `getBlogTags` / `getBlogTag` / `addBlogTag` / `editBlogTag` / `deleteBlogTag`: ブログタグ

### カスタムコンテンツ関連

- `getCustomContents` / `getCustomContent` / `addCustomContent` / `editCustomContent` / `deleteCustomContent`: カスタムコンテンツ
- `getCustomEntries` / `getCustomEntry` / `addCustomEntry` / `editCustomEntry` / `deleteCustomEntry`: カスタムエントリー
- `getCustomFields` / `getCustomField` / `addCustomField` / `editCustomField` / `deleteCustomField`: カスタムフィールド
- `getCustomTables` / `getCustomTable` / `addCustomTable` / `editCustomTable` / `deleteCustomTable`: カスタムテーブル
- `getCustomLinks` / `getCustomLink` / `addCustomLink` / `editCustomLink` / `deleteCustomLink`: カスタムリンク

### システム情報

- `serverInfo`: サーバー情報を取得

## 使用例

### ブログ記事の追加

```
「News」というブログにタイトル「AIの未来について」というタイトルで記事を作成して
```

### カスタムコンテンツ・カスタムエントリーの追加

```
カスタムコンテンツを使って、「家具紹介」のコンテンツを作って
「家具紹介」に「カジュアルデスク」というタイトルでエントリーを追加して
```

## 権限について
設定時、連携を許可する際にログインしたユーザーの権限として動作します。  
また、権限については、Admin Web APIの権限に準じます。  
システム管理グループのユーザーは特に気にする必要はありませんが、それ以外のグループのユーザーで利用する場合は、`管理画面 > ユーザー管理 > ユーザーグループ > 対象グループ > 編集` より、Admin Web API を有効化します。  
その上で、アクセスルールグループより、権限設定を調整してください。


## ファイルアップロードについて
ブログのアイキャッチなどのファイルについて、現在は、ローカルよりアップロードする事はできず、ネット上に公開されたURLからのみ送信可能です。
これは、現在の、HTTP方式のMCPサーバーの制約によるものです。

### 制約事項
- multipart/form-dataに対応しておらず、JSONで送信するため base64エンコード行う必要があり、生成AI側のメッセージ送信のトークン制限に引っかかってしまい処理が中断される
- 約30KB以下でチャンク分割送信を行うにしても送信回数が多くなりすぎ現実的ではない 

### 現状の対応方法
現状としてはSTDIO方式のアップロードツールで、BcMcpが参照可能な領域にアップロードして、そのURLを送信するしかありません。

### 将来的な対応予定
将来的には、MPCの仕様として multipart/form-data に対応する予定との事ですので、その際にBcMcpも対応する予定です。

## 技術的な仕組み

### プロセス内実行

BcMcpプラグインは以下の仕組みでクライアントと連携します：

1. **クライアント** → HTTPリクエスト → **baserCMS(/bc-mcp)**
2. **McpProxyController** → OAuth2認証・権限チェック・Origin検証
3. **McpRequestHandler** → 同一プロセス内でMCPサーバーを実行 → **各ツール** → baserCMS操作
4. **McpProxyController** → HTTPレスポンス → **クライアント**

常駐プロセスや内部へのHTTP転送は行いません。リクエストごとにMCPサーバーを組み立てるため、
ツールや設定の変更が即座に反映されます。

### 対応プロトコルバージョン

`2026-07-28`（ステートレスコア）と、それ以前の `initialize` 方式の世代の双方に対応しています。
プロトコルの世代判定・`server/discover`・必須ヘッダの検証などはSDKが担います。

## トラブルシューティング

### よくある問題

1. **クライアントから接続できない**
   - PHP 8.1以上がインストールされているか確認
   - Composerの依存関係がインストールされているか確認
   - **自己署名証明書のURLを登録していないか確認**（多くのクライアントは接続を拒否します。
     「ローカル環境をHTTPSで公開して動作確認する」を参照）
   - ログファイルにエラーメッセージがないか確認

2. **403が返る**
   - クライアントが送る `Origin` ヘッダが許可されていない可能性があります。
     設定の `BcMcp.allowedOrigins` を確認してください。空の場合は検証を行いません。

3. **ツールが正常に動作しない**
   - baserCMSのデータベースに接続できているか確認
   - 必要なプラグイン（BcBlog、BcCustomContent）が有効になっているか確認

4. **認可画面が表示されない**
    - baserCMSを古いバージョンからアップデートした場合、`/.htaccess` が正しく設定されていない可能性があります。次のように変更をお願いします。
```bash
# 変更前
RewriteRule ^(\.well-known/.*)$ $1 [L]
# 変更後
RewriteRule ^(\.well-known/.*)$ webroot/$1 [L]
```

### MCPサーバーのログの確認

```bash
# プロトコルのネゴシエーション状況を確認
tail -f logs/mcp.log

# MCPサーバー内部のエラーを確認
tail -f logs/bc_mcp_error.log
```

`logs/mcp.log` には、接続ごとのプロトコル世代・クライアント名・呼び出されたメソッドが記録されます。
「MCPサーバー管理」画面からも直近の内容を確認できます。

## 開発への貢献
[CONTRIBUTING.md](.github/CONTRIBUTING.md) をご覧ください。
