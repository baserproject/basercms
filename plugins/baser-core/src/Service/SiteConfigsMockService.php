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
 * Class SiteConfigsMockService
 * @package BaserCore\Service
 */
class SiteConfigsMockService implements SiteConfigsServiceInterface
{
    /**
     * フィールドの値を取得する
     * @param $fieldName
     * @return mixed
     */
    public function value($fieldName)
    {
        // TODO 未実装
        if ($fieldName === 'admin_list_num') {
            return 30;
        } elseif($fieldName === 'name') {
            return 'baserCMS';
        }
        return null;
    }
}
