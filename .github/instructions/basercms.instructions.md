# Copilot Instructions baserCMS
baserCMSの開発についての指示をまとめたものです。

## 概要・アーキテクチャ
- baserCMSは、PHP8 + CakePHP5ベースのCMS。`src/`はアプリ本体、`plugins/`は機能拡張（コア/追加プラグイン/テーマ/カスタムコンテンツ等）。
- サイト構造・コンテンツ管理は「Contents」「CustomContent」などのサービス/モデル/コントローラで分離。
- REST API（`/baser/api/`）で管理画面・外部連携を実現。APIテストはトークン取得→API呼び出しが基本。

## 開発・テスト・ビルド
- ユニットテストはDockerコンテナ内で実行。ルートは `/var/www/html`。
- テストは `phpunit`（`phpunit.xml.dist`参照）。プライベートメソッドは `BcTestCase::execPrivateMethod()` で呼び出し。
- プラグインが見つからない場合は `BcUtil::includePluginClass()` を利用。
- APIテストは `/baser/api/admin/baser-core/users/login.json` で認証→トークン取得→各API呼び出し。
- CI/CDはGitHub Actions（`test.yml`）で自動化。主要コマンドは `composer install`、`docker compose up`。
- **外部プロセス（MCPサーバー等）に依存するテストの方針**:
  - 必要なプロセスは**該当テスト側で起動**し、`setUp` 全体ではなく**それを要する個別テストの先頭**でガードする（他テストに起動待ちを波及させない）。
  - 起動判定は「プロセスの存在（pidファイル）」だけで済ませない。**プロキシ等が実際に接続する先（例 `127.0.0.1:{port}`）へ到達できるまで待つ**（`fsockopen` 等でポーリング、最大十数秒）。プロセス起動直後はポートの bind が間に合わず接続拒否＝500 になり、CI でのみ失敗する典型。
  - **到達できない場合は `markTestSkipped` で隠さず、`assertTrue` 等で明示的に失敗させる**。スキップはサーバー起動の不具合を握りつぶし CI を緑にしてしまうため不可。「外部プロセスが動いていること」も統合テストの検証対象とみなす。
  - 実装例: `plugins/bc-mcp/tests/TestCase/Controller/Admin/OAuth2ControllerTest.php` の `requireMcpServer()`。

## コーディング規約・パターン
- クラスに新メソッド追加時は「必ず最後」に追加。
- テストは `tests/`（本体）・`plugins/*/tests/`（プラグイン）配下。Fixture/Scenarioでデータ準備。
- プラグイン/テーマは `plugins/{PluginName}/` 配下。`src/`（ロジック）、`templates/`（ビュー）、`config/`（初期データ/マイグレーション/シード）などCakePHP標準＋baserCMS独自構成。

## 主要ディレクトリ・ファイル
- `config/`：設定ファイル（`install.php`/`setting.php`/`plugins.php`等）
- `plugins/`：コア/追加プラグイン・テーマ
- `webroot/`：公開ディレクトリ
- `composer.json`：依存管理
- `phpunit.xml.dist`：テスト設定
- 基本的にプラグインとして開発するので、ルート直下の `src/`/`tests/` は利用しない

## プラグイン内の主要ディレクトリ・ファイル
- `src/`：プラグイン本体
- `config/`：設定ファイル（`bootstrap.php`/`setting.php`等）
- `tests/`：ユニットテスト・シナリオ・Fixture

## プラグイン・テーマ開発
- プラグインは `BcBake` でスキャッフォルド可能。`README.md`/`config/`/`src/`/`templates/`/`webroot/` などCakePHP標準＋baserCMS独自構成。
- テーマは `plugins/{ThemeName}/` 配下。`templates/layout/`（大枠）、`src/View/Helper/`（表示用関数）、`webroot/img/`（初期画像）など。

## API・サービス連携
- APIは `/baser/api/` 配下。管理系は `/baser/api/admin/`配下。認証はトークン方式。

# MCPサーバー basercms-mcp を利用する場合
MCPサーバー basercms-mcp がセットアップされている場合は、こちらを利用して、baserCMSのデータの取得や更新を行います。
データの取得更新対象は、basercms-mcp の仕様として、.env に記載します。

## その他
- 参考資料：https://baserproject.github.io/5/functions/bc-custom-content/
- baserCMS4系資産は `__assets/` に保存。






