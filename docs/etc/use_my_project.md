# 外部のCakePHPアプリケーションで ucmitz を利用する

人柱となり自身のプロジェクトで ucmitz を利用したい場合は、composer でインストールできます。

　

## composer でインストール

```
composer require baser-core
```

　

## Application クラスの修正

`src/Application` の継承先クラスを `BaserCore\BcApplication` に変更します。

　
## データベースを初期化する

次のMySQL用のSQLファイルを利用してデータベースを初期化します。

```
/docker/mysql/docker-entry-point-initdb.d/basercms.sql
```  
