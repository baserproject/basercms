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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\Model\Entity\Site;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAdminSiteHelper;

/**
 * Class BcAdminSiteHelperTest
 *
 * @package BaserCore\Test\TestCase\View\Helper
 */
class BcAdminSiteHelperTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SiteConfigs'
    ];

    /**
     * BcAdminSiteHelper
     * @var BcAdminSiteHelper
     */

    public $BcAdminSite;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminSite = new BcAdminSiteHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcAdminSite);
        parent::tearDown();
    }


    /**
     * Test isUseSiteDeviceSetting
     */
    public function testIsUseSiteDeviceSetting()
    {
        $this->assertTrue($this->BcAdminSite->isUseSiteDeviceSetting());
        $siteConfigs = $this->getTableLocator()->get('BaserCore.SiteConfigs');
        $siteConfig = $siteConfigs->find()->where(['name' => 'use_site_device_setting'])->first();
        $siteConfig->value = false;
        $siteConfigs->save($siteConfig);
        $this->assertFalse($this->BcAdminSite->isUseSiteDeviceSetting());
    }

    /**
     * Test isUseSiteLangSetting
     */
    public function testIsUseSiteLangSetting()
    {
        $this->assertFalse($this->BcAdminSite->isUseSiteLangSetting());
        $siteConfigs = $this->getTableLocator()->get('BaserCore.SiteConfigs');
        $siteConfig = $siteConfigs->find()->where(['name' => 'use_site_lang_setting'])->first();
        $siteConfig->value = true;
        $siteConfigs->save($siteConfig);
        $this->assertTrue($this->BcAdminSite->isUseSiteLangSetting());
    }

    /**
     * test isMainOnCurrentDisplay
     */
    public function testIsMainOnCurrentDisplay()
    {
        $site = new Site(['id' => 1, 'main_site_id' => null]);
        $this->assertTrue($this->BcAdminSite->isMainOnCurrentDisplay($site));
        $site = new Site(['id' => 1, 'main_site_id' => null]);
        $this->getRequest('/baser/admin/baser-core/sites/add');
        $this->assertFalse($this->BcAdminSite->isMainOnCurrentDisplay($site));
        $site = new Site(['id' => 2, 'main_site_id' => 1]);
        $this->assertFalse($this->BcAdminSite->isMainOnCurrentDisplay($site));
    }

    /**
     * Test getThemeList
     */
    public function testGetThemeList()
    {
        // TODO BcUtil::getAllThemeList() を実装しないとテストができない
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
