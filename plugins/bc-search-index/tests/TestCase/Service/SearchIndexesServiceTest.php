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

namespace BcSearchIndex\Test\TestCase\Service;

use BaserCore\Model\Table\ContentsTable;
use BaserCore\Test\Scenario\SearchIndexesSearchScenario;
use BcSearchIndex\Model\Table\SearchIndexesTable;
use BcSearchIndex\Service\SearchIndexesService;
use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Test\Factory\SearchIndexFactory;
use BcSearchIndex\Test\Scenario\Service\SearchIndexesServiceScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SearchIndexesServiceTest
 * @property SearchIndexesService $SearchIndexesService
 * @property SearchIndexesTable $SearchIndexes
 * @property ContentsTable $Contents
 */
class SearchIndexesServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Pages',
        'plugin.BaserCore.Factory/SiteConfigs',
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
        $this->SearchIndexesService = new SearchIndexesService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexesService);
        parent::tearDown();
    }

    /**
     * @test construct
     * @return void
     */
    public function testConstruct(){
        $this->assertTrue(isset($this->SearchIndexesService->SearchIndexes));
    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        SearchIndexFactory::make(['id' => 1, 'url' => '/about'])->persist();
        $searchIndex = $this->SearchIndexesService->get(1);
        $this->assertEquals('/about', $searchIndex->url);
    }

	/**
	 * 検索インデックスを再構築する
	 */
	public function testReconstruct()
	{
	    $this->loadFixtureScenario(SearchIndexesServiceScenario::class);
		$this->loginAdmin($this->getRequest());
		// 全ページ再構築
		$this->SearchIndexesService->reconstruct();
		$searchIndexesTable = $this->getTableLocator()->get('SearchIndexes');
		$this->assertEquals(3, $searchIndexesTable->find()->count());
		// 指定ディレクトリ配下再構築
		$contentsTable = $this->getTableLocator()->get('Contents');
		$content = $contentsTable->find()->where(['url' => '/service/'])->first();
		$this->SearchIndexesService->reconstruct($content->id);
		$this->assertEquals(2, $searchIndexesTable->find()->where(['url LIKE' => '/service/%'])->count());
	}

    /**
     * test changePriority
     * @return void
     */
    public function testChangePriority()
    {
        SearchIndexFactory::make(1)
            ->setField('priority', 1)
            ->setField('status', 1)
            ->persist();
        $data = $this->SearchIndexesService->getIndex([])->first();
        $expected = 10;
        $rs = $this->SearchIndexesService->changePriority($data, $expected);
        $this->assertEquals($expected, $rs['priority']);
    }

    /**
     * test delete
     * @return void
     */
    public function testDelete()
    {
        SearchIndexFactory::make(['id' => 1, 'title' => 'test data delete', 'type' => 'admin', 'site_id' => 1], 1)->persist();

        $data = $this->SearchIndexesService->get(1);
        $rs = $this->SearchIndexesService->delete(1);
        $this->assertTrue($rs);

        $searchIndexes = $this->SearchIndexesService->getIndex(['site_id' => $data['site_id'], 'keyword' => $data['title']])->first();
        $this->assertNull($searchIndexes);
    }

    /**
     * test getIndex
     * @return void
     */
    public function testGetIndex(): void
    {
        $this->loadFixtureScenario(SearchIndexesSearchScenario::class);

        $rs = $this->SearchIndexesService->getIndex(['limit' => 2, 'site_id' => 1])->toArray();
        // `limit`: 取得件数
        $this->assertCount(2, $rs);
        // 並び順 - id: 昇順
        $this->assertEquals('test data 1', $rs[0]['title']);
        $this->assertEquals('test data 2', $rs[1]['title']);

        // 並び順 - priority: 降順
        $rs = $this->SearchIndexesService->getIndex(['site_id' => 2])->toArray();
        $this->assertEquals('test data 4', $rs[0]['title']);
        $this->assertEquals('test data 3', $rs[1]['title']);

        // 並び順 - modified: 降順
        $rs = $this->SearchIndexesService->getIndex(['site_id' => 3])->toArray();
        $this->assertEquals('test data 6', $rs[0]['title']);
        $this->assertEquals('test data 5', $rs[1]['title']);

        // その他条件(createIndexConditions)
        $rs = $this->SearchIndexesService->getIndex(['keyword' => 'inc', 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['site_id' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['content_id' => 2, 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['content_filter_id' => 3, 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['type' => 'ページ', 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['model' => 'Page', 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['priority' => 0.5, 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['status' => 1, 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['folder_id' => 1, 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['cf' => 3, 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['m' => 'Page', 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['s' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['c' => 2, 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['f' => 1, 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
        $rs = $this->SearchIndexesService->getIndex(['q' => 'inc', 's' => 4])->toArray();
        $this->assertEquals('会社案内', $rs[0]['title']);
    }

    /**
     * test batch
     * @return void
     */
    public function testBatch()
    {
        SearchIndexFactory::make(['id' => 1, 'title' => 'test data Batch 1', 'type' => 'admin', 'site_id' => 5], 1)->persist();
        SearchIndexFactory::make(['id' => 2, 'title' => 'test data Batch 2', 'type' => 'admin', 'site_id' => 5], 1)->persist();
        SearchIndexFactory::make(['id' => 3, 'title' => 'test data Batch 3', 'type' => 'admin', 'site_id' => 5], 1)->persist();

        $this->SearchIndexesService->batch('delete', [1, 2, 3]);

        $searchIndexes = $this->SearchIndexesService->getIndex(['site_id' => 5])->all();
        $this->assertEquals(0, count($searchIndexes));
    }

    /**
     * Test createIndexConditions
     * @dataProvider createIndexConditionsDataProvider
     */
    public function testCreateIndexConditions($isLoadScenario, $options, $expected)
    {
        if ($isLoadScenario) {
            $this->loadFixtureScenario(SearchIndexesServiceScenario::class);
        }
        $result = $this->execPrivateMethod($this->SearchIndexesService, "createIndexConditions", [$options]);
        $this->assertEquals($expected, $result);
    }

    /**
     * createIndexConditionsテストのデータプロバイダ
     * @return array
     */
    public function createIndexConditionsDataProvider(): array
    {
        return [
            // 空配列の結果テスト
            [false, [], []],
            [false, ['priority_123' => 1], []],
            // keyword(q): 検索キーワード
            [false, ['keyword' => 'test'], [
                'SearchIndexes.site_id' => 1,
                'and' => [0 => ['or' => [0 => ['SearchIndexes.title LIKE' => '%test%'], 1 => ['SearchIndexes.detail LIKE' => '%test%']]]]
            ]],
            [false, ['q' => 'test'], [
                'SearchIndexes.site_id' => 1,
                'and' => [0 => ['or' => [0 => ['SearchIndexes.title LIKE' => '%test%'], 1 => ['SearchIndexes.detail LIKE' => '%test%']]]]
            ]],
            // site_id(s): サイトID
            [false, ['site_id' => 2], ['SearchIndexes.site_id' => 2]],
            [false, ['s' => 2], ['SearchIndexes.site_id' => 2]],
            // content_id(c): コンテンツID
            [false, ['content_id' => 2], ['SearchIndexes.site_id' => 1, 'SearchIndexes.content_id' => 2]],
            [false, ['c' => 2], ['SearchIndexes.site_id' => 1, 'SearchIndexes.content_id' => 2]],
            // content_filter_id(cf): コンテンツフィルダーID
            [false, ['content_filter_id' => 2], ['SearchIndexes.site_id' => 1, 'SearchIndexes.content_filter_id' => 2]],
            [false, ['cf' => 2], ['SearchIndexes.site_id' => 1, 'SearchIndexes.content_filter_id' => 2]],
            // type: コンテンツタイプ
            [false, ['type' => 'ページ'], ['SearchIndexes.site_id' => 1, 'SearchIndexes.type' => 'ページ']],
            // model(m): モデル名（エンティティ名）
            [false, ['model' => 'Page'], ['SearchIndexes.site_id' => 1, 'SearchIndexes.model' => 'Page']],
            [false, ['m' => 'Page'], ['SearchIndexes.site_id' => 1, 'SearchIndexes.model' => 'Page']],
            // priority: 優先度
            [false, ['priority' => 2], ['SearchIndexes.site_id' => 1, 'SearchIndexes.priority' => 2]],
            // folder_id(f): フォルダーID
            [true, ['folder_id' => 2], ['SearchIndexes.site_id' => 1, 'SearchIndexes.rght <' => 3, 'SearchIndexes.lft >' => 2]],
        ];
    }
}
