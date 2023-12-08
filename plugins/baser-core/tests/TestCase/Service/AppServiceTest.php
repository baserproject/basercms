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

use BaserCore\Service\AppService;
use BaserCore\Service\SitesService;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Routing\Router;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * AppServiceTest
 */
class AppServiceTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * AppService
     * @var AppService
     */

    public $AppService;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(SitesScenario::class);
        $this->AppService = new AppService();
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->AppService);
        parent::tearDown();
    }


    /**
     * Test getCurrentSite
     */
    public function testGetCurrentSite()
    {
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
        $service = new SitesService();
        $site = $service->create([
            'main_site_id' => 2,
            'display_name' => 'beforeChange',
            'alias' => 'test',
            'name'  => 'test',
            'title' => 'test',
        ]);
        $request = $request->withAttribute('currentSite', $service->get($site->id));
        Router::setRequest($request);
        $this->assertEquals('beforeChange', $this->AppService->getCurrentSite()->display_name);
        $service->update($site,['display_name' => 'afterChange']);
        $this->assertEquals('afterChange', $this->AppService->getCurrentSite()->display_name);
    }

    /**
     * test getOtherSiteList
     */
    public function test_getOtherSiteList()
    {
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
        $service = new SitesService();
        $request = $request->withAttribute('currentSite', $service->get(3));
        Router::setRequest($request);

        $rs = $this->AppService->getOtherSiteList();
        $this->assertEquals('英語サイト', $this->AppService->getCurrentSite()->display_name);
        $this->assertEquals([1 => "メインサイト", 2 => 'スマホサイト', 4 => "別ドメイン", 5 => "サブドメイン", 6 => "関連メインサイト用"], $rs);

        $request = $request->withAttribute('currentSite', $service->get(6));
        Router::setRequest($request);

        $rs = $this->AppService->getOtherSiteList();
        $this->assertEquals('関連メインサイト用', $this->AppService->getCurrentSite()->display_name);
        $this->assertEquals([1 => "メインサイト", 2 => 'スマホサイト', 3 => "英語サイト", 4 => "別ドメイン", 5 => "サブドメイン"], $rs);
    }

    /**
     * test getViewVarsForAll
     */
    public function test_getViewVarsForAll()
    {
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $vars = $this->AppService->getViewVarsForAll();
        $this->assertTrue(isset($vars['currentSite']));
        $this->assertTrue(isset($vars['otherSites']));
        $this->assertTrue(isset($vars['loginUser']));
        $this->assertTrue(isset($vars['currentAdminTheme']));
    }


}
