# Project to migrate baserCMS to CakePHP3

[![License](https://img.shields.io/packagist/l/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/baserproject/basercms)

baserCMSをCakePHP3化するためのブランチです。  
- [BaserApp ソースコード / baserproject/basercms:dev-5-cake3](https://github.com/baserproject/basercms/tree/dev-5-cake3)
- [BaserCore ソースコード / baserproject/baser-core:dev-5-base-pattern](https://github.com/baserproject/baser-core/tree/dev-5-base-pattern)
- [BcAdminThird ソースコード / baserproject/bc-admin-third:dev-5-base-pattern](https://github.com/baserproject/bc-admin-third/tree/dev-5-base-pattern)
- [開発方針](https://docs.google.com/document/d/1QAmScc65CwMyn8QuwWKE9q_8HnSKcW9oefI9RrHoUYY/edit)

## 開発に協力する準備

1. [/docker/README.md](https://github.com/baserproject/basercms/blob/dev-5-cake3/docker/README.md) に従い、Docker環境を準備し、コンテナを起動します。
2. [phpMyAdmin](http://localhost:8080) にアクセスし、`/__assets/basercms.sql` をデータベースに流し込みます。
3. `/config/app.default.php` を `/config/app.php` としてコピーする。
3. [/docker/README.md](https://github.com/baserproject/basercms/blob/dev-5-cake3/docker/README.md) のデータベース情報を元に、`/config/app.php` へデータベースへの接続設定を更新します。
4. ブラウザで、[http://localhost/baser/admin/users/index](http://localhost/baser/admin/users/index) にアクセスして表示を確認します。

## 現在の状態

- baserCMSのコア（BaserCore）は、CakePHPのプラグインとして開発を前提とし、composer で、vendor 配下内にインストールする仕様とした。 `vendor/baserproject/baser-core/`
- BaserCoreは、.git フォルダを含むようにし、上記パス内のプログラムを直接改修し、コミットできるようにした。
- baserCMS4 の一部のテーブルを SQLファイルで移行し、Bake を利用して、ユーザー情報の CRUD を作成した。
- Docker環境がすぐに作れる準備を行った。
- src/Application.php にプラグインのロードを追記
- BaserCore では、URLに依存する画面のレンダリング用コントロラーと、REST API 用コントローラーを提供し、実際のビューファイルは、BcAdminThird が提供するよう疎結合状態にした。

## 今後の課題

- まずはコントリビューターにて基礎となるアーキテクチャーを設計し、ユーザー情報をベースとして雛形を作成
- アーキテクチャーは、API による JSON 出力を前提とする
- 管理システムの新しいデザイン版をリリース後、適用する
- ユーザー管理が一通りできたら、それをベースとして他の管理機能を作成していく