<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Utility;

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

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
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function get($key)
    {
        $siteConfig = BcContainer::get()->get(SiteConfigsServiceInterface::class);
        return $siteConfig->getValue($key);
    }

}
