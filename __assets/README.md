# __assets README

このディレクトリには、ucmitz の開発において参考となるファイルを保存します。  
データベースの初期データについては次のファイルとして保存してください。

```
/docker/mysql/docker-entry-point-initdb.d/basercms.sql
```  

これは、最初の docker-compose 時に自動読み込みを行うため、また、GitHub Actions におけるユニットテストで利用するためです。
