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

use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\SearchIndexesTable;
use BaserCore\Service\SearchIndexService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SearchIndexServiceTest
 * @property SearchIndexService $SearchIndexService
 * @property SearchIndexesTable $SearchIndexes
 * @property ContentsTable $Contents
 */
class SearchIndexServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Service/SearchIndexService/ContentsReconstruct',
        'plugin.BaserCore.Service/SearchIndexService/PagesReconstruct',
        'plugin.BaserCore.Service/SearchIndexService/ContentFoldersReconstruct',
        'plugin.BaserCore.Service/SearchIndexService/SearchIndexesReconstruct'
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
        $this->SearchIndexService = new SearchIndexService();
        $this->SearchIndexes = $this->getTableLocator()->get('SearchIndexes');
        $this->Contents = $this->getTableLocator()->get('Contents');
        $this->loadFixtures(
	        'Service\SearchIndexService\SearchIndexesReconstruct'
        );
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexService);
        unset($this->SearchIndexes);
        parent::tearDown();
    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        $searchIndex = $this->SearchIndexService->get(2);
        $this->assertEquals('/about', $searchIndex->url);
    }

	/**
	 * 検索インデックスを再構築する
	 */
	public function testReconstruct()
	{
        $this->loadFixtures(
            'Sites',
            'Users',
            'UserGroups',
            'UsersUserGroups',
            'ContentFolders',
            'Pages',
            'SiteConfigs',
	        'Service\SearchIndexService\ContentsReconstruct',
	        'Service\SearchIndexService\PagesReconstruct',
	        'Service\SearchIndexService\ContentFoldersReconstruct',
        );
		$this->loginAdmin($this->getRequest());

		// ===========================================
		// 全ページ再構築
		// ===========================================
		$this->SearchIndexes->deleteAll(['1=1']);
		$this->SearchIndexService->reconstruct();
		$this->assertEquals(4, $this->SearchIndexes->find()->count());

		// ===========================================
		// 指定ディレクトリ配下再構築
		// ===========================================
		$this->SearchIndexes->deleteAll(['url LIKE' => '/service/%']);
		$content = $this->Contents->find()->where(['url' => '/service/'])->first();
		$this->SearchIndexService->reconstruct($content->id);
		$this->assertEquals(2, $this->SearchIndexes->find()->where(['url LIKE' => '/service/%'])->count());
	}

}
