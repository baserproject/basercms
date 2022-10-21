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

namespace BaserCore\Service\Admin;

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\SitesService;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Datasource\EntityInterface;
use Cake\Routing\Router;

/**
 * SitesAdminService
 */
class SitesAdminService extends SitesService implements SitesAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 一覧画面用のデータを取得する
     * @param \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface $sites
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex($sites): array
    {
        return [
            'sites' => $sites,
            'deviceList' => $this->getDeviceList(),
            'langList' => $this->getLangList(),
            'siteList' => $this->getList(['status' => null])
        ];
    }

    /**
     * 編集画面用のデータを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $site): array
    {
        return [
            'site' => $site,
            'isMainOnCurrentDisplay' => $this->isMainOnCurrentDisplay($site),
            'useSiteDeviceSetting' => (bool) $this->getService(SiteConfigsServiceInterface::class)->getValue('use_site_device_setting'),
            'useSiteLangSetting' => (bool) $this->getService(SiteConfigsServiceInterface::class)->getValue('use_site_lang_setting'),
            'selectableLangs' => $this->getSelectableLangs($site->main_site_id, $site->id),
            'selectableDevices' => $this->getSelectableDevices($site->main_site_id, $site->id),
            'selectableThemes' => $this->getSelectableThemes($site),
            'siteList' => $this->getList(['excludeIds' => $site->id, 'status' => null])
        ];
    }

    /**
     * 編集画面用のデータを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForAdd(EntityInterface $site): array
    {
        return [
            'site' => $site,
            'isMainOnCurrentDisplay' => $this->isMainOnCurrentDisplay($site),
            'useSiteDeviceSetting' => (bool) $this->getService(SiteConfigsServiceInterface::class)->getValue('use_site_device_setting'),
            'useSiteLangSetting' => (bool) $this->getService(SiteConfigsServiceInterface::class)->getValue('use_site_lang_setting'),
            'selectableLangs' => $this->getSelectableLangs($site->main_site_id, $site->id),
            'selectableDevices' => $this->getSelectableDevices($site->main_site_id, $site->id),
            'selectableThemes' => $this->getSelectableThemes($site),
            'siteList' => $this->getList(['status' => null])
        ];
    }

    /**
     * 現在の画面で表示しているものがメインサイトかどうか
     * @param EntityInterface $site
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
     * テーマのリストを取得
     * @param EntityInterface $site
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSelectableThemes($site): array
    {
        $themes = $this->getThemeList();
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

}
