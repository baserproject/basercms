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
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Controller\Admin\ContentFoldersController;

/**
 * Class ContentFoldersControllerTest
 *
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
        $this->ContentFolders = $this->getTableLocator()->get('BaserCore.ContentFolders');
        $this->ContentFoldersService = new ContentFoldersService();
        $this->Contents = $this->getTableLocator()->get('BaserCore.Contents');
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
     * コンテンツ編集
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = $this->ContentFoldersService->getIndex(['folder_template' => "testEdit"])->first();
        $data->folder_template = 'testEditテンプレート';
        $data->content->name = "contentFolderTestUpdate";
        $id = $data->id;
        $this->post('/baser/admin/baser-core/content_folders/edit/' . $id, ['ContentFolders' => $data->toArray(), "Contents" => ['title' => $data->content->name, 'parent_id' => $data->content->parent_id]]);
        $this->assertResponseSuccess();
        $this->assertRedirect('/baser/admin/baser-core/content_folders/edit/' . $id);
        $this->assertEquals('testEditテンプレート', $this->ContentFoldersService->get($id)->folder_template);
        $this->assertEquals('contentFolderTestUpdate', $this->ContentFoldersService->get($id)->content->name);
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
