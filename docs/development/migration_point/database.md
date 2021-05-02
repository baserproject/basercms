# データベースにおける注意点

## テータベース構造を変更した場合

誰かがテータベース構造を変更した場合は、反映するために、コンテナにログインし、migrationの実行を行う必要があります。

```
bin/cake migrations migrate --plugin BaserCore
```
