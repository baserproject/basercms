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
        'plugin.BaserCore.Empty/Sites',
        'plugin.BaserCore.Empty/Users',
        'plugin.BaserCore.Empty/Contents',
        'plugin.BaserCore.Empty/ContentFolders',
        'plugin.BaserCore.Empty/Pages',
        'plugin.BaserCore.Empty/SiteConfigs',
    ];

    public $autoFixtures = false;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
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
		$searchIndexesTable->deleteAll(['url LIKE' => '/service/%']);
		$this->SearchIndexesService->reconstruct($content->id);
		$this->assertEquals(2, $searchIndexesTable->find()->where(['url LIKE' => '/service/%'])->count());
	}

}
