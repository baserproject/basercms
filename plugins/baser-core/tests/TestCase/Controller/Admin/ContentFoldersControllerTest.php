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
    ];
    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentFoldersController = new ContentFoldersController($this->getRequest());
        $this->ContentFolders = $this->getTableLocator()->get('ContentFolders');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $event = new Event('Controller.beforeFilter', $this->ContentFoldersController);
        $this->ContentFoldersController->beforeFilter($event);
        $this->assertNotEmpty($this->ContentFoldersController);
    }

    /**
     * コンテンツを登録する
     */
    public function testAdd()
    {
        $this->loginAdmin($this->getRequest('/baser/admin/baser-core/content_folders/add'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'folder_template'=>"testFolderTemplate",
            'page_template'=>"",
            'content'=> [
                'parent_id'=>"1",
                'title'=>"testFolderAdd",
                'plugin'=>'BaserCore',
                'type'=>"ContentFolder",
                'site_id'=>"0",
                'alias_id'=>"",
                'entity_id'=>"",
            ],
        ];
        $this->post('/baser/admin/baser-core/content_folders/add', $data);
        $this->assertResponseOk();
        $this->assertResponseContains(json_encode($data['content']['title']));
        $folderQuery = $this->ContentFolders->find()->where(['folder_template' => $data['folder_template']]);
        $contentQuery = $this->Contents->find()->where(['title' => $data['content']['title']]);
        $this->assertEquals(1, $folderQuery->count());
        $this->assertEquals(1, $contentQuery->count());
    }

    /**
     * コンテンツを更新する
     */
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
