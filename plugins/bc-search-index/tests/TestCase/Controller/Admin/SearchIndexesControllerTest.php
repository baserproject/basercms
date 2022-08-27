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

namespace BcSearchIndex\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Controller\Admin\SearchIndexesController;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SearchIndexesControllerTest
 * @package BcSearchIndex\Test\TestCase\Controller\Admin
 * @property SearchIndexesController $SearchIndexesController
 */
class SearchIndexesControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Empty/Users',
        'plugin.BaserCore.Empty/Sites',
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
        parent::tearDown();
    }

    /**
     * testBeforeRender
     */
    public function testBeforeRender()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/bc-search-index/search_indexes/');
        $request = $this->loginAdmin($request);
        $searchIndexesController = new SearchIndexesController($request);
        $event = new Event('Controller.beforeRender', $searchIndexesController);
        $searchIndexesController->beforeRender($event);
        $this->assertEquals('BcSearchIndex.BcSearchIndex', $searchIndexesController->viewBuilder()->getHelpers()[0]);
    }

}
