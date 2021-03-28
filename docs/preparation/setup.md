# ucmitz をセットアップする
ucmitz の動作を確認するためのセットアップは簡単です。

## 設定ファイルの準備
`/config/app_local.example.php` を `/config/app_local.php` としてコピーします。

　
## .env の準備
`/config/.env.example` を `/config/.env` としてコピーします。

　
## composer の実行
コンテナにログインし、composer でライブラリをインストールします。
```shell
vagrant ssh
cd /vagrant/docker
docker exec -it bc5-php /bin/bash
composer install
```

　
## ブラウザで表示を確認
ブラウザで、次のURLにアクセスして表示を確認します。

- [https://localhost/baser/admin/users/login](https://localhost/baser/admin/users/login) 

　
## 管理画面にログインする
メールアドレスとパスワードを入力し管理画面にログインします。

- ユーザー名：admin@example.com
- パスワード：password
