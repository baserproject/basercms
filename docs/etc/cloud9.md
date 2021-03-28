# Cloud9 上で ucmitz を動作させる

Cloud9を利用すると同時に同じソースコードを編集することができ、非常に便利です。
ucmitz を動作させるには次の手順を踏みます。

　

## docker-compose をインストールする

```
sudo curl -L https://github.com/docker/compose/releases/download/1.16.1/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

　

## docker-compose.yml を準備

```
cp docker/docker-compose.yml.default docker/docker-compose.yml
```
Cloud9 において、標準では、8080 - 8082 しかポートが利用できないため、以下を書き換えます。

```
vim docker/docker-compose.yml

"80:80" → "8080:80"
"8088:80" → "8081:80"
"1080:1080" → "8082:1080"
```

　

## Docker 起動

```
docker-compose up -d
```

　

## logs / tmp フォルダを作成

```
mkdir logs
mkdir tmp
chmod 777 logs
chmod 777 tmp
```

　

## ベースとなるURLを確認する
Cloud9 の メニューの `Preview` より、`Preview Running Application` を実行する。
Cloud9 内にブラウザが立ち上がるので、URLの入力欄にカーソルを当てるとURLを確認できる。

　
## 開発中のURLにアクセスする

```
https://{ベースURL}/baser/admin/users/index
```

　
