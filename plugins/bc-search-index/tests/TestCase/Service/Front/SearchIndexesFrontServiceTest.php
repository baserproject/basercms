<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSearchIndex\Test\TestCase\Service\Front;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Controller\SearchIndexesController;
use BcSearchIndex\Service\Front\SearchIndexesFrontServiceInterface;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SearchIndexesFrontServiceTest
 * @package BcSearchIndex\Test\TestCase\Service\Front
 */
class SearchIndexesFrontServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/SearchIndexes',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test getViewVarsForSearch
     *
     * @return void
     */
    public function test_getViewVarsForSearch(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make(['site_id' => 1, 'site_root' => true])->persist();
        $controller = new SearchIndexesController($this->getRequest());
        $service = $this->getService(SearchIndexesServiceInterface::class);
        $frontService = $this->getService(SearchIndexesFrontServiceInterface::class);
        $searchIndexes = $controller->paginate($service->getIndex());
        $request = $this->getRequest()
            ->withAttribute('currentSite', SiteFactory::get(1))
            ->withQueryParams(['q' => 'test']);
        $vars = $frontService->getViewVarsForSearch($searchIndexes, $request);
        $this->assertTrue(isset($vars['searchIndexes']));
        $this->assertTrue(isset($vars['query']));
        $this->assertTrue(isset($vars['contentFolders']));
        $this->assertTrue(isset($vars['searchIndexesFront']));
    }

}
