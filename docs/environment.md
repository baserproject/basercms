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
cp docker/docker-compose.yml.default docker/docker-compose.yml
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

## 環境構築においてのトラブルシューティング

### マウントが正常に行われない
環境を構築する際、以下のようなエラーに遭遇し、マウントが行われない場合があります。
```
==> default: .FileNotFoundError: [Errno 2] No such file or directory: '/vagrant/docker/docker-compose.yml'
The following SSH command responded with a non-zero exit status.
Vagrant assumes that this means the command failed!

 /usr/local/bin/docker-compose-1.25.4  -f "/vagrant/docker/docker-compose.yml" up -d

Stdout from the command:



Stderr from the command:

.FileNotFoundError: [Errno 2] No such file or directory: '/vagrant/docker/docker-compose.yml'
```

または

```
The following SSH command responded with a non-zero exit status.
Vagrant assumes that this means the command failed!

umount /mnt

Stdout from the command:



Stderr from the command:

umount: /mnt: not mounted
```

これらはvbguestプラグインが正常に動いていないことが原因のようです。

### 対処
以下のコマンドを仮想マシン内で順番に実行して、正常な状態に戻すことで動くようになります。
まず、ホストOSからvbguestの状態を確認します。

```
% vagrant vbguest --status

[default] GuestAdditions versions on your host (6.1.16) and guest (6.1.6) do not match.
```
上記の様に、バージョン違いが原因で正常にマウントできない場合がありますので、
正しいバージョンのGuestAddonを手動でインストールする必要があります。

```
(vagrant up && vagrant ssh を行って仮想マシンにssh接続している状態)

$ cd /tmp
$ wget http://download.virtualbox.org/virtualbox/${正しいバージョン}/VBoxGuestAdditions_${正しいバージョン}.iso
$ sudo mount -t iso9660 /tmp/VBoxGuestAdditions_${正しいバージョン}.iso /mnt
$ cd /mnt
$ sudo ./VBoxLinuxAdditions.run
$ sudo /sbin/rcvboxadd setup
$ sudo reboot
```
ここまで実行したらssh接続を閉じます。
その後、ホストOSから
```
vagrant reload default
```
を実行することで正常に起動するようになります。