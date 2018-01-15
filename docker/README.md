# Docker を利用した開発方法（Mac）

[Docker for Mac](https://www.docker.com/docker-mac) を利用する事で、簡単に開発環境の構築を行う事ができます。  
事前に Docker for Mac をインストールしておきます。

## 設定ファイルの配置

docker/docker-compose.yml をプロジェクトディレクトリの直下にコピーします。
 
## コンテナを作成して起動する

```
docker-compose up -d
```

## コンテナにログインしてコンポーザーを実行

```
docker exex -it basercms3 /bin/bash
composer install
```

コンポーザーの処理が完了したら、 `config/app.php` で、データベースの設定を行い、`http://localhost/` にアクセスしてください。

### データベース情報
| name | value |
|-----------|:------------|
| host | basercake3-database |
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
docker exec -it basercms /bin/bash
```


