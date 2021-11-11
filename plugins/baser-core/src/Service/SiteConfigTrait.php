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

use BaserCore\Model\Entity\SiteConfig;
use BaserCore\Utility\BcContainerTrait;

/**
 * Class SiteConfigTrait
 * @package BaserCore\Service
 */
trait SiteConfigTrait
{
    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * サイト全体の設定値を取得する
     * @param string $name
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteConfig($name)
    {
        $siteConfigs = $this->getService(SiteConfigServiceInterface::class);
        return $siteConfigs->getValue($name);
    }

    /**
     * サイト全体の設定値を更新する
     *
     * @param  string $name
     * @param  string $value
     * @return SiteConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setSiteConfig($name, $value)
    {
        $siteConfigs = $this->getService(SiteConfigServiceInterface::class);
        return $siteConfigs->update([$name, $value]);
    }

    /**
     * サイト全体の設定値をリセットする
     *
     * @param  string $name
     * @return SiteConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetSiteConfig($name)
    {
        return $this->setSiteConfig($name, '');
    }
}
