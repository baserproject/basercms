# Project to migrate baserCMS to CakePHP3

[![License](https://img.shields.io/packagist/l/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/ryuring/basercake3)

baserCMSをCakePHP3化するためのブランチです。  
ソースコードはこちらから確認できます。: [baserproject/basercms:dev-5-cake3](https://github.com/baserproject/basercms/tree/dev-5-cake3)

## 開発に協力する準備

1. [/docker/README.md](https://github.com/baserproject/basercms/blob/dev-5-cake3/docker/README.md) に従い、Docker環境を準備し、コンテナを起動します。
2. [phpMyAdmin](http://localhost:8080) にアクセスし、`/.assets/basercake3.sql` をデータベースに流し込みます。
3. [/docker/README.md](https://github.com/baserproject/basercms/blob/dev-5-cake3/docker/README.md) のデータベース情報を元に、`/config/app.php` へデータベースへの接続設定を更新します。
4. ブラウザで、[http://localhost/baser/admin/users/index](http://localhost/baser/admin/users/index) にアクセスして表示を確認します。

## 現在の状態

- baserCMSのコアは、CakePHPのプラグインとして開発を前提とする為、plugins 内に配置
- baserCMS4 の一部のテーブルを SQLファイルで移行し、Bake を利用して、ユーザー情報の CRUD を作成した。
- Docker環境がすぐに作れる準備を行った。
- 直下の src フォルダは利用しないため削除

## 今後の課題

- まずはコントリビューターにて基礎となるアーキテクチャーを設計し、ユーザー情報をベースとして雛形を作成
- アーキテクチャーは、API による JSON 出力を前提とする
- 管理システムの新しいデザイン版をリリース後、適用する
- ユーザー管理が一通りできたら、それをベースとして他の管理機能を作成していく