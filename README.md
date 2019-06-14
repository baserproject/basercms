# Project to migrate baserCMS to CakePHP3

[![License](https://img.shields.io/packagist/l/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/baserproject/basercms)

baserCMSをCakePHP3化するためのブランチです。  
- [BaserApp ソースコード / baserproject/basercms:dev-5-cake3](https://github.com/baserproject/basercms/tree/dev-5-cake3)  
baserCMSのアプリケーションフレームを提供
- [BaserCore ソースコード / baserproject/baser-core:dev-5-base-pattern](https://github.com/baserproject/baser-core/tree/dev-5-base-pattern)  
baserCMSの本体、主にURLに紐づくルーティングと、RESTfull API を提供
- [BcAdminThird ソースコード / baserproject/bc-admin-third:dev-5-base-pattern](https://github.com/baserproject/bc-admin-third/tree/dev-5-base-pattern)  
baserCMSの画面表示をテーマとして提供
- [開発方針](https://docs.google.com/document/d/1QAmScc65CwMyn8QuwWKE9q_8HnSKcW9oefI9RrHoUYY/edit)

## 開発に協力する準備

1. BaserApp をクローンします。`dev-5-cake3` ブランチを利用します。  
`git clone https://github.com/baserproject/basercms.git`
2. [/docker/README.md](https://github.com/baserproject/basercms/blob/dev-5-cake3/docker/README.md) に従い、Docker環境を準備し、コンテナを起動します。
3. [phpMyAdmin](http://localhost:8080) にアクセスし、`/__assets/basercms.sql` をデータベースに流し込みます。
4. `/config/app.default.php` を `/config/app.php` としてコピーします。
5. [/docker/README.md](https://github.com/baserproject/basercms/blob/dev-5-cake3/docker/README.md) のデータベース情報を元に、`/config/app.php` へデータベースへの接続設定を更新します。
6. コンテナにログインします。  
`docker exec -it basercake3 /bin/bash`
7. composer を実行し、BaserCore、BcAdminThird ほか、CakePHPなどのライブラリをインストールします。  
`composer install`
8. ブラウザで、[http://localhost/baser/admin/users/index](http://localhost/baser/admin/users/index) にアクセスして表示を確認します。

## 現在の状態

- baserCMSのコア（BaserCore）は、CakePHPのプラグインとして開発を前提とし、composer で、vendor 配下内にインストールする仕様とした。 `vendor/baserproject/baser-core/`
- BaserCoreは、.git フォルダを含むようにし、上記パス内のプログラムを直接改修し、コミットできるようにした。
- baserCMS4 の一部のテーブルを SQLファイルで移行し、Bake を利用して、ユーザー情報の CRUD を作成した。
- Docker環境がすぐに作れる準備を行った。
- src/Application.php にプラグインのロードを追記
- BaserCore では、URLに依存する画面のレンダリング用コントロラーと、REST API 用コントローラーを提供し、実際のビューファイルは、BcAdminThird が提供するよう疎結合状態にした。

## BaserCore、BcAdminThird のコミット

BaserCore、BcAdminThird のコードは、`/vendor/baserproject/` 内に、レポジトリごと配置されています。  
変更した場合は、それぞれのレポジトリでコミットする必要があるので注意が必要です。

## 今後の課題

- まずはコントリビューターにて基礎となるアーキテクチャーを設計し、ユーザー情報をベースとして雛形を作成
- アーキテクチャーは、API による JSON 出力を前提とする
- 管理システムの新しいデザイン版をリリース後、適用する
- ユーザー管理が一通りできたら、それをベースとして他の管理機能を作成していく

## 気づき
- composer プラグインをで呼び出すには packagist に登録が必要
- プラグインには、composer.json を定義する必要がある
- プラグインの、composer.json で、type を cakephp-plugin にしようが、vendor に登録される
- composer update を実施した場合、 vendor/cakephp-plugins.php が自動更新される
- vendor/cakephp-pluigins.php が更新されないとプラグインの名前解決ができない
- プラグインの定義は、src/Application.php で行わなければならない