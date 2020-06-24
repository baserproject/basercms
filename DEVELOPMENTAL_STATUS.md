# baserCMS５開発状況

## 現在の状態

- CakePHP4.0.x にて開発
- BaserCore では、コントロラーやモデルを提供し、実際のビューファイルは、BcAdminThird が提供するよう疎結合状態にした。
- baserCMSのコア（BaserCore）は、CakePHPのプラグインとしての開発を前提とし、plugins 配下内に配置する仕様とした。 `/plugins/baser-core/`
- 管理画面のテーマは、CakePHPのプラグインとしての開発を前提とし、plugins 配下内に配置する仕様とした。 `/plugins/bc-admin-third/`
- src/Application.php にプラグインのロードを追記
- Docker on Vagrant の環境をすぐに作れるようにした。
- モノレポとして `monorepo-builder` を利用し、BaserCore、BcAdminThird も統合的に管理できるようにした。
- baserCMS4 の一部のテーブルを SQLファイルで移行した。
- ユーザー管理を開発中
  - 一覧
  - 新規登録
  - 編集
  - ログイン
- ユーザーグループを開発中
  - 一覧
  - 新規登録
  - 編集

## 今後の課題

- まずはコントリビューターにて基礎となるアーキテクチャーを設計し、ユーザー管理をベースとして雛形を作成
- ユーザー管理が一通りできたら、それをベースとして他の管理機能を作成していく。
