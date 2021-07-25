# データベースにおける注意点

## テータベース構造を変更した場合

誰かがテータベース構造を変更した場合は、反映するために、コンテナにログインし、migrationの実行を行う必要があります。
(**※CakeSchemaはなくなったので、Console/cake schemaコマンドは使えません**)

```
bin/cake migrations migrate --plugin BaserCore
```

## マイグレーションファイルからデータベースを作成する場合

```
bin/cake bake migration CreateSamples --plugin BaserCore
```
[参考](https://book.cakephp.org/migrations/2/ja/index.html#id5)

そして、plugins/baser-core/config/Schemaのスキーマを参考にカラムを定義してください

## データベースの初期データ反映をシードに反映する場合

```
bin/cake migrations seed --seed SamplesSeed --plugin BaserCore
```
[参考](https://book.cakephp.org/migrations/2/ja/index.html#seed)

## データベースの初期データをFixtureに反映する場合
```
bin/cake bake fixture -r -f -n 20 -s samples --plugin BaserCore
```

### ※[一部スキーマをマイグレーションファイルに変更する方法](https://github.com/baserproject/ucmitz/tools/SchemeCoverter/README.md)
