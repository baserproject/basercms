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

use BaserCore\Utility\BcContainerTrait;

/**
 * Class SiteConfigsTrait
 * @package BaserCore\Service
 */
trait SiteConfigsTrait
{
    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * 管理画面の一覧の表示件数を取得する
     * @return mixed
     */
    public function getAdminListNum(): int
    {
        $siteConfigs = $this->getService(SiteConfigsServiceInterface::class);
        return (int)$siteConfigs->value('admin_list_num');
    }
}
