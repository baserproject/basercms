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
namespace BaserCore\Utility;

use BaserCore\Service\SiteConfigServiceInterface;

/**
 * BcSiteConfig
 */
class BcSiteConfig
{

    /**
     * Get
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        $siteConfig = BcContainer::get()->get(SiteConfigServiceInterface::class);
        return $siteConfig->getValue($key);
    }

}
