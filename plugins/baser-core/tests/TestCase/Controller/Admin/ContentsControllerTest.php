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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Service\ContentsAdminServiceInterface;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentsService;
use BaserCore\Utility\BcContainerTrait;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Controller\Admin\ContentsController;

/**
 * Class ContentsControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  ContentsController $ContentsController
 * @property ServerRequest $request
 * @property ContentsService $ContentsService
 * @property ContentFoldersService $ContentFoldersService
 */
class ContentsControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;

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
        'plugin.BcSearchIndex.SearchIndexes',
        'plugin.BaserCore.Pages',
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
        $this->ContentsController->loadComponent('BaserCore.BcAdminContents');
        $this->ContentsController->BcAdminContents->setConfig('items', ["test" => ['title' => 'test', 'plugin' => 'BaserCore', 'type' => 'ContentFolder']]);
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
        $this->assertNotEmpty($this->ContentsController->BcAdminContents);
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
        $this->ContentsController->index($this->getService(ContentsAdminServiceInterface::class), $this->getService(SitesServiceInterface::class), $this->getService(SiteConfigsServiceInterface::class));
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
            $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Contents.searchIndex', function(Event $event) {
                $this->request = $event->getData('request');
                return $this->request->withQueryParams(array_merge($this->request->getQueryParams(), ['num' => 1]));
            });
        }

        $ContentsController = $this->ContentsController->setRequest($this->request);
        $ContentsController->viewBuilder()->setVar('authors', '');
        $ContentsController->index($this->getService(ContentsAdminServiceInterface::class), $this->getService(SitesServiceInterface::class), $this->getService(SiteConfigsServiceInterface::class));
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
        $contents = $this->execPrivateMethod($ContentsController, 'getContents', [$this->ContentsService]);
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
            ['index', '1', [], "Cake\ORM\Query", 20],
            ['index', '2', $search, 'Cake\ORM\ResultSet', 15],
            ['trash_index', '1', [], 'Cake\ORM\Query', 4],
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
        $template = $this->execPrivateMethod($ContentsController, 'getTemplate', [$this->ContentsService]);
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
        $this->ContentsController->trash_index($this->getService(ContentsAdminServiceInterface::class), $this->getService(SitesServiceInterface::class), $this->getService(SiteConfigsServiceInterface::class));
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
    public function testTrash_return()
    {
        $this->get('/baser/admin/baser-core/contents/trash_return/');
        $this->assertResponseCode(404);
        $id = $this->ContentsService->getTrashIndex()->first()->id;
        $this->get("/baser/admin/baser-core/contents/trash_return/{$id}");
        $this->assertRedirect('/baser/admin/baser-core/contents/trash_index');
        $this->assertResponseSuccess();
        $this->assertNotEmpty($this->ContentsService->get($id));
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
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = $this->ContentsService->getIndex(['name' => 'testEdit'])->first();
        $data->name = 'ControllerEdit';
        $data->site->name = 'ucmitz'; // site側でエラーが出るため
        $this->post('/baser/admin/baser-core/contents/edit/' . $data->id, ["Contents" => $data->toArray()]);
        $this->assertResponseSuccess();
        $this->assertRedirect('/baser/admin/baser-core/contents/edit/' . $data->id);
        $this->assertEquals('ControllerEdit', $this->ContentsService->get($data->id)->name);
    }

    /**
     * testBatch
     *
     * @return void
     */
    public function testBatch()
    {
        $this->enableCsrfToken();
        // 空データ送信
        $this->post('/baser/admin/baser-core/contents/batch', []);
        $this->assertResponseEmpty();
        // delete
        $data = [
            'ListTool' => [
                'batch' => 'delete',
                'batch_targets' => [1],
            ]
        ];
        $this->post('/baser/admin/baser-core/contents/batch', $data);
        $this->assertResponseNotEmpty();
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentsService->get(1);
    }
    /**
     * testBatchUnpublish
     * NOTE: publishとunPublishのテストを同じ場所に書くとupdateDataが走らないため分離
     *
     * @return void
     */
    public function testBatchUnpublish()
    {
        $this->enableCsrfToken();
        // unpublish
        $data = [
            'ListTool' => [
                'batch' => 'unpublish',
                'batch_targets' => [1],
            ]
        ];
        $this->post('/baser/admin/baser-core/contents/batch', $data);
        $this->assertResponseNotEmpty();
        $content = $this->ContentsService->get(1);
        $this->assertFalse($content->status);
    }

    /**
     * testBatchUnpublish
     *
     * @return void
     */
    public function testBatchPublish()
    {
        $this->enableCsrfToken();
        $content = $this->ContentsService->get(1);
        $this->ContentsService->update($content, ['id' => $content->id, 'status' => false, 'name' => 'test']);
        // publish
        $data = [
            'ListTool' => [
                'batch' => 'publish',
                'batch_targets' => [1],
            ]
        ];
        $this->post('/baser/admin/baser-core/contents/batch', $data);
        $this->assertResponseNotEmpty();
        $content = $this->ContentsService->get(1);
        $this->assertTrue($content->status);
    }

    /**
     * エイリアスを編集する
     */
    public function testEdit_alias()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = $this->ContentsService->getIndex(['name' => 'testEditのエイリアス'])->first();
        $data->title = 'ControllerEditエイリアス';
        $data->site->name = 'ucmitz'; // site側でエラーが出るため
        $this->post('/baser/admin/baser-core/contents/edit_alias/' . $data->id, ["Contents" => $data->toArray()]);
        $this->assertResponseSuccess();
        $this->assertRedirect('/baser/admin/baser-core/contents/edit_alias/' . $data->id);
        $this->assertEquals('ControllerEditエイリアス', $this->ContentsService->get($data->id)->title);
    }

    /**
     *  testAdminDelete
     * コンテンツ削除（論理削除）
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        // 管理画面からの場合
        $this->post('/baser/admin/baser-core/contents/delete', ['Contents' => ['id' => 6]]);
        $this->assertResponseSuccess();
        $this->assertRedirect("/baser/admin/baser-core/contents/index");
        $this->assertEquals("フォルダー「サービス」をゴミ箱に移動しました。", $_SESSION['Flash']['flash'][0]['message']);
        $this->assertStringContainsString("/baser/admin/baser-core/contents/index", $this->_response->getHeaderLine('Location'));
    }

    /**
     * testDeleteWithEvent
     *
     * @return void
     */
    public function testDeleteWithEvent()
    {
        // beforeDeleteイベントテスト(id1の代わりに4が削除されるか)
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Contents.beforeDelete', function(Event $event) {
            $id = 4;
            return $id;
        });
        // afterDeleteイベントテスト(削除されたコンテンツの名前をイベントで更新できるか)
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Contents.afterDelete', function(Event $event) {
            $id = $event->getData('data');
            $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
            $this->ContentsService->get($id);
        });
        $request = $this->getRequest('/baser/admin/baser-core/content/')->withEnv('REQUEST_METHOD', 'POST')->withData('Contents.id', 1);
        $contentsController = new ContentsController($request);
        $contentsController->setName('Contents');
        $contentsController->delete($this->getService(ContentsServiceInterface::class));
        $trash = $this->ContentsService->getTrash(4);
        // beforeDeleteテスト
        $this->assertNotEmpty($trash);
        // afterDeleteテスト
        $this->assertEquals('testAfterDelete', $trash->name);
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
        $this->ContentsController->delete($this->ContentsService);
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
        $this->ContentsController->delete($this->ContentsService);
        $this->assertStringContainsString("不正なリクエストです。", $_SESSION['Flash']['flash'][0]['message']);
    }

    /**
     * 公開状態を変更する
     */
    public function testAdmin_ajax_change_status()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンテンツ表示
     */
    public function testView()
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
     * プラグイン等と関連付けられていない素のコンテンツをゴミ箱より消去する
     */
    public function testAdmin_empty()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
