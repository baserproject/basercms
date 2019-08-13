# Docker を利用した開発方法（Mac）

[Docker for Mac](https://www.docker.com/docker-mac) を利用する事で、簡単に開発環境の構築を行う事ができます。  
事前に Docker for Mac をインストールしておきます。

## docker フォルダへ移動

```
cd docker
```

## 設定ファイルの配置

docker/docker-compose.yml.default を docker-compose.yml として同階層にコピーします。
※ このファイルは自由に編集可能です。

```
cp docker-compose.yml.default docker-compose.yml
```
 
## コンテナを作成して起動する

```
docker-compose up -d
```

## composer を実行する

```
docker exec bc-php composer install
```

## baserCMS を起動する

composer によるライブラリのインストールが完了したら、 `http://localhost/` にアクセスしてください。basercmsのインストールページが表示されます。

### データベース情報
| name | value |
|-----------|:------------|
| host | bc-db |
| user | root |
| password | root |
| database | basercms |

## コンテナの操作

### コンテナを起動する

```
docker-compose start
```
※ 一度コンテナを作成した場合は、up ではなく、start を利用しないと、コンテナが初期化されますので注意が必要です。

### コンテナを停止する

```
docker-compose stop
```

### コンテナを再起動する

```
docker-compose restart
```

### コンテナにログインする

```
docker exec -it bc-php /bin/bash
```

## コンテナ上でbaserCMSを操作する

コンテナにログインした状態で、baserCMSのインストール等を簡単に実行する事ができます。

### baserCMSをインストールする（MySQL環境）

```
/var/www/html/docker/bin/bc_install_mysql
```

### baserCMSを再インストールする（MySQL環境）

```
/var/www/html/docker/bin/bc_reinstall_mysql
```

### baserCMSをリセットする

```
/var/www/html/docker/bin/bc_reset
```

### baserCMSのユニットテストを実行する

```
/var/www/html/docker/bin/bc_test [-c] type PathToClass filterMethod

# 例
/var/www/html/docker/bin/bc_test baser View/Helper/BcBaserHelper testGetLink
```

- -c : カバレッジを確認する為のHTMLを `app/tmp/coverage` に作成
- type : コアのテストの場合、`baser` そうでない場合、プラグイン名を指定
- PathToClass : テスト対象のクラスまでのパスを含めた名称を指定
- filterMethod : 特定のメソッドのみ実行する場合に、メソッド名称を指定

## データベース（MySQL）を確認する
データベースの内容は、MySQLで確認する事ができます。  
`http://localhost:8080/`


## 送信メールを確認する

baserCMSが送信したメールは、MailCatcher で確認する事ができます。  
`http://localhost:1080/`


## SSL通信でサイトを確認する
自己証明書によってSSL通信で確認する事ができます。  
`https://localhost/`


## Xdebug によるデバッグ（PhpStorm on Mac）

### PhpStorm設定

#### サーバー追加

ホストを `localhost` として、サーバーを追加し、パスのマッピングを行います。
ローカルのプロジェクトディレクトリが、`/var/www/html` としてマッピングされるように設定します。

#### PHP Remote Dubug 設定

PHP Remote Debug を追加します。  
先ほど選択したサーバーを選択し、ide key を設定します。 （ide key は何でも構いません。）

### デバッグ実行

プログラムコードの任意の箇所において、ブレークポイントを設定し、 Start Listening for PHP Debug Connections のボタンをクリックします。  
ブラウザでプログラムを実行し、ブレークポイントで動作が止まれば成功です。  
なお、ユニットテストでもブラウザと同様にブレークポイントでプログラムを止める事ができます。


