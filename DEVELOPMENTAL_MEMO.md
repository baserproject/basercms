# 開発メモ

## プログラムコードのヘッダー
以下に統一
```php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
```

## クラスヘッダー
つける
```php
/**
 * Class BcAdminAppController
 * @package BaserCore\Controller\Admin
 */
```

## フォームコントロールのテンプレート
`/baser-core/config/bc_form.php` で定義できる

## フォームコントロールの出力
`$this->BcForm->input()` から、`$this->BcAdminForm->control()` に変更

