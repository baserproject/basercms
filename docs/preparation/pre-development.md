# 開発の準備

1. BaserApp をクローンし、ブランチを切り替えます。
`git clone https://github.com/baserproject/ucmitz.git`
`git checkout dev`
2. [開発環境の構築](https://github.com/baserproject/ucmitz/blob/dev/docs/preparation/environment.md) に従い、Docker on Vagrant 環境を準備し、コンテナを起動します。
3. [phpMyAdmin](http://localhost:8080) にアクセスし、`/__assets/basercms.sql` をデータベースに流し込みます。
4. `/config/app_local.example.php` を `/config/app_local.php` としてコピーします。
5. `/config/.env.example` を `/config/.env` としてコピーします。
6. コンテナにログインします。
`docker exec -it bc5-php /bin/bash`
7. composer を実行し、CakePHPなどのライブラリをインストールします。
`composer install`
8. ブラウザで、[https://localhost/baser/admin/users/login](https://localhost/baser/admin/users/login) にアクセスして表示を確認します。
9. admin@example.com / password でログインします。
