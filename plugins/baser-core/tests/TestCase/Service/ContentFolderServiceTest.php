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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Service\ContentFolderService;
use BaserCore\Model\Table\ContentFoldersTable;

/**
 * BaserCore\Model\Table\ContentFoldersTable Test Case
 *
 * @property ContentFolderService $ContentFolderService
 * @property ContentFoldersTable $ContentFolders
 * @property ContentsTable $Contents
 */
class ContentFolderServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var ContentFolderService
     */
    public $ContentFolders;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin($this->getRequest());
        $this->ContentFolderService = new ContentFolderService();
        $this->Contents = $this->getTableLocator()->get('Contents');
        $this->ContentFolders = $this->getTableLocator()->get('ContentFolders');
    }
    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentFolderService);
        parent::tearDown();
    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        $contentFolder = $this->ContentFolderService->get(1);
        $this->assertEquals('フォルダーテンプレート1', $contentFolder->folder_template);
        $this->assertEquals(1, $contentFolder->content->entity_id);
        $this->assertEquals('メインサイト', $contentFolder->content->site->display_name);
    }
    /**
     * Test getTrash
     *
     * @return void
     */
    public function testGetTrash()
    {
        $contentFolder = $this->ContentFolderService->getTrash(10);
        $this->assertEquals('削除済みフォルダー(親)', $contentFolder->folder_template);
        $this->assertEquals(10, $contentFolder->content->entity_id);
        $this->assertEquals('メインサイト', $contentFolder->content->site->display_name);
    }

    /**
     * Test getIndex
     *
     * @return void
     */
    public function testGetIndex()
    {
        $contentFolders = $this->ContentFolderService->getIndex();
        $this->assertEquals('フォルダーテンプレート1', $contentFolders->first()->folder_template);
        $this->assertEquals(5, $contentFolders->count());
    }
    /**
     * Test create
     */
    public function testCreate()
    {
        $data = [
            'folder_template' => 'テストcreate',
            'content' => [
                "parent_id" => "1",
                "title" => "新しい フォルダー",
                "plugin" => 'BaserCore',
                "type" => "ContentFolder",
                "site_id" => "0",
                "alias_id" => "",
                "entity_id" => "",
            ],
        ];
        $result = $this->ContentFolderService->create($data);
        $folderExpected = $this->ContentFolderService->ContentFolders->find()->last();
        $contentExpected = $this->Contents->find()->last();
        $this->assertEquals($folderExpected->name, $result->name);
        $this->assertEquals("新しい フォルダー", $contentExpected->title);
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {
        $content = $this->Contents->find()->where(['type' => 'ContentFolder', 'entity_id' => 10])->first();
        $this->assertTrue($this->ContentFolderService->delete($content->entity_id));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentFolderService->get($content->entity_id);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->Contents->get($content->id);
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $data = [
            'Content' => [
                'id' => 1,
                'name' => '',
                'plugin' => 'BaserCore',
                'type' => 'ContentFolder',
                'entity_id' => 1,
                'url' => '/',
                'site_id' => 1,
                'alias_id' => null,
                'main_site_content_id' => null,
                'parent_id' => null,
                'lft' => 1,
                'rght' => 34,
                'level' => 0,
                'title' => 'testUpdatebaserCMSサンプル',
                'description' => '',
                'eyecatch' => '',
                'author_id' => 1,
                'layout_template' => 'default',
                'status' => true,
                'publish_begin' => null,
                'publish_end' => null,
                'self_status' => true,
                'self_publish_begin' => null,
                'self_publish_end' => null,
                'exclude_search' => false,
                'created_date' => null,
                'modified_date' => '2019-06-11 12:27:01',
                'site_root' => true,
                'deleted_date' => null,
                'exclude_menu' => false,
                'blank_link' => false,
                'created' => '2016-07-29 18:02:53',
                'modified' => '2020-09-14 21:10:41',
            ],
            'ContentFolder' => [
                'id' => '1',
                'folder_template' => 'フォルダーテンプレートtestUpdate',
            ]
        ];
        $contentFolder = $this->ContentFolderService->get($data["ContentFolder"]["id"]);
        $result = $this->ContentFolderService->update($contentFolder, $data);
        $contentFolders = $this->ContentFolderService->getIndex();
        $this->assertEquals($contentFolders->first()->name, $data['name']);
    }
}
