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

}
