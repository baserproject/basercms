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
