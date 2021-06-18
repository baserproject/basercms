<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

/**
 * Class PermissionsMockService
 * @package BaserCore\Service
 */
class PermissionsMockService implements PermissionsServiceInterface
{
    /**
     * URLの権限チェックを行う
     * @param string $url
     * @param array $userGroupId
     * @return bool
     */
    public function check($url, $userGroupId): bool
    {
        // TODO 未実装
        return true;
    }

}
