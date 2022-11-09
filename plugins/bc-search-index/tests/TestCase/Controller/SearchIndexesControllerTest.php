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

namespace BcSearchIndex\Test\TestCase\Controller;

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SearchIndexesSearchScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Controller\SearchIndexesController;
use BcSearchIndex\Service\Front\SearchIndexesFrontServiceInterface;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SearchIndexesControllerTest
 *
 * @property SearchIndexesController $SearchIndexesController
 */
class SearchIndexesControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/SearchIndexes',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexesController);
        parent::tearDown();
    }

    /**
     * test search
     */
    public function test_search()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SearchIndexesSearchScenario::class);

        $request = $this->getRequest()->withAttribute('currentSite', SiteFactory::get(1));
        $controller = new SearchIndexesController($request);
        $service = $this->getService(SearchIndexesFrontServiceInterface::class);

        $controller->setRequest($this->getRequest()->withQueryParams(['q' => '']));
        $controller->search($service);
        $vars = $controller->viewBuilder()->getVars();
        $this->assertArrayHasKey('searchIndexes', $vars);
        $this->assertArrayHasKey('query', $vars);
        $this->assertArrayHasKey('contentFolders', $vars);
        $this->assertArrayHasKey('searchIndexesFront', $vars);

        // 並び順 - id: 昇順
        $controller->setRequest($this->getRequest()->withQueryParams(['site_id' => 1, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('test data 1', $rs[0]['title']);
        $this->assertEquals('test data 2', $rs[1]['title']);

        // 並び順 - priority: 降順
        $controller->setRequest($this->getRequest()->withQueryParams(['s' => 2, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('test data 4', $rs[0]['title']);
        $this->assertEquals('test data 3', $rs[1]['title']);

        // 並び順 - modified: 降順
        $controller->setRequest($this->getRequest()->withQueryParams(['site_id' => 3, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('test data 6', $rs[0]['title']);
        $this->assertEquals('test data 5', $rs[1]['title']);

        // その他条件(createIndexConditions)
        $controller->setRequest($this->getRequest()->withQueryParams(['keyword' => 'inc', 's' => 4, 'q' => 'inc']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['content_id' => 2, 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['content_filter_id' => 3, 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['type' => 'ページ', 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['model' => 'Page', 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['priority' => 0.5, 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['status' => 1, 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['folder_id' => 1, 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['f' => 1, 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['c' => 2, 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['cf' => 3, 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);

        $controller->setRequest($this->getRequest()->withQueryParams(['m' => 'Page', 's' => 4, 'q' => '']));
        $controller->search($service);
        $rs = $controller->viewBuilder()->getVars()['searchIndexes']->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
    }

}
