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

use BaserCore\Test\Factory\ContentFactory;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentFoldersService;

/**
 * BaserCore\Model\Table\ContentsTable Test Case
 *
 * @property ContentsService $ContentsService
 */
class ContentsServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var ContentsService
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
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.SiteConfigs',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentsService = new ContentsService();
        $this->ContentFoldersService = new ContentFoldersService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Router::reload();
        unset($this->ContentsService);
        parent::tearDown();
    }

    /**
     * testGet
     *
     * @return void
     */
    public function testGet(): void
    {
        $result = $this->ContentsService->get(1);
        $this->assertEquals("baserCMSサンプル", $result->title);
    }

    /**
     * testGetChildren
     *
     * @return void
     */
    public function testGetChildren(): void
    {
        $this->assertNull($this->ContentsService->getChildren(1000));
        $this->assertNull($this->ContentsService->getChildren(4));
        $this->assertEquals(3, $this->ContentsService->getChildren(6)->count());
    }

    /**
     * testGetEmptyIndex
     *
     * @return void
     */
    public function testGetEmptyIndex(): void
    {
        $result = $this->ContentsService->getEmptyIndex();
        $this->assertTrue($result->all()->isEmpty());
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
        $result = $this->ContentsService->getTreeIndex($request->getQueryParams());
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
            'name' => 'テスト',
            'type' => 'ContentFolder',
            'self_status' => '1',
            'author_id' => '',
        ]);
        $result = $this->ContentsService->getTableConditions($request->getQueryParams());
        $this->assertEquals([
            'OR' => [
            'name LIKE' => '%テスト%',
            'title LIKE' => '%テスト%',
            ],
            'name' => 'テスト',
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
        $result = $this->ContentsService->getTableIndex($conditions);
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
            ], 19],
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
        $contents = $this->ContentsService->getIndex($request->getQueryParams());
        $this->assertEquals('', $contents->first()->name);

        $request = $this->getRequest('/?name=index');
        $contents = $this->ContentsService->getIndex($request->getQueryParams());
        $this->assertEquals('index', $contents->first()->name);
        $this->assertEquals('トップページ', $contents->first()->title);

        $request = $this->getRequest('/?limit=1');
        $contents = $this->ContentsService->getIndex($request->getQueryParams());
        $this->assertEquals(1, $contents->all()->count());
        // softDeleteの場合
        $request = $this->getRequest('/?status=1');
        $contents = $this->ContentsService->getIndex($request->getQueryParams());
        $this->assertEquals(19, $contents->all()->count());
        // ゴミ箱を含むの場合
        $request = $this->getRequest('/?status=1&withTrash=true');
        $contents = $this->ContentsService->getIndex($request->getQueryParams());
        $this->assertEquals(22, $contents->all()->count());
        // 否定の場合
        $request = $this->getRequest('/?status=1&type!=Page');
        $contents = $this->ContentsService->getIndex($request->getQueryParams());
        $this->assertEquals(11, $contents->all()->count());
        // フォルダIDを指定する場合
        $request = $this->getRequest('/?status=1&folder_id=6');
        $contents = $this->ContentsService->getIndex($request->getQueryParams());
        $this->assertEquals(3, $contents->all()->count());
    }
    /**
     * testGetTrashIndex
     *
     * @return void
     */
    public function testGetTrashIndex(): void
    {
        // type: all
        $result = $this->ContentsService->getTrashIndex();
        $this->assertNotNull($result->first()->deleted_date);
        // type: threaded
        $request = $this->getRequest('/');
        $result = $this->ContentsService->getTrashIndex($request->getQueryParams(), 'threaded');
        $this->assertNotNull($result->first()->deleted_date);
    }

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     */
    public function testGetContentFolderList()
    {
        $siteId = 1;
        $result = $this->ContentsService->getContentFolderList($siteId);
        $this->assertEquals(
            [
                1 => "baserCMSサンプル",
                6 => "　　　└サービス",
                18 => '　　　└ツリー階層削除用フォルダー(親)',
                19 => '　　　　　　└ツリー階層削除用フォルダー(子)',
                20 => '　　　　　　　　　└ツリー階層削除用フォルダー(孫)',
                21 => '　　　└testEdit',
            ],
        $result);
        $result = $this->ContentsService->getContentFolderList($siteId, ['conditions' => ['site_root' => false]]);
        $this->assertEquals([
            6 => 'サービス',
            18 => 'ツリー階層削除用フォルダー(親)',
            19 => '　　　└ツリー階層削除用フォルダー(子)',
            20 => '　　　　　　└ツリー階層削除用フォルダー(孫)',
            21 => 'testEdit',
        ], $result);
    }

    /**
     * ツリー構造のデータを コンボボックスのデータ用に変換する
     */
    public function testConvertTreeList()
    {
        $this->assertEquals([], $this->ContentsService->convertTreeList([]));
        // 空でない場合
        $this->assertEquals([6 => "　　　└service"], $this->ContentsService->convertTreeList([6 => '_service']));
    }

    /**
     * testDelete
     *
     * @return void
     */
    public function testDelete(): void
    {
        $this->assertTrue($this->ContentsService->delete(14));
        $contents = $this->ContentsService->getTrash(14);
        $this->assertNotNull($contents->deleted_date);
        // aliasの場合
        $content = $this->ContentsService->get(5);
        $this->ContentsService->update($content, ['alias_id' => 5, 'name' => 'test']);
        $this->assertTrue($this->ContentsService->delete(5));
        // ゴミ箱行きではなくちゃんと削除されてるか確認
        $this->assertTrue($this->ContentsService->getIndex(['withTrash' => true, 'id' => 5])->all()->isEmpty());
    }

    /**
     * testHardDelete
     *
     * @return void
     */
    public function testHardDelete(): void
    {
        // treeBehavior falseの場合
        $this->assertTrue($this->ContentsService->hardDelete(15));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentsService->getTrash(15);
        // treeBehavior trueの場合
        $this->assertTrue($this->ContentsService->hardDelete(16, true));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentsService->getTrash(16); // 親要素
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentsService->getTrash(17); // 子要素
    }

    /**
     * testHardDeleteWithAssoc
     *
     * @return void
     */
    public function testHardDeleteWithAssoc(): void
    {
        $content = $this->ContentsService->getTrash(16);
        $this->assertTrue($this->ContentsService->hardDeleteWithAssoc(16));
        try {
            $this->ContentsService->getTrash(16);
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('Cake\Datasource\Exception\RecordNotFoundException', get_class($e));
        }
        try {
            $this->ContentFoldersService->get($content->entity_id);
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('Cake\Datasource\Exception\RecordNotFoundException', get_class($e));
        }
        try {
            $this->assertTrue($this->ContentsService->hardDeleteWithAssoc(999));
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('Cake\Datasource\Exception\RecordNotFoundException', get_class($e));
        }
    }

    /**
     * testDeleteAll
     *
     * @return void
     */
    public function testDeleteAll(): void
    {
        $this->assertEquals(20, $this->ContentsService->deleteAll());
        $contents = $this->ContentsService->getIndex();
        $this->assertEquals(0, $contents->all()->count());
    }

    /**
     * testRestore
     *
     * @return void
     */
    public function testRestore()
    {
        $this->assertNotEmpty($this->ContentsService->restore(16));
        $this->assertNotEmpty($this->ContentsService->get(16));
    }

    /**
     * testRestoreAll
     *
     * @return void
     */
    public function testRestoreAll()
    {
        $this->assertEquals(2, $this->ContentsService->restoreAll(['type' => "ContentFolder"]));
        $this->assertTrue($this->ContentsService->getTrashIndex(['type' => "ContentFolder"])->all()->isEmpty());
    }

    /**
     * testGetContentsInfo
     *
     * @return void
     */
    public function testGetContentsInfo()
    {
        $result = $this->ContentsService->getContentsInfo();
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
        // $result = $this->ContentsService->softDeleteFromTree(1);
    }

    /**
     * 再帰的に削除
     * エイリアスの場合物理削除
     */
    public function testDeleteRecursive()
    {
        // 子要素がない場合
        $this->assertTrue($this->ContentsService->deleteRecursive(4));
        $this->assertNotEmpty($this->ContentsService->getTrash(4));
        // 子要素がある場合
        $children = $this->ContentsService->getChildren(6);
        $this->assertTrue($this->ContentsService->deleteRecursive(6));
        foreach ($children as $child) {
            $this->assertNotEmpty($this->ContentsService->getTrash($child->id));
        }
        // 子要素の階層が深い場合
        $children = $this->ContentsService->getChildren(18);
        $this->assertTrue($this->ContentsService->deleteRecursive(18));
        foreach ($children as $child) {
            $this->assertNotEmpty($this->ContentsService->getTrash($child->id));
        }
        // エイリアスを子に持つ場合
        $this->assertTrue($this->ContentsService->deleteRecursive(21));
        $this->assertFalse($this->ContentsService->exists(22, true)); // エイリアス
        // エンティティが存在しない場合
        $this->expectExceptionMessage('idが指定されてません');
        $this->assertFalse($this->ContentsService->deleteRecursive(0));
    }

    /**
     * test getParentLayoutTemplate
     */
    public function testGetParentLayoutTemplate()
    {
        $result = $this->ContentsService->getParentLayoutTemplate(6);
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
        $result = $this->ContentsService->getUrlById($id, $full);
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
        $result = $this->ContentsService->getUrl($url, $full, $useSubDomain);
        $this->assertEquals($result, $expects);
        Configure::write('BcEnv.siteUrl', $siteUrl);
    }

    public function getUrlDataProvider()
    {
        return [
            //NOTE: another.comがそもそもSiteに無いため一旦コメントアウト
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
        $result = $this->ContentsService->getUrl($url, false, false, $useBase);
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
        $newContent = $this->ContentsService->getIndex(['name' => 'testEdit'])->first();
        $newContent->name = $name;
        $newContent->site->name = 'ucmitz'; // site側でエラーが出るため
        $this->ContentsService->update($this->ContentsService->get($newContent->id), $newContent->toArray());
        $this->assertEquals($this->ContentsService->get($newContent->id)->name, $name);
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
        $request = $this->loginAdmin($this->getRequest('/'));
        Router::setRequest($request);
        $content = $this->ContentsService->getIndex()->all()->last();
        $request = $request->withParsedBody([
            'parent_id' => '1',
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'title' => 'テストエイリアス',
            'alias_id' => $content->id
        ]);
        $result = $this->ContentsService->alias($request->getData());
        $expected = $this->ContentsService->Contents->find()->all()->last();
        $this->assertEquals($expected->name, $result->name);
        $this->assertEquals($content->id, $result->alias_id);
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

        $content = $this->ContentsService->publish($content->id);
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

        $content = $this->ContentsService->unpublish($content->id);
        $this->assertFalse($content->self_status);
    }

    /**
     * testExists
     *
     * @return void
     */
    public function testExists()
    {
        $this->assertTrue($this->ContentsService->exists(1));
        $this->assertFalse($this->ContentsService->exists(100));
    }

    /**
     * testMove
     *
     * @return void
     */
    public function testMove()
    {
        // 移動元のエンティティ
        $originEntity = $this->ContentsService->getIndex(['parent_id' => 1])->order('lft')->first();
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
        $result = $this->ContentsService->move($origin, $target1);
        $lastEntity = $this->ContentsService->getIndex(['parent_id' => 1])->order('lft')->all()->last();
        $this->assertEquals($result->title, $originEntity->title);
        $this->assertEquals($result->title, $lastEntity->title);
        // targetIdが指定されてる場合
        // 対象が同じ要素の2番目のエンティティなので、直前つまり最初に移動
        $target2 = [
            'id' => "10",
            'parentId' => "1",
            'siteId' => "1",
        ];
        $result = $this->ContentsService->move($origin, $target2);
        $firstEntity = $this->ContentsService->getIndex(['parent_id' => 1])->order('lft')->first();
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
        $result = $this->execPrivateMethod($this->ContentsService, 'moveRelateSubSiteContent', ['12', '6', '']);
        $this->assertTrue($result);
    }

    /**
     * 公開状態を取得する
     */
    public function testIsAllowPublish()
    {
        $content = $this->ContentsService->get(1);
        $this->assertTrue($this->ContentsService->isAllowPublish($content));
    }

    /**
     * サイトルートコンテンツを取得する
     *
     * @param int $siteId
     * @param mixed $expects 期待するコンテントのid (存在しない場合はから配列)
     * @dataProvider getSiteRootDataProvider
     */
    public function testGetSiteRoot($siteId, $expects)
    {
        $result = $this->ContentsService->getSiteRoot($siteId);
        if ($result) {
            $result = $result->id;
        }

        $this->assertEquals($expects, $result);
    }

    public function getSiteRootDataProvider()
    {
        return [
            [1, 1],
            [7, null],        // 存在しないsiteId
        ];
    }

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     */
    public function testExistsContentByUrl()
    {
        $this->assertFalse($this->ContentsService->existsContentByUrl('/aaa'));
        $this->assertTrue($this->ContentsService->existsContentByUrl('/about'));
    }

    /**
     * タイトル、URL、公開状態が更新されているか確認する
     * @dataProvider isChangedStatusDataProvider
     */
    public function testIsChangedStatus($id, $newData, $expected)
    {
        $this->assertEquals($expected, $this->ContentsService->isChangedStatus($id, $newData));
    }

    public function isChangedStatusDataProvider()
    {
        return [
            // idが存在しない場合はtrueを返す
            [
                100, [], true
            ],
            [
                1,
                [
                    "self_status" => "1",
                    "self_publish_begin_date" => "2022/01/04",
                    "self_publish_begin_time" => "00:00:00",
                    "self_publish_begin" => "2022-01-04 00:00:00",
                    "self_publish_end_date" => "2022/01/07",
                    "self_publish_end_time" => "00:00:00",
                    "self_publish_end" => "2022-01-07 00:00:00"
                ],
                true
            ]
        ];
    }

    /**
     * testSetTreeConfig
     *
     * @return void
     */
    public function testSetTreeConfig()
    {
        $treeBehavior = $this->ContentsService->setTreeConfig('scope', ['country_name' => 'France']);
        $this->assertEquals($treeBehavior->getConfig('scope'), ['country_name' => 'France']);
    }

    /**
     * testGetNeighbors
     *
     * @param  mixed $options
     * @return void
     */
    public function testGetNeighbors()
    {
        $content = $this->ContentsService->get(5);
        $conditions = array_merge($this->ContentsService->getConditionAllowPublish(), [
            'Contents.type <>' => 'ContentFolder',
            'Contents.site_id' => $content->site_id
        ]);
        $options = [
            'field' => 'lft',
            'value' => $content->lft,
            'conditions' => $conditions,
            'order' => ['Contents.lft'],
        ];
        $neighbors = $this->ContentsService->getNeighbors($options);
        $this->assertEquals("サービス１", $neighbors['next']['title']);
        $this->assertEquals("NEWS(※関連Fixture未完了)", $neighbors['prev']['title']);
        // 100より前を取得する場合
        $options = [
            'field' => 'id',
            'value' => 100,
            'conditions' => ['Contents.site_id' => 1]
        ];
        $neighbors = $this->ContentsService->getNeighbors($options);
        // フィールドが空かテスト
        $this->assertEquals($this->ContentsService->getIndex(['site_id' => 1])->all()->last(), $neighbors['prev']);
    }


    /**
     * testEncodeParsedUrl
     * @dataProvider encodeParsedUrlDataProvider
     * @return void
     */
    public function testEncodeParsedUrl($path, $expected)
    {
        $result = $this->ContentsService->encodeParsedUrl($path);
        $this->assertEquals($expected, $result["path"]);
    }

    public function encodeParsedUrlDataProvider()
    {
        // サブサイトはすべて同じpathに変換されているかテスト
        return [
            // サブサイト
            [
                "http://localhost/en/新しい_固定ページ",
                "/en/%E6%96%B0%E3%81%97%E3%81%84_%E5%9B%BA%E5%AE%9A%E3%83%9A%E3%83%BC%E3%82%B8"
            ],
            // サブドメインを使う場合
            [
                "https://en.localhost/新しい_固定ページ",
                "/en/%E6%96%B0%E3%81%97%E3%81%84_%E5%9B%BA%E5%AE%9A%E3%83%9A%E3%83%BC%E3%82%B8"
            ],
            // サブドメインを使う場合 type2
            [
                "http://en/新しい_固定ページ",
                "/en/%E6%96%B0%E3%81%97%E3%81%84_%E5%9B%BA%E5%AE%9A%E3%83%9A%E3%83%BC%E3%82%B8"
            ],
        ];
    }

    /**
     * test getPath
     */
    public function testGetPath()
    {
        $this->assertEquals(1, $this->ContentsService->getPath(1)->all()->count());
        $this->assertEquals(3, $this->ContentsService->getPath(11)->all()->count());
        $this->expectException(RecordNotFoundException::class);
        $this->ContentsService->getPath(100)->all()->count();
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        $result = $this->ContentsService->getList();
        $this->assertContains('testEdit', $result);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $content = $this->ContentsService->getNew()->toArray();
        $this->assertArrayHasKey('self_status', $content);
        $this->assertArrayHasKey('self_publish_begin', $content);
        $this->assertArrayHasKey('self_publish_end', $content);
        $this->assertArrayHasKey('created_date', $content);
        $this->assertArrayHasKey('site_root', $content);
        $this->assertArrayHasKey('exclude_search', $content);
    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->assertNull($this->ContentsService->create([]));
    }

    /**
     * test batch
     * @return void
     */
    public function testBatch()
    {
        ContentFactory::make(['id' => 100, 'plugin' => 'BaserCore', 'type' => 'ContentFolder', 'site_id' => 100, 'lft' => 1, 'rght' => 48,], 1)->persist();
        ContentFactory::make(['id' => 101, 'plugin' => 'BaserCore', 'type' => 'ContentFolder', 'site_id' => 100, 'lft' => 1, 'rght' => 48,], 1)->persist();
        ContentFactory::make(['id' => 102, 'plugin' => 'BaserCore', 'type' => 'ContentFolder', 'site_id' => 100, 'lft' => 1, 'rght' => 48,], 1)->persist();


        $this->ContentsService->batch('delete', [100, 101, 102]);

        $contents = $this->ContentsService->getIndex(['site_id' => 100])->all();
        $this->assertEquals(0, count($contents));
    }

    /**
     * test getTitlesById
     */
    public function testGetTitlesById()
    {
        ContentFactory::make(['id' => 110, 'title' => 'ID110'], 1)->persist();
        ContentFactory::make(['id' => 111, 'title' => 'ID111'], 1)->persist();
        $titles = $this->ContentsService->getTitlesById([110, 111]);
        $this->assertCount(2, $titles);
        $this->assertEquals('ID110', $titles[110]);
        $this->assertEquals('ID111', $titles[111]);
    }
}
