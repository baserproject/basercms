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

use BaserCore\Controller\Admin\ContentsController;
use BaserCore\Service\Admin\ContentsAdminServiceInterface;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class ContentsControllerTest
 *
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
        'plugin.BaserCore.Pages',
    ];

    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
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
        $config = $this->ContentsController->Security->getConfig('unlockedActions');
        $this->assertTrue(in_array('delete', $config));
        $this->assertTrue(in_array('batch', $config));
        $this->assertTrue(in_array('trash_return', $config));
    }


    /**
     * testIndex
     *
     * @return void
     */
    public function testIndexRequest(): void
    {
        $this->get('/baser/admin/baser-core/contents/index?list_type=2');
        $this->assertResponseOk();
        // リクエストの変化をテスト
        $this->ContentsController->index($this->getService(ContentsAdminServiceInterface::class), $this->getService(SiteConfigsServiceInterface::class));
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
        $ContentsController->index($this->getService(ContentsAdminServiceInterface::class), $this->getService(SiteConfigsServiceInterface::class));
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
     * ゴミ箱内のコンテンツ一覧を表示する
     *
     * @return void
     */
    public function testTrash_index(): void
    {
        $request = $this->request->withParam('action', 'trash_index')->withParam('prefix', 'Admin');
        $this->ContentsController->setRequest($request);
        $this->ContentsController->trash_index($this->getService(ContentsAdminServiceInterface::class));
        $this->assertArrayHasKey('site_id', $this->ContentsController->getRequest()->getQueryParams());
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
        $this->assertRedirect('/baser/admin/baser-core/contents/index');
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
     * エイリアスを編集する
     */
    public function testEdit_alias()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = $this->ContentsService->getIndex(['name' => 'testEditのエイリアス'])->first();
        $data->title = 'ControllerEditエイリアス';
        $data->site->name = 'ucmitz'; // site側でエラーが出るため
        $this->post('/baser/admin/baser-core/contents/edit_alias/' . $data->id, ["content" => $data->toArray()]);
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
        $this->post('/baser/admin/baser-core/contents/delete', ['content' => ['id' => 6]]);
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
        $request = $this->getRequest('/baser/admin/baser-core/content/')->withEnv('REQUEST_METHOD', 'POST')->withData('content.id', 1);
        $contentsController = new ContentsController($request);
        $contentsController->setName('Contents');
        $contentsController->delete($this->getService(ContentsServiceInterface::class));
        $trash = $this->ContentsService->getTrash(4);
        // beforeDeleteテスト
        $this->assertNotEmpty($trash);
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
