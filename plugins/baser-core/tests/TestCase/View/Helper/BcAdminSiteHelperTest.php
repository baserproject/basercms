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
use BaserCore\Service\SiteConfigServiceInterface;
use BaserCore\Service\SiteService;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAdminSiteHelper;
use Cake\Http\ServerRequest;

/**
 * Class BcAdminSiteHelperTest
 *
 * @package BaserCore\Test\TestCase\View\Helper
 */
class BcAdminSiteHelperTest extends \BaserCore\TestSuite\BcTestCase
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
        $siteConfigService = $this->getService(SiteConfigServiceInterface::class);
        $siteConfig = $siteConfigService->get();
        $siteConfig->use_site_device_setting = false;
        $siteConfigService->update($siteConfig->toArray());
        $this->assertFalse($this->BcAdminSite->isUseSiteDeviceSetting());
    }

    /**
     * Test isUseSiteLangSetting
     */
    public function testIsUseSiteLangSetting()
    {
        $this->assertFalse($this->BcAdminSite->isUseSiteLangSetting());
        $siteConfigService = $this->getService(SiteConfigServiceInterface::class);
        $siteConfig = $siteConfigService->get();
        $siteConfig->use_site_lang_setting = true;
        $siteConfigService->update($siteConfig->toArray());
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

    /**
     * Test getCurrentSite
     */
    public function testGetCurrentSite()
    {
        $request = $this->getRequest('/');
        $service = new SiteService();
        $site = $service->create([
            'main_site_id' => 2,
            'display_name' => 'beforeChange',
            'alias' => 'test',
            'name'  => 'test',
            'title' => 'test',
        ]);
        $request = $request->withAttribute('currentSite', $service->get($site->id));
        $this->BcAdminSite = new BcAdminSiteHelper(new BcAdminAppView($request));
        $this->assertEquals('beforeChange', $this->BcAdminSite->getCurrentSite()->display_name);
        $service->update($site,['display_name' => 'afterChange']);
        $this->assertEquals('afterChange', $this->BcAdminSite->getCurrentSite()->display_name);
    }
}
