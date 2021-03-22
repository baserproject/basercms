# リクエスト関連における注意点

## リクエストの取得
```php
$this->getRequest();    // Controller / View
Router::getRequest();    // Other
```


## URL関連

### 現在のURLを取得する

#### パラメータなし
```php
$request->getPath();
```
#### パラメータあり
```php
$request->getRequestTarget();
```

## リクエストデータ
```php
// baserCMS4
$request->data[];
```
```php
// ucmitz
$request->getData();
```

## パラメーター
```php
// baserCMS4
$request->params[];
```
```php
// ucmitz
$request->getParam();
```
