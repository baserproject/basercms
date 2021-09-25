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

namespace BaserCore\Test\TestCase\Controller\Admin;

use Cake\Event\Event;
use Cake\Http\ServerRequest;
use BaserCore\Service\SiteService;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentService;
use BaserCore\Service\ContentFolderService;
use BaserCore\Controller\Admin\ContentsController;

/**
 * Class ContentsControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  ContentsController $ContentsController
 * @property ServerRequest $request
 * @property ContentService $ContentService
 * @property ContentFolderService $ContentFolderService
 */
class ContentsControllerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.ContentFolders',

    ];

    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/contents/'));
        $this->ContentsController = new ContentsController($this->request);
        $this->ContentsController->setName('Contents');
        $this->ContentsController->loadModel('BaserCore.ContentFolders');
        $this->ContentsController->loadModel('BaserCore.Users');
        $this->ContentsController->loadComponent('BaserCore.BcContents');
        $this->ContentsController->BcContents->setConfig('items', ["test" => ['title' => 'test', 'plugin' => 'BaserCore']]);
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
        parent::tearDown();
        unset($this->ContentsController, $this->request);
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->ContentsController->BcContents);
    }


    /**
     * testBeforeFilter
     *
     * @return void
     */
    public function testBeforeFilter(): void
    {
        $event = new Event('Controller.beforeFilter', $this->ContentsController);
        $this->ContentsController->beforeFilter($event);
        $this->assertNotEmpty($this->ContentsController->Sites);
        $this->assertNotEmpty($this->ContentsController->SiteConfigs);
        $this->assertNotEmpty($this->ContentsController->ContentFolders);
        $this->assertNotEmpty($this->ContentsController->Users);
    }


    /**
     * testIndex
     *
     * @return void
     */
    public function testIndexRequest(): void
    {
        $this->get('/baser/admin/baser-core/contents/index/');
        $this->assertResponseOk();
        // リクエストの変化をテスト
        $this->ContentsController->index($this->ContentService, new SiteService());
        $this->assertArrayHasKey('num', $this->ContentsController->getRequest()->getQueryParams());
    }

    /**
     * testAjax_index
     * @dataProvider indexDataProvider
     * @return void
     */
    public function testIndex($listType, $action): void
    {
        $search = [
            'site_id' => 1,
            'list_type' => $listType,
            'open' => '1',
            'folder_id' => '',
            'name' => '',
            'type' => '',
            'self_status' => '',
            'author_id' => '',
        ];
        // 初期パラメーター
        $this->request = $this->request->withQueryParams($search)->withParam('action', $action);

        if ($action === 'index' && $listType === 2) {
            // イベント設定
            $this->entryControllerEventToMock('Controller.BaserCore.Contents.searchIndex', function(Event $event) {
                $this->request = $event->getData('request');
                return $this->request->withQueryParams(array_merge($this->request->getQueryParams(), ['num' => 1]));
            });
        }

        $ContentsController = $this->ContentsController->setRequest($this->request);
        $ContentsController->viewBuilder()->setVar('authors', '');
        $ContentsController->index($this->ContentService, new SiteService());
        $this->assertNotEquals('', $ContentsController->viewBuilder()->getVar('template'));
        $this->assertNotEmpty($ContentsController->viewBuilder()->getVar('contents'));

        if ($action === 'index' && $listType === 2) {
            $this->assertEquals(1, $ContentsController->getRequest()->getQuery('num'));
        }
    }

    public function indexDataProvider()
    {
        return [
            [1, "index"],
            [1, "trash_index"],
            [2, "index"],
        ];
    }

    /**
     * testGetContents
     *
     * @param  string $action
     * @param  string $listType
     * @param  string $expected
     * @return void
     * @dataProvider getContentsDataProvider
     */
    public function testGetContents($action, $listType, $search, $expected, $count): void
    {
        $request = $this->request->withParam('action', $action)->withQueryParams(array_merge(['list_type' => $listType], $search));
        $ContentsController = $this->ContentsController->setRequest($request);
        $contents = $this->execPrivateMethod($ContentsController, 'getContents', [$this->ContentService]);
        $this->assertInstanceOf($expected, $contents);
        $this->assertEquals($count, $contents->count());
    }
    public function getContentsDataProvider()
    {
        $search = [
            'site_id' => 1,
            'open' => '1',
            'folder_id' => '',
            'name' => '',
            'type' => '',
            'self_status' => '',
            'author_id' => '',
        ];
        return [
            ['index', '1', [], "Cake\ORM\Query", 14],
            ['index', '2', $search, 'Cake\ORM\ResultSet', 13],
            ['trash_index', '1', [], 'Cake\ORM\Query', 3],
            // 足りない場合は空のindexを返す
            ['index', '', [], 'Cake\ORM\Query', 0],
            ['', '1', [], 'Cake\ORM\Query', 0],
        ];
    }

    /**
     * testGetTemplate
     *
     * @param  string $action
     * @param  string $listType
     * @param  string $expected
     * @return void
     * @dataProvider getTemplateDataProvider
     */
    public function testGetTemplate($action, $listType, $expected): void
    {
        $request = $this->request->withParam('action', $action)->withQueryParams(['list_type' => $listType]);
        $ContentsController = $this->ContentsController->setRequest($request);
        $template = $this->execPrivateMethod($ContentsController, 'getTemplate', [$this->ContentService]);
        $this->assertEquals($expected, $template);
    }
    public function getTemplateDataProvider()
    {
        return [
            ['index', '1', 'index_tree'],
            ['index', '2', 'index_table'],
            ['trash_index', '1', 'index_trash'],
            // 足りない場合空文字列を返す
            ['index', '', 'index_tree'],
            ['', '1', 'index_tree'],
        ];
    }

    /**
     * ゴミ箱内のコンテンツ一覧を表示する
     *
     * @return void
     */
    public function testTrash_index(): void
    {
        $request = $this->request->withParam('action', 'trash_index')->withParam('prefix', 'Admin');
        $this->ContentsController->setRequest($request);
        $this->ContentsController->trash_index($this->ContentService, new SiteService());
        $this->assertEquals('index', $this->ContentsController->viewBuilder()->getTemplate());
        $this->assertArrayHasKey('num', $this->ContentsController->getRequest()->getQueryParams());
    }

    /**
     * ゴミ箱内のコンテンツ一覧を表示する(リクエストテスト)
     *
     * @return void
     */
    public function testTrash_index_getRequest(): void
    {
        // requestテスト
        $this->get('/baser/admin/baser-core/contents/trash_index/');
        $this->assertResponseOk();
    }

    /**
     * ゴミ箱のコンテンツを戻す
     */
    public function testAdmin_ajax_trash_return()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 新規コンテンツ登録（AJAX）
     */
    public function testAdmin_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンテンツ編集
     */
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * エイリアスを編集する
     */
    public function testAdmin_edit_alias()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     *  testAdminDelete
     * コンテンツ削除（論理削除）
     */
    public function testAdminDelete()
    {
        $id = 6;
        // 管理画面からの場合
        $this->request = $this->request->withData('Content.id', $id);
        $this->ContentsController->setRequest($this->request);
        $response = $this->ContentsController->delete($this->ContentService);
        $this->assertEquals("フォルダー「サービス」をゴミ箱に移動しました。", $_SESSION['Flash']['flash'][0]['message']);
        $this->assertStringContainsString("/baser/admin/baser-core/contents/index", $response->getHeaderLine('Location'));
    }

    /**
     *  testAjaxDelete
     * コンテンツ削除（論理削除）
     */
    public function testAjaxDelete()
    {
        $id = 6;
        // ajaxからの場合
        $this->request = $this->request->withData('contentId', $id)->withEnv('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->ContentsController->setRequest($this->request);
        $response = $this->ContentsController->delete($this->ContentService);
        $this->assertStringContainsString("/baser/admin/baser-core/contents/index", $response->getHeaderLine('Location'));
    }

    /**
     *  testDelete
     * IDがなく失敗する場合
     *
     * @return void
     */
    public function testDeleteWithoutId()
    {
        // 管理画面からの場合
        $this->expectException("Cake\Http\Exception\NotFoundException");
        $this->expectExceptionMessage("見つかりませんでした。");
        $this->ContentsController->delete($this->ContentService);
        // ajaxからの場合(TODO: exitのテスト)
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->request = $this->request->withEnv('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->ContentsController->setRequest($this->request);
    }

    /**
     *  testDelete
     * IDが存在しない場合
     *
     * @return void
     */
    public function testDeleteWithFail()
    {
        $id = 100;
        // 管理画面からの場合
        $this->request = $this->request->withData('Content.id', $id);
        $this->ContentsController->setRequest($this->request);
        $this->ContentsController->delete($this->ContentService);
        $this->assertStringContainsString("データベース処理中にエラーが発生しました。", $_SESSION['Flash']['flash'][0]['message']);
        $this->assertStringContainsString("削除中にエラーが発生しました。", $_SESSION['Flash']['flash'][1]['message']);
    }

    /**
     * コンテンツ削除（論理削除）
     */
    public function testAdmin_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 公開状態を変更する
     */
    public function testAdmin_ajax_change_status()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ゴミ箱を空にする
     */
    public function testTrashEmpty()
    {
        // BcContentsTestはコンポネントのテスト用のため、一旦復活させtrashEmptyを実行
        $this->ContentService->restoreAll(['type' => 'BcContentsTest']);
        $this->request = $this->request->withData('test', 'テスト');
        $this->ContentsController->setRequest($this->request);
        $response = $this->ContentsController->trash_empty($this->ContentService);
        $this->assertTrue($this->ContentService->getTrashIndex(['type' => "ContentFolder"])->isEmpty());
        $this->assertEquals(3, $this->ContentFolderService->getIndex()->count());
        $this->assertStringContainsString("/baser/admin/baser-core/contents/trash_index", $response->getHeaderLine('Location'));
    }

    /**
     * コンテンツ表示
     */
    public function testView()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * リネーム
     *
     * 新規登録時の初回リネーム時は、name にも保存する
     */
    public function testAdmin_ajax_rename()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 並び順を移動する
     */
    public function testAdmin_ajax_move()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     */
    public function testAdmin_exists_content_by_url()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 指定したIDのコンテンツが存在するか確認する
     * ゴミ箱のものは無視
     */
    public function testAdmin_ajax_exists()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * プラグイン等と関連付けられていない素のコンテンツをゴミ箱より消去する
     */
    public function testAdmin_empty()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * サイトに紐付いたフォルダリストを取得
     */
    public function testAdmin_ajax_get_content_folder_list()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンテンツ情報を取得する
     */
    public function test_ajax_contents_info()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * admin_ajax_get_full_url
     */
    public function testAdmin_ajax_get_full_url()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
