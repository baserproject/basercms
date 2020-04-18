# Project to migrate baserCMS to CakePHP3

[![License](https://img.shields.io/packagist/l/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/baserproject/basercms)

baserCMSをCakePHP3化するためのブランチです。  
BaserApp を親パッケージとして、BaserCore、BcAdminThirdは、子パッケージとしてモノレポで管理します。
- [BaserApp ソースコード / baserproject/basercms:dev-5-cake3](https://github.com/baserproject/basercms/tree/dev-5-cake3)  
baserCMSのアプリケーションフレームを提供
- [BaserCore ソースコード / baserproject/baser-core:dev-5-cake3](https://github.com/baserproject/baser-core/tree/dev-5-cake3)  
baserCMSの本体、主にURLに紐づくルーティングと、ビジネスロジックを提供
- [BcAdminThird ソースコード / baserproject/bc-admin-third:dev-5-cake3](https://github.com/baserproject/bc-admin-third/tree/dev-5-cake3)  
baserCMSの画面表示をテーマとして提供

## 開発方針
- [開発方針](https://docs.google.com/document/d/1QAmScc65CwMyn8QuwWKE9q_8HnSKcW9oefI9RrHoUYY/edit) 

## 開発に協力する準備

1. BaserApp をクローンし、`dev-5-cake3` ブランチを利用します。  
`git clone https://github.com/baserproject/basercms.git`
2. [/docker/README.md](https://github.com/baserproject/basercms/blob/dev-5-cake3/docker/README.md) に従い、Docker環境を準備し、コンテナを起動します。
3. [phpMyAdmin](http://localhost:8080) にアクセスし、`/__assets/basercms.sql` をデータベースに流し込みます。
4. `/config/app.default.php` を `/config/app.php` としてコピーします。
5. コンテナにログインします。  
`docker exec -it bc5-php /bin/bash`
6. composer を実行し、CakePHPなどのライブラリをインストールします。  
`composer install`
7. ブラウザで、[http://localhost/baser/admin/users/index](http://localhost/baser/admin/users/index) にアクセスして表示を確認します。
8. admin@example.com / password でログインします。

## 現在の状態

- baserCMSのコア（BaserCore）は、CakePHPのプラグインとしての開発を前提とし、plugins 配下内に配置する仕様とした。 `/plugins/baser-core/`
- 管理画面のテーマは、CakePHPのプラグインとしての開発を前提とし、plugins 配下内に配置する仕様とした。 `/plugins/bc-admin-third/`
- baserCMS4 の一部のテーブルを SQLファイルで移行し、Bake を利用して、ユーザー情報の CRUD を作成した。
- Docker環境がすぐに作れるようにした。
- src/Application.php にプラグインのロードを追記
- BaserCore では、コントロラーやモデルを提供し、実際のビューファイルは、BcAdminThird が提供するよう疎結合状態にした。
- モノレポとして `monorepo-builder` を利用し、BaserCore、BcAdminThird も統合的に管理できるようにした。

## 今後の課題

- まずはコントリビューターにて基礎となるアーキテクチャーを設計し、ユーザー管理をベースとして雛形を作成
- ユーザー管理が一通りできたら、それをベースとして他の管理機能を作成していく。
- できるだけ、APIを提供しやすいアーキテクチャーを目指す。

## そのほかのドキュメント
- [プラグインの呼び出し](/documents/call-plugin.md)
- [モノレポによるパッケージ管理](/documents/monorepo.md)
