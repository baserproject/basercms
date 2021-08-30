<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentService;

/**
 * BaserCore\Model\Table\ContentsTable Test Case
 *
 * @property ContentService $ContentService
 */
class ContentServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var ContentService
     */
    public $Contents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentService = new ContentService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentService);
        parent::tearDown();
    }

    /**
     * testGet
     *
     * @return void
     */
    public function testGet(): void
    {
        $result = $this->ContentService->get(1);
        $this->assertEquals("baserCMSサンプル", $result->title);

    }
    /**
     * testGetTreeIndex
     *
     * @return void
     */
    public function testGetTreeIndex(): void
    {
        $request = $this->getRequest('/?site_id=1');
        $result = $this->ContentService->getTreeIndex($request->getQueryParams());
        $this->assertEquals("baserCMSサンプル", $result->first()->title);
    }

    /**
     * testGetTableConditions
     *
     * @return void
     */
    public function testGetTableConditions()
    {

        $request = $this->getRequest()->withQueryParams([
            'site_id' => 1,
            'open' => '1',
            'folder_id' => '6',
            'name' => 'テスト',
            'type' => 'ContentFolder',
            'self_status' => '1',
            'author_id' => '',
        ]);
        $result = $this->ContentService->getTableConditions($request->getQueryParams());
        $this->assertEquals([
            'OR' => [
            'name LIKE' => '%テスト%',
            'title LIKE' => '%テスト%',
            ],
            'rght <' => (int) 15,
            'lft >' => (int) 8,
            'self_status' => '1',
            'type' => 'ContentFolder',
            'site_id' => 1
            ], $result);
    }

    /**
     * testgetTableIndex
     *
     * @return void
     * @dataProvider getTableIndexDataProvider
     */
    public function testgetTableIndex($conditions, $expected): void
    {
        $result = $this->ContentService->getTableIndex($conditions);
        $this->assertEquals($expected, $result->count());
    }
    public function getTableIndexDataProvider()
    {
        return [
            [[
                'site_id' => 1,
            ], 10],
            [[
                'site_id' => 1,
                'open' => '1',
                'folder_id' => '',
                'name' => '',
                'type' => 'ContentFolder',
                'self_status' => '1',
                'author_id' => '',
            ], 2],
            [[
                'site_id' => 1,
                'open' => '1',
                'folder_id' => '6',
                'name' => 'サービス',
                'type' => 'Page',
                'self_status' => '',
                'author_id' => '',
            ], 3],
        ];
    }

    /**
     * test getIndex
     */
    public function testGetIndex(): void
    {
        $request = $this->getRequest('/');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals('', $contents->first()->name);

        $request = $this->getRequest('/?name=index');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals('index', $contents->first()->name);
        $this->assertEquals('トップページ', $contents->first()->title);

        $request = $this->getRequest('/?num=1');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals(1, $contents->all()->count());

        $request = $this->getRequest('/?status=1');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals(10, $contents->all()->count());
    }
    /**
     * testGetTrashIndex
     *
     * @return void
     */
    public function testGetTrashIndex(): void
    {
        $request = $this->getRequest('/');
        $result = $this->ContentService->getTrashIndex($request->getQueryParams());
        $this->assertTrue($result->first()->deleted);
    }

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     */
    public function testGetContentFolderList()
    {
        $siteId = 1;
        $result = $this->ContentService->getContentFolderList($siteId);
        $this->assertEquals([1 => "", 6 => "　　　└service"], $result);
        $result = $this->ContentService->getContentFolderList($siteId, ['conditions' => ['site_root' => false]]);
        $this->assertEquals([6 => 'service'], $result);
    }

    /**
     * ツリー構造のデータを コンボボックスのデータ用に変換する
     */
    public function testConvertTreeList()
    {
        $this->assertEquals([], $this->ContentService->convertTreeList([]));
        // 空でない場合
        $this->assertEquals([6 => "　　　└service"], $this->ContentService->convertTreeList([6 => '_service']));
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $request = $this->getRequest('/');
        $request = $request->withParsedBody([
            'name' => 'テストcreate',
        ]);
        $result = $this->ContentService->create($request->getData());
        $expected = $this->ContentService->Contents->find()->last();
        $this->assertEquals($expected->name, $result->name);
    }

    /**
     * testGetContentsInfo
     *
     * @return void
     */
    public function testGetContentsInfo()
    {
        $result = $this->ContentService->getContensInfo();
        $this->assertTrue(isset($result[0]['unpublished']));
        $this->assertTrue(isset($result[0]['published']));
        $this->assertTrue(isset($result[0]['total']));
        $this->assertTrue(isset($result[0]['display_name']));
    }
}
