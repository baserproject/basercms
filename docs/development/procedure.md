# 開発の手順

## 1. 開発対象 を選択する

開発対象については次の３つより選びます。  

基本的には Issue をメインで対応し、対応できる Issue がなくなったら、機能要件一覧から機能の実装を検討します。実装できそうな機能がない場合は、ucmitz 進行管理の未対応分を対応していきます。

マーキング漏れをマークしている作業だけでも非常に助かります。

　
### GitHubのIssue より選択する
GitHub の [Issue](https://github.com/baserproject/ucmitz/issues) 現在対象となっているマイルストーンの Issue より、対応する Issue を選択します。  
担当することをコメントしておくと他の人作業がかぶりません。  
（コラボレータは、Issue で担当するコメントを見つけたらその人を Assignees に設定してください）

　
### 機能要件一覧より選択する
[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) にて各機能の仕様を確認し、現在進行しているマイルストーンの機能より取りかかれそうな機能な選択します。  
機能要件一覧より選択する場合、次の項目を更新します。

- 担当者名：自身の名前を記入します
- Issue：「●」を記載し、Issueへのリンクを貼ります（Issueが存在しなければ新しく作成します）
- 状況：着手中に切り替えます。

（コラボレーターは、新しい Issueを見つけたら、適切なマイルストンを設定してください）
　
### ucmitz 進行管理の未対応分より選択する 
[ucmitz 進行管理](https://docs.google.com/spreadsheets/d/1EGxMk-dy8WIg2NmgOKsS_fBXqDB6oJky9M0mB7TADEk/edit#gid=938641024) にて作業状況の進捗を確認できますが、その中より未対応のものを対応していいきます。

 - **未チェックを対応する**  
 「チェック済」に「●」が入っていないものは動作の確認ができてない状態です。動作を確認し動作しない場合は動作するように調整します。
 - **残タスクを対応する**  
 「TODOなし」に「●」が入っていないものは残タスクが残っている状態です。残タスクを解消して動作するように調整します。
 - **ユニットテストを実装する**  
 「テスト実装済」に「●」が入っていないものはユニットテストが未実装の状態です。ユニットテストを実装します。
 
中にはマーキングが漏れているものもありますので見つけた場合は [コード移行時のマーキング](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_rule.md#コード移行時のマーキング) を参考に、マーキングをお願いします。

　
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
また、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) の状況を「レビュー待ち」に切り替えます。

　
## 7. レビューとマージ

マージ担当者はコードをレビューし問題なければマージします。  
また、実装担当者は、コードがマージされたら、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) の状況を「完了」に切り替えます。

　
