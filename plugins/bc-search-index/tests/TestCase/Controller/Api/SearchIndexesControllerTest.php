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

namespace BcSearchIndex\Test\TestCase\Controller\Api;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SearchIndexesSearchScenario;
use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Controller\Api\SearchIndexesController;
use BcSearchIndex\Service\SearchIndexesService;
use BcSearchIndex\Test\Factory\SearchIndexFactory;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SearchIndexesControllerTest
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
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/SearchIndexes',
        'plugin.BaserCore.Factory/Dblogs',
        'plugin.BaserCore.Factory/Contents',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin();
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test beforeFilter
     * @return void
     */
    public function testBeforeFilter()
    {
        $request = $this->getRequest('/baser/admin/bc-search-index/search_indexes/');
        $request = $this->loginAdmin($request);
        $searchIndexes = new SearchIndexesController($request);

        $event = new Event('filter');
        $searchIndexes->beforeFilter($event);
        $this->assertFalse($searchIndexes->Security->getConfig('validatePost'));
    }

    /**
     * test change_priority
     * @return void
     */
    public function testChangePriority()
    {
        SearchIndexFactory::make(['id' => 1, 'title' => 'test data', 'type' => 'admin', 'site_id' => 1], 1)->persist();
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/api/bc-search-index/search_indexes/change_priority/1.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $data = [
            'priority' => 10
        ];
        $this->post('/baser/api/bc-search-index/search_indexes/change_priority/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());

        $this->assertEquals('検索インデックス「test data」の優先度を変更しました。', $result->message);
        $this->assertEquals(10, $result->searchIndex->priority);

    }

    /**
     * test reconstruct
     * @return void
     */
    public function testReconstruct()
    {
        $this->post('/baser/api/bc-search-index/search_indexes/reconstruct.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('検索インデックスの再構築に成功しました。', $result->message);

        $this->get('/baser/api/bc-search-index/search_indexes/reconstruct.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);
    }

    /**
     * test index
     * @return void
     */
    public function testIndex(){

        $this->loadFixtureScenario(SearchIndexesSearchScenario::class);

        // `limit`: 取得件数
        $query = http_build_query(['limit' => 2, 'site_id' => 1, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->searchIndexes);

        // keyword(q): 検索キーワード
        $query = http_build_query(['keyword' => 'inc', 'site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);
        $query = http_build_query(['q' => 'inc', 's' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);

        // site_id(s): サイトID
        $query = http_build_query(['site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);
        $query = http_build_query(['s' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);

        // content_id(c): コンテンツID
        $query = http_build_query(['content_id' => 2, 'site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);
        $query = http_build_query(['c' => 2, 's' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);

        // content_filter_id(cf): コンテンツフィルダーID
        $query = http_build_query(['content_filter_id' => 3, 'site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);
        $query = http_build_query(['cf' => 3, 's' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);

        // type: コンテンツタイプ
        $query = http_build_query(['type' => 'ページ', 'site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);

        // model(m): モデル名（エンティティ名）
        $query = http_build_query(['model' => 'Page', 'site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);
        $query = http_build_query(['m' => 'Page', 's' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);

        // priority: 優先度
        $query = http_build_query(['priority' => 0.5, 'site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);

        // folder_id(f): フォルダーID
        $query = http_build_query(['folder_id' => 1, 'site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);
        $query = http_build_query(['f' => 1, 'site_id' => 4, 'token' => $this->accessToken]);
        $this->get('/baser/api/bc-search-index/search_indexes/index.json?' . $query);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('会社案内', $result->searchIndexes[0]->title);
    }

    /**
     * test delete
     * @return void
     */
    public function testDelete()
    {
        SearchIndexFactory::make(['id' => 3, 'title' => 'test data delete', 'type' => 'admin', 'site_id' => 0], 1)->persist();

        $this->post('/baser/api/bc-search-index/search_indexes/delete/3.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('test data delete', $result->searchIndex->title);
        $this->assertEquals('検索インデックス: test data delete を削除しました。', $result->message);

        $this->get('/baser/api/bc-search-index/search_indexes/delete/3.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->post('/baser/api/bc-search-index/search_indexes/delete/0.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test batch
     * @return void
     */
    public function testBatch()
    {
        $this->post('/baser/api/bc-search-index/search_indexes/batch.json?token=' . $this->accessToken, []);
        $this->assertResponseFailure();

        SearchIndexFactory::make(['id' => 1, 'title' => 'test data Batch 1', 'type' => 'admin', 'site_id' => 10], 1)->persist();
        SearchIndexFactory::make(['id' => 2, 'title' => 'test data Batch 2', 'type' => 'admin', 'site_id' => 10], 1)->persist();
        SearchIndexFactory::make(['id' => 3, 'title' => 'test data Batch 3', 'type' => 'admin', 'site_id' => 10], 1)->persist();

        $data = [
            'batch' => 'delete',
            'batch_targets' => [1, 2, 3],
        ];

        $this->post('/baser/api/bc-search-index/search_indexes/batch.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);

        $searchIndexesService = new SearchIndexesService();
        $searchIndexes = $searchIndexesService->getIndex(['site_id' => 10])->all();
        $this->assertEquals(0, count($searchIndexes));
    }

}
