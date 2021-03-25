# モデルにおける注意点

## モデルの取得

```php
// baserCMS4
ClassRegistry::init('User');

// ucmitz
TableRegistry::getTableLocator()->get('Users');
```

## テーブルプレフィックス

CakePHP3系よりテーブルプレフィックスはなくなっています。ない前提での移行が必要です。
