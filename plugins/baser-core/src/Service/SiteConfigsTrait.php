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
     * サイト全体の設定値を取得する
     * @param string $name
     * @return mixed
     */
    public function getSiteConfig($name)
    {
        $siteConfigs = $this->getService(SiteConfigsServiceInterface::class);
        return (int)$siteConfigs->value($name);
    }

}
