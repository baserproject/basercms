# 開発への貢献方法

baserCMS５の開発コードは、`ucmitz` です。開発については、`ucmitz` レポジトリの `dev` ブランチを利用します。

　

## 開発方針の確認

開発にたずさわる前に [開発方針](https://docs.google.com/document/d/1QAmScc65CwMyn8QuwWKE9q_8HnSKcW9oefI9RrHoUYY/edit) を必ず確認します。

　

## 開発環境の準備

1. BaserApp をクローンし、ブランチを切り替えます。
`git clone https://github.com/baserproject/ucmitz.git`
`git checkout dev`
2. [/docs/environment.md](https://github.com/baserproject/ucmitz/blob/dev/docs/environment.md) に従い、Docker on Vagrant 環境を準備し、コンテナを起動します。
3. [phpMyAdmin](http://localhost:8080) にアクセスし、`/__assets/basercms.sql` をデータベースに流し込みます。
4. `/config/app_local.example.php` を `/config/app_local.php` としてコピーします。
5. `/config/.env.example` を `/config/.env` としてコピーします。
6. コンテナにログインします。
`docker exec -it bc5-php /bin/bash`
7. composer を実行し、CakePHPなどのライブラリをインストールします。
`composer install`
8. ブラウザで、[https://localhost/baser/admin/users/login](https://localhost/baser/admin/users/login) にアクセスして表示を確認します。
9. admin@example.com / password でログインします。

　


## 開発の手順

### 1. 実装する機能、または課題を選択する

[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) にて各機能の仕様を確認し、取りかかれそうな機能な選択します。もしくは、同ファイル内のバージョンごとの課題タブより対応する課題を選択します。

### 2. Issue を立てる

[Issue](https://github.com/baserproject/ucmitz/issues) に実装対象がすでに存在するか確認しなければ作成します。

### 3. ブランチを切る

Issue番号にもとづいた名称でブランチを作成し切り替えます。（例） dev-#1

### 4. 詳細仕様をメソッドのヘッダーコメントに転記する

詳細仕様を作成しメソッドのヘッダーコメントに記載します。
仕様定義については、[ドキュメントキーワード定義](https://github.com/baserproject/ucmitz/blob/dev/docs/keyword.md) を参考にアルファベットで定義を簡潔に記述する

### 5. 機能を実装する

ヘッダーコメントの仕様に従って機能を実装します。

### 6. ユニットテストの作成

テスト可能なメソッドを作成した場合は、ユニットテストも作成しておきます。
ユニットテストの作成と実行については [ユニットテスト](https://github.com/baserproject/ucmitz/blob/dev/docs/unittest.md) を参考にしてください。

### 7. プルリクエストを作成する

実装とテストが完了したら、自身のレポジトリにプッシュしプルリクエストを作成し、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) （もしくは同ファイル内課題タブの課題）の「状況」を「レビュー待ち」に更新します。

### 8. マージ確認

プルリクエストがマージされたら、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) （もしくは同ファイル内課題タブの課題）の「状況」を「完了」に更新します。

　

## 全体の構成や baserCMS４からの変更点について

[開発メモ](https://github.com/baserproject/ucmitz/blob/dev/DEVELOPMENTAL_MEMO.md) を参考にします。

　

## 新しい仕様の定義について

仕様の定義者は、新しく仕様を定義する場合、 [機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) に存在するか確認し、なければ追加します。

　

## 開発中に発生した開発に必要なドキュメントの追加について

`/docs/` に、マークダウン形式でファイルを作成します。

　

## その他の開発に必要なドキュメント
- [システム要件](https://github.com/baserproject/ucmitz/blob/dev/docs/system.md)
- [データベースの定義](https://github.com/baserproject/ucmitz/blob/dev/docs/database.md)
- [プラグインの呼び出し](https://github.com/baserproject/ucmitz/blob/dev/docs/call-plugin.md)
- [BcAdminThirdの開発](https://github.com/baserproject/ucmitz/blob/dev/plugins/bc-admin-third/README.md)
- [外部のCakePHPアプリケーションで baserCMSを利用する](https://github.com/baserproject/ucmitz/blob/dev/docs/application.md)
- [baserCMSのプラグイン開発](https://github.com/baserproject/ucmitz/blob/dev/docs/plugin.md)
- [ユニットテスト](https://github.com/baserproject/ucmitz/blob/dev/docs/unittest.md)
- [モノレポによるパッケージ管理](https://github.com/baserproject/ucmitz/blob/dev/docs/monorepo.md)
- [Cloud9 上で Docker を動作させる](https://github.com/baserproject/ucmitz/blob/dev/docs/cloud9.md)
- [開発メモ](https://github.com/baserproject/ucmitz/blob/dev/DEVELOPMENTAL_MEMO.md)
- [将来的に追加したい機能](https://docs.google.com/document/d/1AwJQ0h0xQ5utFB1tVzLh1b1UhZp-lxQbM2fDjxtDc9I/edit#)
