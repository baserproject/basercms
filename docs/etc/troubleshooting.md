# トラブルシューティング

## Vagrantのマウントが正常に行われない

### トラブル内容
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

　
### 解決法

これらはOSのカーネルが古いことと、vbguestプラグインが正常に動いていないことが原因のようです。

以下のコマンドを仮想マシン内で順番に実行して、正常な状態に戻すことで動くようになります。
まず、ホストOSからvbguestの状態を確認します。

```
% vagrant vbguest --status

[default] GuestAdditions versions on your host (6.1.16) and guest (6.1.6) do not match.
```
上記の様に、バージョン違いが原因で正常にマウントできない場合がありますので、
正しいバージョンのGuestAddonを手動でインストールする必要があります。
ここでは、ホストのバージョンに合わせる様に6.1.16を手動でインストールします。

```
(vagrant up && vagrant ssh を行って仮想マシンにssh接続している状態)

$ sudo yum -y update kernel
$ sudo yum -y install kernel-devel kernel-headers dkms gcc gcc-c++
$ cd /tmp
$ wget http://download.virtualbox.org/virtualbox/6.1.16/VBoxGuestAdditions_6.1.16.iso
$ sudo mount -t iso9660 /tmp/VBoxGuestAdditions_6.1.16.iso /mnt
$ cd /mnt
$ sudo ./VBoxLinuxAdditions.run
$ sudo /sbin/rcvboxadd setup
$ sudo reboot
```
ここまで実行したらssh接続を閉じます。
その後、ホストOSから
```
% vagrant reload default
```
を実行することで正常に起動するようになります。
