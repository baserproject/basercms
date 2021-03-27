# 開発の手順

## 1. Issue を選択する

GitHub の [Issue](https://github.com/baserproject/ucmitz/issues) より対応する Issue を選択します。
もしくは、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) にて各機能の仕様を確認し、取りかかれそうな機能な選択します。  
機能要件一覧より対応する場合、GitHubの Issueに存在するか確認し、存在しなければ作成し、機能要件一覧にリンクを記載します。

　
## 2. ブランチを切る

Issue番号にもとづいた名称でブランチを作成し切り替えます。  
（例） dev-#1

　
## 3. 機能を実装する

Issueの内容に従って機能を実装します。

　
## 4. ユニットテストの作成

テスト可能なメソッドを作成した場合は、ユニットテストも作成しておきます。  
ユニットテストの作成と実行については [ユニットテスト](https://github.com/baserproject/ucmitz/blob/dev/docs/development/test/unittest.md) を参考にしてください。

　
## 5. マーキングを行う

[コード移行時のマーキング](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_rule.md#コード移行時のマーキング) を参考に、マーキングを行います。

　 
## 6. プルリクエストを作成する

実装とテストが完了したら、自身のレポジトリにプッシュしプルリクエストを作成します。

　


　
