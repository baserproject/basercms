# 開発への貢献方法

baserCMS５の開発コードは、`ucmitz` です。開発については、`ucmitz` レポジトリの `dev` ブランチを利用します。

## 開発方針の確認

開発に携わる前に [開発方針](https://docs.google.com/document/d/1QAmScc65CwMyn8QuwWKE9q_8HnSKcW9oefI9RrHoUYY/edit) を必ず確認します。

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
8. ブラウザで、[http://localhost/baser/admin/users/index](http://localhost/baser/admin/users/index) にアクセスして表示を確認します。
9. admin@example.com / password でログインします。

## 開発状況を確認する

開発状況は都度変化していきます。随時、[baserCMS５開発状況](https://github.com/baserproject/ucmitz/blob/dev/DEVELOPMENTAL_STATUS.md) を確認します。

## 開発の手順

### 1. 実装する機能を選択する

[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) を確認し、取りかかれそうな機能を選択します。

### 2. 仕様を確認する

[ucmitz仕様](https://baserproject.github.io/ucmitz/) より上記機能の仕様を確認します。

### 3. Issue を立てる

[Issue](https://github.com/baserproject/ucmitz/issues) に実装対象がすでに存在するか確認しなければ作成します。

### 4. ブランチを切る

Issue番号にもとづいた名称でブランチを作成し切り替えます。（例） dev-#1

### 5. 詳細仕様をメソッドのコメントに転記する

仕様書を元に詳細仕様を作成しメソッドのヘッダーコメントに記載します。

### 6. 機能を実装する

ヘッダーコメントの仕様に従って機能を実装します。

### 7. ユニットテストの作成

テスト可能なメソッドを作成した場合は、ユニットテストも作成しておきます。
※ 現時点（2020/06/24）ではコントローラーのテストは作成しません。

### 8. プルリクエストを作成する

実装とテストが完了したら、自身のレポジトリにプッシュしプルリクエストを作成し、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) の「状況」を「レビュー待ち」に更新します。

### 9. マージ確認

プルリクエストがマージされたら、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) の「状況」を「完了」に更新します。

## 仕様定義の手順

### 1. github.io へ定義を記載

仕様の定義者は、 [ucmitz仕様](https://baserproject.github.io/ucmitz/) で閲覧できるように仕様を定義していきます。
定義の際には、 `git@github.com:baserproject/baserproject.github.io.git` より clone し、マークダウンで定義を記載します。

### 2. 機能要件一覧へ要件を追加

定義した仕様が、 [機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) 存在するか確認し、なければ追加します。

## その他のドキュメント
- [プラグインの呼び出し](https://github.com/baserproject/ucmitz/blob/dev/docs/call-plugin.md)
- [BcAdminThirdの開発](https://github.com/baserproject/ucmitz/blob/dev/plugins/bc-admin-third/README.md)
- [モノレポによるパッケージ管理](https://github.com/baserproject/ucmitz/blob/dev/docs/monorepo.md)
- [Cloud9 上で Docker を動作させる](https://github.com/baserproject/ucmitz/blob/dev/docs/cloud9.md)
