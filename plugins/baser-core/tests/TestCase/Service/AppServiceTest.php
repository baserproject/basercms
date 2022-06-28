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
use BaserCore\TestSuite\BcTestCase;
use Cake\Routing\Router;

/**
 * AppServiceTest
 */
class AppServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.SiteConfigs',
    ];

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
         $this->markTestIncomplete('テストが未実装です');
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
