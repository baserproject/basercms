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

use Cake\Event\Event;
use BaserCore\Service\PagesService;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\Admin\PagesController;

/**
 * Class PagesControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  PagesController $PagesController
 */
class PagesControllerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.SearchIndexes',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Dblogs',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PagesController = new PagesController($this->getRequest());
        $this->PagesService = new PagesService();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->PagesController->BcAdminContents);
    }
    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $event = new Event('Controller.beforeFilter', $this->PagesController);
        $this->PagesController->beforeFilter($event);
        $helpers = $this->PagesController->viewBuilder()->getHelpers();
        $this->assertCount(4, $helpers);
    }

    /**
     * 固定ページ情報登録
     */
    public function testAdmin_ajax_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 固定ページ情報編集
     */
    public function testEdit()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = $this->PagesService->getIndex()->first();
        $data->page_template = 'testEdit';
        $data->content->name = "pageTestUpdate";
        $id = $data->id;
        $this->post('/baser/admin/baser-core/pages/edit/' . $id, [
            'Pages' => $data->toArray(),
            "Contents" => ['title' => $data->content->name, 'parent_id' => $data->content->parent_id]
        ]);
        $this->assertResponseSuccess();
        $this->assertRedirect('/baser/admin/baser-core/pages/edit/' . $id);
        $this->assertEquals('testEdit', $this->PagesService->get($id)->page_template);
        $this->assertEquals('pageTestUpdate', $this->PagesService->get($id)->content->name);
    }

    /**
     * 削除
     *
     * Controller::requestAction() で呼び出される
     */
    public function testAdmin_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 固定ページファイルを登録する
     */
    public function testAdmin_entry_page_files()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 固定ページファイルを登録する
     */
    public function testAdmin_write_page_files()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ビューを表示する
     */
    public function testDisplay()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コピー
     */
    public function testAdmin_ajax_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
