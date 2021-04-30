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

OSのカーネルが古いため最新のGuest Additionsをインストールできないことが原因です。

```
vagrant ssh -c 'sudo yum update -y kernel'
```

上記のコマンドを実行してゲストOS側のカーネルを更新してください。

```
vagrant reload
```

上記コマンドでvagrantを再起動すればGuest Additionsも更新されます。
