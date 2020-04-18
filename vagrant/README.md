# Docker on Vagrant で開発

[Docker for Mac](https://www.docker.com/docker-mac) で開発する際、表示速度が遅い場合、[Vagrant](https://www.vagrantup.com/) 上で、Docker を動かすことで、表示速度を改善することができます。  
事前に [VirtualBox](https://www.virtualbox.org/) と Vagrant をインストールしておきます。

## Vagrantfile をコピーする

Vagrant の設定ファイルをコピーし、メモリ等、必要があれば設定を修正します。
```shell script
cp vagrant/Vagrantfile Vagrantfile
```

## Vagrant を実行する

Vagrant を実行すると、ContOS をインストールし、その上に、Docker や、docker-compose のインストールが始まります。  
docker-compose も自動実行しますので、他に何もする必要がありません。
```shell script
vagrant up
```

## ブラウザで確認する

[https://localhost/](https://localhosst/) にアクセスすると、baserCMSのインストールがはじまります。

## Vagrant 上の Docker コンテナを操作する

Vagrant にコンソールログインすることで、Vagrant 上の Docker コンテナを操作することができます。
```shell script
vagrant ssh
cd /vagrant/docker
docker-compose restart # コンテナを再起動する
```
※ Dockerの操作については、[docker/README.md](https://github.com/baserproject/basercms/blob/master/docker/README.md) を参考にしてください。

