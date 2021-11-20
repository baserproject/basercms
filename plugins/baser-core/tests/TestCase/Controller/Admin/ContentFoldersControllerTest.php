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
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Service\ContentFolderService;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Controller\Admin\ContentFoldersController;

/**
 * Class ContentFoldersControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  ContentFoldersController $ContentFoldersController
 * @property ContentFoldersTable $ContentFolders
 * @property ContentsTable $Contents
 */
class ContentFoldersControllerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
    ];
    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin($this->getRequest('/baser/admin/baser-core/content_folders'));
        $this->ContentFoldersController = new ContentFoldersController($this->getRequest());
        $this->ContentFolders = $this->getTableLocator()->get('ContentFolders');
        $this->ContentFolderService = new ContentFolderService();
        $this->Contents = $this->getTableLocator()->get('Contents');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->ContentFoldersController);
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->ContentFoldersController->BcAdminContents);
    }

    /**
     * Before Filter
     */
    public function testBeforeFilter()
    {
        $event = new Event('Controller.beforeFilter', $this->ContentFoldersController);
        $this->ContentFoldersController->beforeFilter($event);
        $this->assertNotEmpty($this->ContentFoldersController);
        $this->assertNotEmpty($this->ContentFoldersController->ContentFolders);

    }

    /**
     * コンテンツ編集
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = $this->ContentFolderService->getIndex(['folder_template' => "testEdit"])->first();
        $data->folder_template = 'testEditテンプレート';
        $data->content->name = "contentFolderTestUpdate";
        $id = $data->id;
        $this->post('/baser/admin/baser-core/content_folders/edit/' . $id, ['ContentFolder' => $data->toArray()]);
        $this->assertResponseSuccess();
        $this->assertRedirect('/baser/admin/baser-core/content_folders/edit/' . $id);
        $this->assertEquals('testEditテンプレート', $this->ContentFolderService->get($id)->folder_template);
        $this->assertEquals('contentFolderTestUpdate', $this->ContentFolderService->get($id)->content->name);
    }

    /**
     * コンテンツを削除する
     */
    public function testAdmin_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンテンツを表示する
     */
    public function testView()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
