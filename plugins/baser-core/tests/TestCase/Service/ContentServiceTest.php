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

use Cake\Core\Configure;
use Cake\Routing\Router;
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
            'site_id' => 1,
            'open' => '1'
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
            ], 15],
            [[
                'site_id' => 1,
                'withTrash' => true,
            ], 17],
            [[
                'site_id' => 1,
                'open' => '1',
                'folder_id' => '',
                'name' => '',
                'type' => 'ContentFolder',
                'self_status' => '1',
                'author_id' => '',
            ], 7],
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
        $this->assertEquals(15, $contents->all()->count());
        // ゴミ箱を含むの場合
        $request = $this->getRequest('/?status=1&withTrash=true');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals(17, $contents->all()->count());
        // 否定の場合
        $request = $this->getRequest('/?status=1&type!=Page');
        $contents = $this->ContentService->getIndex($request->getQueryParams());
        $this->assertEquals(9, $contents->all()->count());
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
                20 => '　　　　　　└ツリー階層削除用フォルダー(孫)',
                21 => '　　　　　　└testEdit',
            ],
        $result);
        $result = $this->ContentService->getContentFolderList($siteId, ['conditions' => ['site_root' => false]]);
        $this->assertEquals([
            6 => 'service',
            18 => '　　　└ツリー階層削除用フォルダー(親)',
            19 => '　　　└ツリー階層削除用フォルダー(子)',
            20 => '　　　└ツリー階層削除用フォルダー(孫)',
            21 => '　　　└testEdit',
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
        $this->assertEquals(16, $this->ContentService->deleteAll());
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
        // エイリアスがある場合物理削除
        $result = $this->ContentService->treeDelete(22);
        $this->assertFalse($this->ContentService->exists(22, true));
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
        $result = $this->ContentService->getContentsInfo();
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
        // エイリアスを子に持つ場合
        $this->assertTrue($this->ContentService->deleteRecursive(21));
        $this->assertFalse($this->ContentService->exists(22, true)); // エイリアス
    }

    /**
     * test getParentLayoutTemplate
     */
    public function testGetParentLayoutTemplate()
    {
        $result = $this->ContentService->getParentLayoutTemplate(6);
        $this->assertEquals('default', $result);
    }

        /**
     * コンテンツIDよりURLを取得する
     *
     * @param int $id コンテンツID
     * @param bool $full http からのフルのURLかどうか
     * @param string $expects 期待するURL
     * @dataProvider getUrlByIdDataProvider
     */
    public function testGetUrlById($id, $full, $expects)
    {
        $siteUrl = Configure::read('BcEnv.siteUrl');
        Configure::write('BcEnv.siteUrl', 'http://main.com');
        $result = $this->ContentService->getUrlById($id, $full);
        $this->assertEquals($expects, $result);
        Configure::write('BcEnv.siteUrl', $siteUrl);
    }

    public function getUrlByIdDataProvider()
    {
        return [
            // ノーマルURL
            [1, false, '/'],
            [1, true, 'http://main.com/'],    // フルURL
            [999, false, ''],                // 存在しないid
            ['あ', false, '']                // 異常系
        ];
    }

    /**
     * testGetUrl
     *
     * $param string $host ホスト名
     * $param string $userAgent ユーザーエージェント名
     * @param string $url 変換前URL
     * @param boolean $full フルURLで出力するかどうか
     * @param boolean $useSubDomain サブドメインを利用するかどうか
     * @param string $expects 期待するURL
     * @dataProvider getUrlDataProvider
     */
    public function testGetUrl($host, $userAgent, $url, $full, $useSubDomain, $expects)
    {
        $siteUrl = Configure::read('BcEnv.siteUrl');
        Configure::write('BcEnv.siteUrl', 'http://main.com');
        if ($userAgent) {
            $_SERVER['HTTP_USER_AGENT'] = $userAgent;
        }
        if ($host) {
            Configure::write('BcEnv.host', $host);
        }
        // Router::setRequestInfo($this->_getRequest('/m/'));
        $result = $this->ContentService->getUrl($url, $full, $useSubDomain);
        $this->assertEquals($result, $expects);
        Configure::write('BcEnv.siteUrl', $siteUrl);
    }

    public function getUrlDataProvider()
    {
        return [
            //TODO: another.comがそもそもSiteに無いため一旦コメントアウト
            // ノーマルURL
            ['main.com', '', '/', false, false, '/'],
            ['main.com', '', '/index', false, false, '/'],
            ['main.com', '', '/news/archives/1', false, false, '/news/archives/1'],
            ['main.com', 'SoftBank', '/m/news/archives/1', false, false, '/m/news/archives/1'],
            ['main.com', 'iPhone', '/news/archives/1', false, false, '/news/archives/1'],    // 同一URL
            ['sub.main.com', '', '/sub/', false, true, '/'],
            ['sub.main.com', '', '/sub/index', false, true, '/'],
            ['sub.main.com', '', '/sub/news/archives/1', false, true, '/news/archives/1'],
            // ['another.com', '', '/another.com/', false, true, '/'],
            // ['another.com', '', '/another.com/index', false, true, '/'],
            // ['another.com', '', '/another.com/news/archives/1', false, true, '/news/archives/1'],
            // ['another.com', 'iPhone', '/another.com/s/news/archives/1', false, true, '/news/archives/1'],
            // フルURL
            ['main.com', '', '/', true, false, 'http://main.com/'],
            ['main.com', '', '/index', true, false, 'http://main.com/'],
            ['main.com', '', '/news/archives/1', true, false, 'http://main.com/news/archives/1'],
            ['main.com', 'SoftBank', '/m/news/archives/1', true, false, 'http://main.com/m/news/archives/1'],
            ['main.com', 'iPhone', '/news/archives/1', true, false, 'http://main.com/news/archives/1'],    // 同一URL
            ['sub.main.com', '', '/sub/', true, true, 'http://sub.main.com/'],
            ['sub.main.com', '', '/sub/index', true, true, 'http://sub.main.com/'],
            ['sub.main.com', '', '/sub/news/archives/1', true, true, 'http://sub.main.com/news/archives/1'],
            // ['another.com', '', '/another.com/', true, true, 'http://another.com/'],
            // ['another.com', '', '/another.com/index', true, true, 'http://another.com/'],
            // ['another.com', '', '/another.com/news/archives/1', true, true, 'http://another.com/news/archives/1'],
            // ['another.com', 'iPhone', '/another.com/s/news/archives/1', true, true, 'http://another.com/news/archives/1'],
        ];
    }

    /**
     * testGetUrl の base テスト
     *
     * @param $url
     * @param $base
     * @param $expects
     * @dataProvider getUrlBaseDataProvider
     */
    public function testGetUrlBase($url, $base, $useBase, $expects)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        Configure::write('app.baseUrl', $base);
        $request = $this->_getRequest('/');
        $request->base = $base;
        Router::setRequestInfo($request);
        $result = $this->ContentService->getUrl($url, false, false, $useBase);
        $this->assertEquals($result, $expects);
    }

    public function getUrlBaseDataProvider()
    {
        return [
            ['/news/archives/1', '', true, '/news/archives/1'],
            ['/news/archives/1', '', false, '/news/archives/1'],
            ['/news/archives/1', '/sub', true, '/sub/news/archives/1'],
            ['/news/archives/1', '/sub', false, '/news/archives/1'],
        ];
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $name = "testUpdate";
        $newContent = $this->ContentService->getIndex(['name' => 'testEdit'])->first();
        $newContent->name = $name;
        $newContent->site->name = 'ucmitz'; // site側でエラーが出るため
        $this->ContentService->update($this->ContentService->get($newContent->id), $newContent->toArray());
        $this->assertEquals($this->ContentService->get($newContent->id)->name, $name);
    }

    /**
     */
    public function testTrashReturn()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 再帰的にゴミ箱より元に戻す
     */
    public function testTrashReturnRecursive()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コピーする
     *
     * @dataProvider copyDataProvider
     */
    public function testCopy($id, $entityId, $newTitle, $newAuthorId, $newSiteId, $titleExpected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->loginAdmin($this->getRequest());
        $result = $this->Content->copy($id, $entityId, $newTitle, $newAuthorId, $newSiteId)['Content'];
        $this->assertEquals($result['site_id'], $newSiteId);
        $this->assertEquals($result['entity_id'], $entityId);
        $this->assertEquals($result['title'], $titleExpected);
        $this->assertEquals($result['author_id'], $newAuthorId);
    }
    public function copyDataProvider()
    {
        return [
            [1, 2, 'hoge', 3, 4, 'hoge'],
            [1, 2, '', 3, 4, 'baserCMS inc. [デモ] のコピー'],
        ];
    }

    /**
     * testAlias
     *
     * @return void
     */
    public function testAlias()
    {
        $request = $this->getRequest('/');
        $request = $request->withParsedBody([
            'parent_id' => '1',
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'title' => 'テストエイリアス',
        ]);
        $content = $this->ContentService->getIndex()->last();
        $result = $this->ContentService->alias($content->id, $request->getData());
        $expected = $this->ContentService->Contents->find()->last();
        $this->assertEquals($expected->name, $result->name);
        $this->assertEquals($content->id, $result->alias_id);
    }
    /**
     * testAliasDelete
     *
     * @return void
     */
    public function testAliasDelete()
    {
        $this->assertFalse($this->ContentService->deleteAlias(5));
        $content = $this->ContentService->get(5);
        $this->ContentService->update($content, ['alias_id' => 5]);
        $this->assertTrue($this->ContentService->deleteAlias(5));
        // ゴミ箱行きではなくちゃんと削除されてるか確認
        $this->assertTrue($this->ContentService->getIndex(['withTrash' => true, 'id' => 5])->isEmpty());
    }

    /**
     * Test publish
     *
     * @return void
     */
    public function testPublish()
    {
        $contents = $this->getTableLocator()->get('Contents');

        $content = $contents->find()->order(['id' => 'ASC'])->first();
        $content->status = false;
        $contents->save($content);

        $content = $this->ContentService->publish($content->id);
        $this->assertTrue($content->self_status);
    }

    /**
     * Test unpublish
     *
     * @return void
     */
    public function testUnpublish()
    {
        $contents = $this->getTableLocator()->get('Contents');

        $content = $contents->find()->order(['id' => 'ASC'])->first();
        $content->status = true;
        $contents->save($content);

        $content = $this->ContentService->unpublish($content->id);
        $this->assertFalse($content->self_status);
    }

    /**
     * testExists
     *
     * @return void
     */
    public function testExists()
    {
        $this->assertTrue($this->ContentService->exists(1));
        $this->assertFalse($this->ContentService->exists(100));
    }

    /**
     * testMove
     *
     * @return void
     */
    public function testMove()
    {
        // 移動元のエンティティ
        $originEntity = $this->ContentService->getIndex(['parent_id' => 1])->order('lft')->first();
        $origin = [
            'id' => $originEntity->id,
            'parentId' => $originEntity->parent_id
        ];
        // target idが指定されてない場合 親要素内の最後に移動
        $target1 = [
            'id' => "",
            'parentId' => "1",
            'siteId' => "1",
        ];
        $result = $this->ContentService->move($origin, $target1);
        $lastEntity = $this->ContentService->getIndex(['parent_id' => 1])->order('lft')->last();
        $this->assertEquals($result->title, $originEntity->title);
        $this->assertEquals($result->title, $lastEntity->title);
        // targetIdが指定されてる場合
        // 対象が同じ要素の2番目のエンティティなので、直前つまり最初に移動
        $target2 = [
            'id' => "10",
            'parentId' => "1",
            'siteId' => "1",
        ];
        $result = $this->ContentService->move($origin, $target2);
        $firstEntity = $this->ContentService->getIndex(['parent_id' => 1])->order('lft')->first();
        $this->assertEquals($result->title, $originEntity->title);
        $this->assertEquals($result->title, $firstEntity->title);
    }

    /**
     * メインサイトの場合、連携設定がされている子サイトも移動する
     *
     *  @return void
     *  @todo 子サイトが複数ある状況のテストを追加する
     */
    public function testMoveRelateSubSiteContent()
    {
        $result = $this->execPrivateMethod($this->ContentService, 'moveRelateSubSiteContent', ['12', '6', '']);
        $this->assertTrue($result);
    }
}
