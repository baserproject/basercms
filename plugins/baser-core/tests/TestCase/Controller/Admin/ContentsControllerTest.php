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
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\Admin\SiteManageService;
use BaserCore\Service\Admin\ContentManageService;
use BaserCore\Controller\Admin\ContentsController;

/**
 * Class ContentsControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  ContentsController $ContentsController
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

    ];
    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->loginAdmin($this->getRequest())->withParam('prefix', 'Admin');
        $this->ContentsController = new ContentsController($this->request);
        $this->ContentsController->setName('Contents');
        $this->ContentsController->loadModel('BaserCore.ContentFolders');
        $this->ContentsController->loadModel('BaserCore.Users');
        $this->ContentsController->loadComponent('BaserCore.BcContents');
        $this->ContentsController->BcContents->setConfig('items', ["test" => ['title' => 'test']]);
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
        $this->assertEquals($this->ContentsController->Security->getConfig('unlockedActions'), ['index']);
    }


    /**
     * testIndex
     *
     * @return void
     */
    public function testIndex(): void
    {
        $request = $this->getRequest();
        $this->get('/baser/admin/baser-core/contents/index/');
        $this->assertResponseOk();
        // リクエストの変化をテスト
        $this->ContentsController->index(new ContentManageService(), new SiteManageService());
        $this->assertArrayHasKey('num', $this->ContentsController->getRequest()->getQueryParams());

    }

    /**
     * testAjax_index
     * @dataProvider ajaxIndexDataProvider
     * @return void
     */
    public function testAjax_index($listType, $action, $expected): void
    {
        $this->request = $this->request->withQueryParams(
            [
                'action' => $action,
                'site_id' => 0,
                'list_type' => $listType,
                'open' => '1',
                'folder_id' => '',
                'name' => '',
                'type' => '',
                'self_status' => '',
                'author_id' => '',
            ]);
        if ($expected === "ajax_index_table") {
            // イベント設定
            $this->entryControllerEventToMock('Controller.BaserCore.Contents.searchIndex', function(Event $event) {
                $this->request = $event->getData('request');
                    return $this->request->withQueryParams(['num' => 1]);
            });
        }
        $ContentsController = $this->ContentsController->setRequest($this->request);
        // index_row_table.phpでエラーがでるため、変数を補完
        if ($expected === "ajax_index_table") $ContentsController->viewBuilder()->setVar('authors', '');

        $this->execPrivateMethod($ContentsController, "ajax_index", [new ContentManageService()]);
        $this->assertFalse($ContentsController->viewBuilder()->isAutoLayoutEnabled());
        $this->assertEquals($expected, $ContentsController->viewBuilder()->getTemplate());

        if ($expected !== "ajax_index_table") {
            $this->assertInstanceOf('Cake\ORM\Query', $ContentsController->viewBuilder()->getVar('datas'));
        } else {
            // FIXME:　うまくいかない　イベント登録正常かテスト
            // $this->assertEquals(1, $ContentsController->getRequest()->getQuery('num'));
            $this->assertInstanceOf('Cake\ORM\ResultSet', $ContentsController->viewBuilder()->getVar('datas'));
        }
    }
    public function ajaxIndexDataProvider()
    {
        return [
            [1, "index", "ajax_index_tree"],
            [1, "trash_index", "ajax_index_trash"],
            [2, "index", "ajax_index_table"],
        ];
    }

    /**
     * ゴミ箱内のコンテンツ一覧を表示する
     *
     * @return void
     */
    public function testTrash_index(): void
    {
        $request = $this->request->withParam('action', 'trash_index');
        $this->ContentsController->setRequest($request);
        $this->ContentsController->trash_index(new ContentManageService(), new SiteManageService());
        $this->assertEquals('index', $this->ContentsController->viewBuilder()->getTemplate());
        $this->assertEquals('trash_index', $this->ContentsController->getRequest()->getQuery('action'));
        $this->assertArrayHasKey('num', $this->ContentsController->getRequest()->getQueryParams());
    }

    /**
     * ゴミ箱内のコンテンツ一覧を表示する(ajaxの場合)
     *
     * @return void
     */
    public function testTrash_index_ajax(): void
    {
        $request = $this->request->withParam('action', 'trash_index')->withEnv('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->ContentsController->setRequest($request);
        $this->ContentsController->trash_index(new ContentManageService(), new SiteManageService());
        $this->assertEquals("ajax_index_trash", $this->ContentsController->viewBuilder()->getTemplate());
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
     * コンテンツ削除（論理削除）
     */
    public function testAdmin_ajax_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
    public function testAdmin_ajax_trash_empty()
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
