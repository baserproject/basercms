# Project to migrate baserCMS to CakePHP3

[![License](https://img.shields.io/packagist/l/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/ryuring/basercake3)

baserCMSをCakePHP3化するためのプロジェクトです。  
ソースコードはこちらから確認できます。: [cakephp/cakephp](https://github.com/ryuring/basercake3).

## 開発に協力する準備

1. MySQLでデータベースを用意します。
2. `/.assets/basercake3.sql` をデータベースに流し込みます。
3. `/config/app.php` でデータベースへの接続設定を更新します。
4. ブラウザで、/baser/admin/users/index にアクセスして表示を確認します。
5. これからどうするか考える

マシンのWebサーバーを使用してデフォルトのホームページを表示するか、組み込みWebサーバーを以下のように起動することができます。

```bash
bin/cake server -p 8765
```

その後、 `http://localhost:8765` にアクセスしてようこそページを表示してください。


