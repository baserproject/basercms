# 開発の手順

## 1. 実装する機能、または課題を選択する

[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) にて各機能の仕様を確認し、取りかかれそうな機能な選択します。もしくは、同ファイル内のバージョンごとの課題タブより対応する課題を選択します。

## 2. Issue を立てる

[Issue](https://github.com/baserproject/ucmitz/issues) に実装対象がすでに存在するか確認しなければ作成します。

## 3. ブランチを切る

Issue番号にもとづいた名称でブランチを作成し切り替えます。（例） dev-#1

## 4. 機能を実装する

ヘッダーコメントの仕様に従って機能を実装します。

## 5. ユニットテストの作成

テスト可能なメソッドを作成した場合は、ユニットテストも作成しておきます。
ユニットテストの作成と実行については [ユニットテスト](https://github.com/baserproject/ucmitz/blob/dev/docs/unittest.md) を参考にしてください。

## 6. プルリクエストを作成する

実装とテストが完了したら、自身のレポジトリにプッシュしプルリクエストを作成し、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) （もしくは同ファイル内課題タブの課題）の「状況」を「レビュー待ち」に更新します。

## 7. マージ確認

プルリクエストがマージされたら、[機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) （もしくは同ファイル内課題タブの課題）の「状況」を「完了」に更新します。

　
