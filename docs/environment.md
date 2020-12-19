# 開発環境の構築

開発は、Vagrant 上に Docker コンテナを立ち上げる、Docker on Vagrant で行います。

## 事前準備
[VirtualBox](https://www.virtualbox.org/) と [Vagrant](https://www.vagrantup.com/) をインストールします。


## Vagrant のプラグインのインストール
ucmitz プロジェクトのディレクトリに移動しプラグインのインストールコマンドを実行します。
```shell script
vagrant plugin install vagrant-vbguest
vagrant plugin install vagrant-docker-compose
```

## Vagrantfile をコピーする
Vagrant の設定ファイルをコピーし、メモリ等、必要があれば設定を修正します。
```shell script
cp vagrant/Vagrantfile Vagrantfile
```
※ このファイルは自由に編集可能です。

## docker-compose をコピーする
```shell script
cp docker/docker-compose.default.yml docker-compose.yml
```
※ このファイルは自由に編集可能です。

## Vagrant を実行する
Vagrant を実行すると、ContOS をインストールし、その上に、Docker や、docker-compose のインストールが始まります。
docker-compose も自動実行しますので、他に何もする必要がありません。
```shell script
vagrant up
```

## Vagrant にログイン
```shell script
vagrant ssh
```

## docker フォルダに移動
ローカルのファイル群は、/vagrant にマウントされています。
```shell script
cd /vagrant/docker
```

## composer を実行する
composer を実行し、CakePHP等、必要なライブラリをインストールします。
```
docker exec bc5-php composer install
```

## ブラウザで確認する
[https://localhost/](https://localhost/) にアクセスすると、cakephpのトップページが表示されます。

## データベース（MySQL）を確認する
データベースの内容は、MySQLで確認する事ができます。
[http://localhost:8080/](http://localhost:8080/)

### データベース情報
| name | value |
|-----------|:------------|
| host | bc5-db |
| user | root |
| password | root |
| database | basercms |

## 送信メールを確認する
baserCMSが送信したメールは、MailCatcher で確認する事ができます。
[http://localhost:1080/](http://localhost:1080/)


## SSL通信でサイトを確認する
自己証明書によってSSL通信で確認する事ができます。
[https://localhost/](https://localhost/)
