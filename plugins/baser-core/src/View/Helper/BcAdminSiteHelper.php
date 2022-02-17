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

namespace BaserCore\View\Helper;

use Cake\ORM\Query;
use Cake\View\Helper;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Model\Entity\Site;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\SiteServiceInterface;
use BaserCore\Service\BcAdminServiceInterface;
use BaserCore\Service\SiteConfigServiceInterface;

/**
 * Class BcAdminSiteHelper
 * @package BaserCore\View\Helper
 */
class BcAdminSiteHelper extends Helper
{

    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * Sites Service
     * @var SiteServiceInterface
     */
    public $SiteService;

    /**
     * initialize
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->SiteService = $this->getService(SiteServiceInterface::class);
        $this->SiteConfigService = $this->getService(SiteConfigServiceInterface::class);
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
        return $this->SiteService->getDeviceList();
    }

    /**
     * デバイスリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLangList(): array
    {
        return $this->SiteService->getLangList();
    }

    /**
     * サイトのリストを取得
     * @param array $options
     *  - `excludeId` : 除外するサイトID（初期値：なし）
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteList($options = []): array
    {
        return $this->SiteService->getList($options);
    }

    /**
     * テーマのリストを取得
     * @param Site $site
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getThemeList($site): array
    {
        $themes = $this->SiteService->getThemeList($site);
        if(!$this->isMainOnCurrentDisplay($site)) {
            $defaultThemeName = __d('baser', 'メインサイトに従う');
            $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
            $mainTheme = $sites->getRootMain()->theme;
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
        return (bool) $this->SiteConfigService->getValue('use_site_device_setting');
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
        return (bool) $this->SiteConfigService->getValue('use_site_lang_setting');
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
     * URLよりサイトを取得する
     * @param $url
     * @return Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findByUrl($url): EntityInterface
    {
        return $this->SiteService->findByUrl($url);
    }

    /**
     * IDよりサイトを取得する
     *
     * @param string $id
     * @return Query
     */
    public function findById($id): Query
    {
        return $this->SiteService->findById($id);
    }

    /**
     * 現在の管理対象のサイトを取得する
     * @return Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentSite(): ?Site
    {
        return $this->SiteService->get(
            $this->_View->getRequest()->getAttribute('currentSite')->id
        );
    }

    /**
     * 現在の管理対象のサイト以外のリストを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getOtherSiteList(): array
    {
        return $this->SiteService->getList([
            'excludeIds' => $this->getCurrentSite()->id
        ]);
    }

}
