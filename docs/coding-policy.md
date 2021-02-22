# コーディング方針

## 既存コードの移植について

基本的には、テストも含めてbaserCMS4の既存コードを配置し、動作するように改修を加えていく。

## ユニットテストについて

移植のタイミングで存在しないテストは必ず追加する。また、新しいメソッドについても必ずテストを追加する。
その際、テストの実装が間に合わない場合は、 `markTestIncomplete()` を記載しておくこと。

```
$this->markTestIncomplete('Not implemented yet.');
```

## File / Folder の取り扱い

CakePHP4 から、File、Folder クラスは非推奨となり、SplFileInfo、SplFileObject の利用が推奨されているが、baserCMSでは利用箇所が多いため、一旦、そのまま利用する。

## HTML

- table タグの `cellpadding="0" cellspacing="0"` は除外する。
