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

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Controller\Admin\SearchIndexesController;
use BcSearchIndex\Service\SearchIndexesAdminServiceInterface;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use BcSearchIndex\Test\Factory\SearchIndexFactory;
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
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        SiteConfigFactory::make(['name' => 'content_types', 'value' => serialize(['Page'])])->persist();
        $request = $this->getRequest('/baser/admin/bc-search-index/search_indexes/');
        $request = $this->loginAdmin($request);
        $this->SearchIndexesController = new SearchIndexesController($request);
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
     * testBeforeRender
     */
    public function testBeforeRender()
    {
        $event = new Event('Controller.beforeRender', $this->SearchIndexesController);
        $this->SearchIndexesController->beforeRender($event);
        $this->assertEquals('BcSearchIndex.BcSearchIndex', $this->SearchIndexesController->viewBuilder()->getHelpers()[0]);
    }

    /**
     * test index
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/admin/bc-search-index/search_indexes/index');
        $this->assertResponseOk();

        // イベントテスト
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcSearchIndex.SearchIndexes.searchIndex', function (Event $event) {
            $request = $event->getData('request');
            return $request->withQueryParams(['num' => 1]);
        });
        // アクション実行（requestの変化を判定するため $this->get() ではなくクラスを直接利用）
        $this->SearchIndexesController->beforeFilter(new Event('beforeFilter'));
        $this->SearchIndexesController->index(
            $this->getService(SearchIndexesServiceInterface::class),
            $this->getService(SearchIndexesAdminServiceInterface::class),
            $this->getService(SiteConfigsServiceInterface::class)
        );
        $this->assertEquals(1, $this->SearchIndexesController->getRequest()->getQuery('num'));
    }

    /**
     * test delete
     * @return void
     */
    public function testDelete(){
        SearchIndexFactory::make(['id' => 1, 'title' => 'test data', 'type' => 'admin', 'site_id' => 1], 1)->persist();
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->delete('/baser/admin/bc-search-index/search_indexes/delete/1');
        $this->assertFlashMessage('検索インデックスより No.1 を削除しました。');
        $this->assertResponseSuccess();

        $this->assertRedirect([
            'plugin' => 'BcSearchIndex',
            'prefix' => 'Admin',
            'controller' => 'search_indexes',
            'action' => 'index'
        ]);
    }

}
