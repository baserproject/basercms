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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Model\Entity\Site;
use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;

/**
 * Class SiteManageServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property SiteManageServiceInterface $SiteManage
 */
class SiteManageServiceTest extends BcTestCase
{

    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.LoginStores',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs'
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SiteManage = $this->getService(SiteManageServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserGroups);
        parent::tearDown();
    }

    /**
     * Test getLangList
     */
    public function testGetLangList()
    {
        $langs = $this->SiteManage->getLangList();
        $this->assertEquals('english', key($langs));
    }

    /**
     * Test getDeviceList
     */
    public function testGetDeviceList()
    {
        $devices = $this->SiteManage->getDeviceList();
        $this->assertEquals('mobile', key($devices));
    }

    /**
     * Test getSiteList
     */
    public function testGetSiteList()
    {
        $this->assertEquals(4, count($this->SiteManage->getSiteList()));
        $this->SiteManage->create([
            'name' => 'test',
            'display_name' => 'test',
            'alias' => 'test',
            'title' => 'test',
            'status' => true
        ]);
        $this->assertEquals(5, count($this->SiteManage->getSiteList()));
    }

    /**
     * Test getThemeList
     */
    public function testGetThemeList()
    {
        // TODO BcUtil::getAllThemeList() を実装しないとテストができない
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test isUseSiteDeviceSetting
     */
    public function testIsUseSiteDeviceSetting()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // TODO テストの実装は完了したが、SiteConfigsMockService::value() を実装しないと動作しない
        $this->assertTrue($this->SiteManage->isUseSiteDeviceSetting());
        $siteConfigs = $this->getTableLocator()->get('BaserCore.SiteConfigs');
        $siteConfig = $siteConfigs->find()->where(['name' => 'use_site_device_setting'])->first();
        $siteConfig->value = false;
        $siteConfigs->save($siteConfig);
        $this->assertFalse($this->SiteManage->isUseSiteDeviceSetting());
    }

    /**
     * Test isUseSiteLangSetting
     */
    public function testIsUseSiteLangSetting()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // TODO テストの実装は完了したが、SiteConfigsMockService::value() を実装しないと動作しない
        $this->assertFalse($this->SiteManage->isUseSiteLangSetting());
        $siteConfigs = $this->getTableLocator()->get('BaserCore.SiteConfigs');
        $siteConfig = $siteConfigs->find()->where(['name' => 'use_site_lang_setting'])->first();
        $siteConfig->value = true;
        $siteConfigs->save($siteConfig);
        $this->assertTrue($this->SiteManage->isUseSiteLangSetting());
    }

    /**
     * test isMainOnCurrentDisplay
     */
    public function testIsMainOnCurrentDisplay()
    {
        $site = new Site(['id' => 1, 'main_site_id' => null]);
        $this->assertTrue($this->SiteManage->isMainOnCurrentDisplay($site));
        $site = new Site(['id' => 1, 'main_site_id' => null]);
        $this->getRequest('/baser/admin/baser-core/sites/add');
        $this->assertFalse($this->SiteManage->isMainOnCurrentDisplay($site));
        $site = new Site(['id' => 2, 'main_site_id' => 1]);
        $this->assertFalse($this->SiteManage->isMainOnCurrentDisplay($site));
    }

}
