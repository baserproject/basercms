# 開発への貢献方法

baserCMS５の開発コードは、`ucmitz` です。開発については、`ucmitz` レポジトリの `dev` ブランチを利用します。

### 開発環境の準備

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
