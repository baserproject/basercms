# コーディングの基本

## ヘッダーコメント

### プログラムコードのヘッダー
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

### クラスヘッダー
つける
```php
/**
 * Class BcAdminAppController
 * @package BaserCore\Controller\Admin
 */
```

## クラスメンバー変数へのアクセス

メンバー変数への直接アクセスはせず、セッター、ゲッターを配置するようにする。
