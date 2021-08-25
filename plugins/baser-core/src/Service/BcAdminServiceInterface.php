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

use BaserCore\Model\Entity\Site;

/**
 * BcAdminService
 */
Interface BcAdminServiceInterface
{

    /**
     * 現在の管理対象のサイトを設定する
     */
    public function setCurrentSite(): void;

    /**
     * 現在の管理対象のサイトを取得する
     * @return Site
     */
    public function getCurrentSite(): Site;

    /**
     * 現在の管理対象のサイト以外のリストを取得する
     * @return array
     */
    public function getOtherSiteList(): array;

}
