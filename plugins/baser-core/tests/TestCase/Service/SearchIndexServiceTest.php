<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Model\Table\SearchIndexesTable;
use BaserCore\Service\SearchIndexService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SearchIndexServiceTest
 * @property SearchIndexService $SearchIndexService
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SearchIndexes',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Service/SearchIndexService/ContentsReconstruct'
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
            'SearchIndexes',
            'Sites',
            'Users',
            'UserGroups',
            'UsersUserGroups',
            'ContentFolders',
            'Pages',
            'SiteConfigs'
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $searchIndex = $this->SearchIndexService->get(2);
    }

	/**
	 * 検索インデックスを再構築する
	 */
	public function testReconstruct()
	{
	    $this->loadFixtures('Service\SearchIndexService\ContentsReconstruct');
		$this->loginAdmin($this->getRequest());

		// ===========================================
		// 全ページ再構築
		// ===========================================
		$this->SearchIndexes->deleteAll(['1=1']);
		$this->SearchIndexService->reconstruct();
		$this->assertEquals(15, $this->SearchIndexes->find()->count());
        return;
		// ===========================================
		// 指定ディレクトリ配下再構築
		// ===========================================
		/* @var Page $pageModel */
		/* @var ContentFolder $contentFolderModel */
		$pageModel = ClassRegistry::init('Page');
		$contentFolderModel = ClassRegistry::init('ContentFolder');
		$pageModel->clear();
		$contentFolderModel->clear();
		// ディレクトリを追加
		$contentFolder = $contentFolderModel->save(['Content' => [
			'parent_id' => 1,
			'title' => 'test',
			'site_id' => 0
		], 'ContentFolder' => []]);
		// ディレクトリを公開
		$contentFolder['Content']['self_status'] = true;
		$contentFolderModel->save($contentFolder);
		// ページを追加
		$page = $pageModel->save(['Content' => [
			'parent_id' => $contentFolder['Content']['id'],
			'title' => 'test2',
			'site_id' => 0
		]]);
		// 検索インデックス更新なしでページを公開
		$pageModel->searchIndexSaving = false;
		$page['Content']['self_status'] = true;
		$pageModel->save($page);
		$pageModel->searchIndexSaving = true;
		// 指定フォルダ配下の検索インデックスを再構築
		$this->SearchIndex->reconstruct($contentFolder['Content']['id']);
		// 対象のページが公開になっている事を確認
		/* @var \SearchIndex $searchIndexModel */
		$searchIndexModel = ClassRegistry::init('SearchIndex');
		$searchIndex = $searchIndexModel->find('first', ['conditions' => ['id' => 8]]);
		$this->assertTrue($searchIndex['SearchIndex']['status']);
	}

}
