<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

namespace BaserCore\Service\Admin;

use BaserCore\Model\Entity\UserGroup;

/**
 * Interface PermissionManageServiceInterface
 * @package BaserCore\Service\Admin
 */
interface PermissionManageServiceInterface
{

    /**
     * アクセス制限におけるメソッドのリストを取得
     * @return array
     */
    public function getMethodList(): array;

}
