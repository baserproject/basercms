# BcMcp plugin for baserCMS

baserCMS用のMCP（Model Context Protocol）サーバープラグインです。  
外部のAIツールやアプリケーションからbaserCMSのデータを操作することができます。

## 機能

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
事前にメニューの「MCPサーバー管理」よりMCPサーバーを起動します。

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

## 利用可能なツール

### ブログ関連

- `getBlogPosts`: ブログ記事一覧を取得
- `getBlogPost`: 単一のブログ記事を取得
- `addBlogPost`: ブログ記事を追加
- `editBlogPost`: ブログ記事を編集
- `deleteBlogPost`: ブログ記事を削除

### カスタムコンテンツ関連

- `getCustomContents`: カスタムコンテンツ一覧を取得
- `getCustomContent`: 単一のカスタムコンテンツを取得
- `addCustomContent`: カスタムコンテンツを追加
- `editCustomContent`: カスタムコンテンツを編集
- `deleteCustomContent`: カスタムコンテンツを削除
- `getCustomContentEntries`: カスタムコンテンツのエントリー一覧を取得
- `getCustomContentEntry`: 単一のカスタムエントリーを取得
- `addCustomEntry`: カスタムエントリーを追加
- `editCustomEntry`: カスタムエントリーを編集
- `deleteCustomEntry`: カスタムエントリーを削除
- `getCustomFields`: カスタムフィールド情報を取得
- `getCustomField`: 単一のカスタムフィールド情報を取得
- `addCustomField`: カスタムフィールドを追加
- `editCustomField`: カスタムフィールドを編集
- `deleteCustomField`: カスタムフィールドを削除
- `getCustomTables`: カスタムテーブル情報を取得
- `getCustomTable`: 単一のカスタムテーブル情報を取得
- `addCustomTable`: カスタムテーブルを追加
- `editCustomTable`: カスタムテーブルを編集
- `deleteCustomTable`: カスタムテーブルを削除

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

### HTTPプロキシベースの接続

BcMcpプラグインは以下の仕組みでクラアントと連携します：

1. **クライアント** → HTTPリクエスト → **baserCMS(/bc-mcp)**
2. **MCPProxyController** → JSON-RPC変換 → **内部MCPサーバー**
3. **内部MCPサーバー** → baserCMS操作 → **レスポンス**
4. **MCPProxyController** → HTTPレスポンス → **クライアント**

## トラブルシューティング

### よくある問題

1. **MCPサーバーが起動しない**
   - PHP 8.1以上がインストールされているか確認
   - Composerの依存関係がインストールされているか確認
   - ログファイルにエラーメッセージがないか確認

2. **ツールが正常に動作しない**
   - baserCMSのデータベースに接続できているか確認
   - 必要なプラグイン（BcBlog、BcCustomContent）が有効になっているか確認

3. **認可画面が表示されない**
    - baserCMSを古いバージョンからアップデートした場合、`/.htaccess` が正しく設定されていない可能性があります。次のように変更をお願いします。
```bash
# 変更前
RewriteRule ^(\.well-known/.*)$ $1 [L]
# 変更後
RewriteRule ^(\.well-known/.*)$ webroot/$1 [L]
```

### MCPサーバーのログの確認

```bash
# MCPサーバーのログを確認
tail -f tmp/logs/mcp_server.log
```

## 開発への貢献
[CONTRIBUTING.md](.github/CONTRIBUTING.md) をご覧ください。
