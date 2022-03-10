<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Service;

use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
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
        'plugin.BaserCore.SiteConfigs',
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
        Router::reload();
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
        $this->assertEquals('baserCMSサンプル', $contentFolder->folder_template);
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
        // 論理削除されているコンテンツに紐付いている場合
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->expectExceptionMessage('Record not found in table "contents"');
        $this->ContentFolderService->getTrash(1);
    }

    /**
     * Test getIndex
     *
     * @return void
     */
    public function testGetIndex()
    {
        $contentFolders = $this->ContentFolderService->getIndex();
        $this->assertEquals('baserCMSサンプル', $contentFolders->first()->folder_template);
        $this->assertEquals(10, $contentFolders->count());
    }
    /**
     * Test create
     */
    public function testCreate()
    {
        Router::setRequest($this->loginAdmin($this->getRequest()));
        $data = [
            'folder_template' => 'テストcreate',
            'content' => [
                "parent_id" => "1",
                "title" => "新しい フォルダー",
                "plugin" => 'BaserCore',
                "type" => "ContentFolder",
                "site_id" => "1",
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
        Router::setRequest($this->loginAdmin($this->getRequest()));
        $newContentFolder = $this->ContentFolderService->getIndex(['folder_template' => "testEdit"])->first();
        $newContentFolder->folder_template = "testUpdate";
        $newContentFolder->content->title = "contentFolderTestUpdate";
        $newContentFolder->content->name = "contentFolderTestUpdate";
        $oldContentFolder = $this->ContentFolderService->get($newContentFolder->id);
        $result = $this->ContentFolderService->update($oldContentFolder, $newContentFolder->toArray());
        $this->assertEquals("testUpdate", $result->folder_template);
        $this->assertEquals("contentFolderTestUpdate", $result->content->name);
    }

    /**
     * 親のテンプレートを取得する
     * @param  int $id
     * @param  string $type
     * @param  string $expected
     * @return void
     * @dataProvider getParentTemplateDataProvider
     */
    public function testGetParentTemplate($id, $type, $expected)
    {
        $this->assertEquals($expected, $this->ContentFolderService->getParentTemplate($id, $type));
    }

    public function getParentTemplateDataProvider()
    {
        return [
            [1, 'folder', 'default'],
             // 親フォルダ（サービス）のfolder_templateを取得できるか確認
            [11, 'folder', 'サービスフォルダー'],
            [1, 'page', 'default'],
            // 親フォルダ（サービス）のpage_templateを取得できるか確認
            [11, 'page', 'サービスページ'],
        ];
    }


    /**
     * 親のテンプレートを取得する
     * @param  int $id
     * @param  string $plugins
     * @param  string $expected
     * @return void
     *
     * @dataProvider getFolderTemplateListDataProvider
     */
    public function testGetFolderTemplateList($id, $plugins, $expected)
    {
        $result = $this->ContentFolderService->getFolderTemplateList($id, $plugins);
        $this->assertEquals($expected,  $result);
    }

    public function getFolderTemplateListDataProvider()
    {
        return [
            // idが1ならgetParentTemplateに関しての処理を飛ばす
            [1, '', []],
            [4, '', ['' => "親フォルダの設定に従う（baserCMSサンプル）"]],
            // 親フォルダ（サービス）のfolder_templateを取得できるか確認
            [11, '', ['' => "親フォルダの設定に従う（サービスフォルダー）"]],
            // プラグインが存在する場合
            [4, 'BcFront', ['' => "親フォルダの設定に従う（baserCMSサンプル）", 'default' => 'default']],
        ];
    }


    /**
     * testSaveSiteRoot
     *
     * @return void
     */
    public function testSaveSiteRoot(): void
    {
        Router::setRequest($this->loginAdmin($this->getRequest()));
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        // 初期サイトIDの場合はfalse
        $site = $sites->get(1);
        $this->assertFalse($this->ContentFolderService->saveSiteRoot($site, true));
        // サイト新規作成の場合
        $site = $sites->get(3);
        $site->setNew(true);
        $site->alias = 'create';
        $contentFolder = $this->ContentFolderService->saveSiteRoot($site);
        $this->assertEquals('create', $contentFolder->content->name);
        // サイト更新の場合
        $site = $sites->get(3);
        $site->alias = 'update';
        $contentFolder = $this->ContentFolderService->saveSiteRoot($site, true);
        $this->assertEquals('update', $contentFolder->content->name);
        $updatedChild = $this->Contents->get(25);
        $this->assertEquals('/update/サイトID3の固定ページ', $updatedChild->url);
        // エラーが出る場合
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->expectExceptionMessage('Record not found in table "content_folders"');
        $site = $sites->get(6);
        $site->alias = 'update';
        $contentFolder = $this->ContentFolderService->saveSiteRoot($site, true);

    }
}
