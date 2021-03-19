# コントローラーにおける注意点

### サブメニューの設定

廃止。コントローラーの下記のようなコードは削除する。

```php
$this->subMenuElements = ['plugins'];
```

### 検索フォームの設定
```php
// baserCMS4
$this->search = $templateName;
// ucmitz
$this->setSearch($templateName);
```

### ヘルプの設定
```php
// baserCMS4
$this->help = $templateName;
// ucmitz
$this->setHelp($templateName);
```

### タイトルの設定
```php
// baserCMS4
$this->pageTitle = $title;
// ucmitz
$this->setTitle($title);
```
