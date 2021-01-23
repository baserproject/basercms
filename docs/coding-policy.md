# コーディング方針

## 既存コードの移植について

基本的には、テストも含めてbaserCMS4の既存コードを配置し、動作するように改修を加えていく。

## ユニットテストについて

移植のタイミングで存在しないテストはできる限り追加する。また、新しいメソッドは必ずテストを追加する。

## File / Folder の取り扱い

CakePHP4 から、File、Folder クラスは非推奨となり、SplFileInfo、SplFileObject の利用が推奨されているが、baserCMSでは利用箇所が多いため、一旦、そのまま利用する。

## HTML

- table タグの `cellpadding="0" cellspacing="0"` は除外する。
