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
use BaserCore\Service\ContentFolderService;

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
        'plugin.BaserCore.ContentFolders',
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
        $this->ContentFolderService = new ContentFolderService();
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
     * testGetChildren
     *
     * @return void
     */
    public function testGetChildren(): void
    {
        $this->assertNull($this->ContentService->getChildren(1000));
        $this->assertNull($this->ContentService->getChildren(4));
        $this->assertEquals(3, $this->ContentService->getChildren(6)->count());
    }

    /**
     * testGetEmptyIndex
     *
     * @return void
     */
    public function testGetEmptyIndex(): void
    {
        $result = $this->ContentService->getEmptyIndex();
        $this->assertTrue($result->isEmpty());
        $this->assertInstanceOf('Cake\ORM\Query', $result);
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
            'name' => 'テスト',
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
            ], 13],
            [[
                'site_id' => 1,
                'withTrash' => true,
            ], 15],
            [[
                'site_id' => 1,
                'open' => '1',
                'folder_id' => '',
                'name' => '',
                'type' => 'ContentFolder',
                'self_status' => '1',
                'author_id' => '',
            ], 5],
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
        // softDeleteの場合
        $request = $this->getRequest('/?status=1');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals(13, $contents->all()->count());
        // ゴミ箱を含むの場合
        $request = $this->getRequest('/?status=1&withTrash=true');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals(15, $contents->all()->count());
        // 否定の場合
        $request = $this->getRequest('/?status=1&type!=Page');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals(7, $contents->all()->count());
    }
    /**
     * testGetTrashIndex
     *
     * @return void
     */
    public function testGetTrashIndex(): void
    {
        // type: all
        $result = $this->ContentService->getTrashIndex();
        $this->assertNotNull($result->first()->deleted_date);
        // type: threaded
        $request = $this->getRequest('/');
        $result = $this->ContentService->getTrashIndex($request->getQueryParams(), 'threaded');
        $this->assertNotNull($result->first()->deleted_date);
    }

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     */
    public function testGetContentFolderList()
    {
        $siteId = 1;
        $result = $this->ContentService->getContentFolderList($siteId);
        $this->assertEquals(
            [
                1 => "",
                6 => "　　　└service",
                18 => '　　　　　　└ツリー階層削除用フォルダー(親)',
                19 => '　　　　　　└ツリー階層削除用フォルダー(子)',
                20 => '　　　　　　└ツリー階層削除用フォルダー(孫)'
            ],
        $result);
        $result = $this->ContentService->getContentFolderList($siteId, ['conditions' => ['site_root' => false]]);
        $this->assertEquals([
            6 => 'service',
            18 => '　　　└ツリー階層削除用フォルダー(親)',
            19 => '　　　└ツリー階層削除用フォルダー(子)',
            20 => '　　　└ツリー階層削除用フォルダー(孫)'
        ], $result);
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
            'parent_id' => '',
            'plugin' => 'BaserCore',
            'type' => '',
            'name' => 'テストcreate',
            'title' => 'テストcreate',
        ]);
        $result = $this->ContentService->create($request->getData());
        $expected = $this->ContentService->Contents->find()->last();
        $this->assertEquals($expected->name, $result->name);
    }

    /**
     * testDelete
     *
     * @return void
     */
    public function testDelete(): void
    {
        $this->assertTrue($this->ContentService->delete(14));
        $contents = $this->ContentService->getTrash(14);
        $this->assertNotNull($contents->deleted_date);
    }

    /**
     * testHardDelete
     *
     * @return void
     */
    public function testHardDelete(): void
    {
        // treeBehavior falseの場合
        $this->assertTrue($this->ContentService->hardDelete(15));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentService->getTrash(15);
        // treeBehavior trueの場合
        $this->assertTrue($this->ContentService->hardDelete(16, true));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentService->getTrash(16); // 親要素
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentService->getTrash(17); // 子要素
    }

    /**
     * testHardDeleteWithAssoc
     *
     * @return void
     */
    public function testHardDeleteWithAssoc(): void
    {
        $content = $this->ContentService->getTrash(16);
        $this->assertTrue($this->ContentService->hardDeleteWithAssoc(16));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentService->getTrash(16);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentFolderService->get($content->entity_id);
    }

    /**
     * testDeleteAll
     *
     * @return void
     */
    public function testDeleteAll(): void
    {
        $this->assertEquals(14, $this->ContentService->deleteAll());
        $contents = $this->ContentService->getIndex();
        $this->assertEquals(0, $contents->all()->count());
    }

    /**
     * testTreeDelete
     *
     * @return void
     */
    public function testTreeDelete()
    {
        // エンティティが存在しない場合
        $this->assertFalse($this->ContentService->treeDelete(0));
        // エイリアス出ない場合
        $this->assertTrue($this->ContentService->treeDelete(6));
        $query = $this->ContentService->getTrashIndex(['name' => 'service']);
        $this->assertEquals(4, $query->count());
        // エイリアスがある場合
        // TODO: $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // $result = $this->ContentService->treeDelete(14);
    }

    /**
     * testRestore
     *
     * @return void
     */
    public function testRestore()
    {
        $this->assertNotEmpty($this->ContentService->restore(16));
        $this->assertNotEmpty($this->ContentService->get(16));
    }

    /**
     * testRestoreAll
     *
     * @return void
     */
    public function testRestoreAll()
    {
        $this->assertEquals(2, $this->ContentService->restoreAll(['type' => "ContentFolder"]));
        $this->assertTrue($this->ContentService->getTrashIndex(['type' => "ContentFolder"])->isEmpty());
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

    /**
     * ツリー構造より論理削除する
     */
    public function testSoftDeleteFromTree()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // $result = $this->ContentService->softDeleteFromTree(1);
    }

    /**
     * 再帰的に削除
     *
     * エイリアスの場合
     */
    public function testDeleteRecursive()
    {
        // 子要素がない場合
        $this->assertTrue($this->ContentService->deleteRecursive(4));
        $this->assertNotEmpty($this->ContentService->getTrash(4));
        // 子要素がある場合
        $children = $this->ContentService->getChildren(6);
        $this->assertTrue($this->ContentService->deleteRecursive(6));
        foreach ($children as $child) {
            $this->assertNotEmpty($this->ContentService->getTrash($child->id));
        }
        // 子要素の階層が深い場合
        $children = $this->ContentService->getChildren(18);
        $this->assertTrue($this->ContentService->deleteRecursive(18));
        foreach ($children as $child) {
            $this->assertNotEmpty($this->ContentService->getTrash($child->id));
        }
    }

    /**
     * test getParentLayoutTemplate
     */
    public function testGetParentLayoutTemplate()
    {
        $result = $this->ContentService->getParentLayoutTemplate(6);
        $this->assertEquals('default', $result);
    }

}
