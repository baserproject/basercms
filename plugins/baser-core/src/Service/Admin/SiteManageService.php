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

namespace BaserCore\Service\Admin;

use BaserCore\Service\SiteConfigsTrait;
use BaserCore\Service\SitesService;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;

/**
 * Class UserGroupManageService
 * @package BaserCore\Service
 */
class SiteManageService extends SitesService implements SiteManageServiceInterface
{

    /**
     * Trait
     */
    use SiteConfigsTrait;

    /**
     * 言語リストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLangList(): array
    {
        $languages = Configure::read('BcLang');
        $langs = [];
        foreach($languages as $key => $lang) {
            $langs[$key] = $lang['name'];
        }
        return $langs;
    }

    /**
     * デバイスリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDeviceList(): array
    {
        $agents = Configure::read('BcAgent');
        $devices = [];
        foreach($agents as $key => $agent) {
            $devices[$key] = $agent['name'];
        }
        return $devices;
    }

    /**
     * サイトのリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteList(): array
    {
        return $this->Sites->getSiteList();
    }

    /**
     * テーマのリストを取得する
     * @return array
     */
    public function getThemeList(): array
    {
        $defaultThemeName = __d('baser', 'メインサイトに従う');
        $mainTheme = $this->Sites->getRootMain(['theme'])['theme'];
        if (!empty($this->siteConfigs['theme'])) {
            $defaultThemeName .= '（' . $mainTheme . '）';
        }
        $themes = BcUtil::getThemeList();
        if (in_array($mainTheme, $themes)) {
            unset($themes[$mainTheme]);
        }
        return array_merge(['' => $defaultThemeName], $themes);
    }

    /**
     * デバイス設定を利用するかどうか
     * @return bool
     */
    public function isUseSiteDeviceSetting(): bool
    {
        return (bool) $this->getSiteConfig('use_site_device_setting');
    }

    /**
     * 言語設定を利用するかどうか
     * @return bool
     */
    public function isUseSiteLangSetting(): bool
    {
        return (bool) $this->getSiteConfig('use_site_lang_setting');
    }

}
