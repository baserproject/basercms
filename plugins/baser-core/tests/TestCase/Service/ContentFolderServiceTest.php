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
        $this->assertEquals(6, $contentFolders->count());
    }
    /**
     * Test create
     */
    public function testCreate()
    {
        $this->loginAdmin($this->getRequest());
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
        $option = ['validate' => 'default'];
        $result = $this->ContentFolderService->create($data, $option);
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
        $newContentFolder = $this->ContentFolderService->getIndex(['folder_template' => "testEdit"])->first();
        $newContentFolder->folder_template = "testUpdate";
        $newContentFolder->content->title = "contentFolderTestUpdate";
        $newContentFolder->content->name = "contentFolderTestUpdate";
        $oldContentFolder = $this->ContentFolderService->get($newContentFolder->id);
        $result = $this->ContentFolderService->update($oldContentFolder, $newContentFolder->toArray());
        $this->assertEquals("testUpdate", $result->folder_template);
        $this->assertEquals("contentFolderTestUpdate", $result->content->name);
    }
}
