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

use BaserCore\Model\Entity\Site;
use BaserCore\Service\SiteConfigsTrait;
use BaserCore\Service\SitesService;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Routing\Router;

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
     * @param array $options
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteList($options = []): array
    {
        return $this->Sites->getList(null, $options);
    }

    /**
     * テーマのリストを取得する
     * @param Site $site
     * @return array
     * @checked
     * @noTodo
     */
    public function getThemeList(Site $site): array
    {
        $themes = BcUtil::getThemeList();
        if(!$this->isMainOnCurrentDisplay($site)) {
            $defaultThemeName = __d('baser', 'メインサイトに従う');
            $mainTheme = $this->Sites->getRootMain()->theme;
            if (!empty($mainTheme)) {
                if (in_array($mainTheme, $themes)) {
                    unset($themes[$mainTheme]);
                }
                $defaultThemeName .= '（' . $mainTheme . '）';
            }
            $themes = array_merge(['' => $defaultThemeName], $themes);
        }
        return $themes;
    }

    /**
     * デバイス設定を利用するかどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isUseSiteDeviceSetting(): bool
    {
        return (bool) $this->getSiteConfig('use_site_device_setting');
    }

    /**
     * 言語設定を利用するかどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isUseSiteLangSetting(): bool
    {
        return (bool) $this->getSiteConfig('use_site_lang_setting');
    }

    /**
     * 現在の画面で表示しているものがメインサイトかどうか
     * @param Site $site
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isMainOnCurrentDisplay($site): bool
    {
        if(!empty($site->main_site_id)) {
            return false;
        }
        $request = Router::getRequest();
        if(!$request) {
            return true;
        }
        if($request->getParam('controller') === 'Sites' && $request->getParam('action') === 'add') {
            return false;
        }
        return true;
    }

    /**
     * 現在の管理対象のサイトを設定する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setCurrentSite(): void
    {
        $request = Router::getRequest();
        $session = $request->getSession();
        $currentSiteId = 1;
        $queryCurrentSiteId = $request->getQuery('current_site_id');
        if (!$session->check('BcApp.Admin.currentSite') || $queryCurrentSiteId) {
            if ($queryCurrentSiteId) {
                $currentSiteId = $queryCurrentSiteId;
            }
            $session->write('BcApp.Admin.currentSite', $this->Sites->get($currentSiteId));
        }
    }

    /**
     * 現在の管理対象のサイトを取得する
     * @return Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentSite(): Site
    {
        $site = Router::getRequest()->getSession()->read('BcApp.Admin.currentSite');
        if(!$site) {
            $site = $this->Sites->getRootMain();
        }
        return $site;
    }

    /**
     * 現在の管理対象のサイト以外のリストを取得する
     * @return array
     */
    public function getOtherSiteList(): array
    {
        return $this->getSiteList(['excludeIds' => [$this->getCurrentSite()->id]]);
    }

}
