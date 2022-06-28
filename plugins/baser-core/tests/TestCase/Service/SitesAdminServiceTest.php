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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Model\Entity\Site;
use BaserCore\Service\SitesAdminService;
use BaserCore\Utility\BcContainerTrait;

/**
 * Class SitesAdminServiceTest
 */
class SitesAdminServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Sites'
    ];

//    public $autoFixtures = false;

    /**
     * SitesAdminService
     * @var SitesAdminService
     */
    public $SitesAdmin;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SitesAdmin = new SitesAdminService();
//        $this->fixtureStrategy->setupTest($this->getFixtures());
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->SitesAdmin);
        parent::tearDown();
    }

    /**
     * test isMainOnCurrentDisplay
     */
    public function testIsMainOnCurrentDisplay()
    {
        $site = new Site(['id' => 1, 'main_site_id' => null]);
        $this->assertTrue($this->SitesAdmin->isMainOnCurrentDisplay($site));
        $site = new Site(['id' => 1, 'main_site_id' => null]);
        $this->getRequest('/baser/admin/baser-core/sites/add');
        $this->assertFalse($this->SitesAdmin->isMainOnCurrentDisplay($site));
        $site = new Site(['id' => 2, 'main_site_id' => 1]);
        $this->assertFalse($this->SitesAdmin->isMainOnCurrentDisplay($site));
    }

    /**
     * Test getSelectableThemes
     */
    public function test_getSelectableThemes()
    {
        // TODO ucmitz BcUtil::getAllThemeList() を実装しないとテストができない
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $sites = $this->SitesAdmin->getIndex([])->all();
        $vars = $this->SitesAdmin->getViewVarsForIndex($sites);
        $this->assertTrue(isset($vars['sites']));
        $this->assertTrue(isset($vars['deviceList']));
        $this->assertTrue(isset($vars['langList']));
        $this->assertTrue(isset($vars['siteList']));
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        $site = $this->SitesAdmin->get(1);
        $vars = $this->SitesAdmin->getViewVarsForAdd($site);
        $this->assertTrue(isset($vars['site']));
        $this->assertTrue(isset($vars['isMainOnCurrentDisplay']));
        $this->assertTrue(isset($vars['useSiteDeviceSetting']));
        $this->assertTrue(isset($vars['useSiteLangSetting']));
        $this->assertTrue(isset($vars['selectableLangs']));
        $this->assertTrue(isset($vars['selectableDevices']));
        $this->assertTrue(isset($vars['selectableThemes']));
        $this->assertTrue(isset($vars['siteList']));
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $site = $this->SitesAdmin->get(1);
        $vars = $this->SitesAdmin->getViewVarsForEdit($site);
        $this->assertTrue(isset($vars['site']));
        $this->assertTrue(isset($vars['isMainOnCurrentDisplay']));
        $this->assertTrue(isset($vars['useSiteDeviceSetting']));
        $this->assertTrue(isset($vars['useSiteLangSetting']));
        $this->assertTrue(isset($vars['selectableLangs']));
        $this->assertTrue(isset($vars['selectableDevices']));
        $this->assertTrue(isset($vars['selectableThemes']));
        $this->assertTrue(isset($vars['siteList']));
    }

}
