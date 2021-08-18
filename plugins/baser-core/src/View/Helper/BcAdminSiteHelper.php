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

use BaserCore\Model\Entity\Site;
use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Datasource\EntityInterface;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcAdminSiteHelper
 * @package BaserCore\View\Helper
 */
class BcAdminSiteHelper extends Helper
{

    use BcContainerTrait;

    /**
     * User Manage Service
     * @var SiteManageServiceInterface
     */
    public $SiteManage;

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
        $this->SiteManage = $this->getService(SiteManageServiceInterface::class);
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
        return $this->SiteManage->getDeviceList();
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
        return $this->SiteManage->getLangList();
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
        return $this->SiteManage->getSiteList($options);
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
        return $this->SiteManage->getThemeList($site);
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
        return $this->SiteManage->isUseSiteDeviceSetting();
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
        return $this->SiteManage->isUseSiteLangSetting();
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
        return $this->SiteManage->isMainOnCurrentDisplay($site);
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
        return $this->SiteManage->findByUrl($url);
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
        return $this->SiteManage->getCurrentSite();
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
        return $this->SiteManage->getOtherSiteList();
    }

}
