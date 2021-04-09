# ビューにおける注意点

ビューのテンプレートは、BcAdminThirdプラグインとして、`/plugins/bc-admin-third/templates/` 内に配置します。

## フォーム関連

### フォームコントロールのテンプレート
`/baser-core/config/bc_form.php` で定義しています。

　
### フォームコントロールの出力
```php
// baserCMS4
$this->BcForm->input()
// ucmitz
$this->BcAdminForm->control()
```

　
## タイトルの設定
```php
$this->BcAdmin->setTitle($title);
```

　
## 検索フォームの設定
```php
$this->BcAdmin->setSearch($templateName);
```

　
## ヘルプの設定
```php
$this->BcAdmin->setHelp($templateName);
```

　
## BcTime::format() の引数の順番変更

```php
// baserCMS4
$this->BcTime->format($format, $date);
// ucmitz
$this->BcTime->format($date, $format);
```

また、フォーマットの形式が変更となっているので注意が必要です。`YYYY-MM-dd`  

https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax


## モデルの配列をエンティティに変換する

正規表現で置き換えます。

```
例）
\$modelName\['ModelName'\]\['(.+?)'\]　→　\$modelName->$1
```

　
## リンク作成関連

### 名前付きパラメータ

名前付きパラメータは仕様から削除されています。クエリーストリングに変換します。

```php
// 名前付きパラメーター
$this->BcBaser->link('hoge', ['controller' => 'Users', 'action' => 'index', 'name1' => 1, 'named2' => 2]);
// クエリーストリング
$this->BcBaser->link('hoge', ['controller' => 'Users', 'action' => 'index', '?' => ['name1' => 1, 'named2' => 2]]);
```

　
## HTML

- table タグの `cellpadding="0" cellspacing="0"` は除外します。

　
## テーマの定義について
現在、テーマの定義は、次のメソッドにてハードコーディングで行っています。
```php
BaserCore\Controller\Admin\BcAdminAppController::beforeRender()
```
