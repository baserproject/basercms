# セッションにおける注意点

## セッションの取得

セッションは、リクエストから取得します。

```php
$session = $request->getSession();
$session->read('etc');
```

　
