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

namespace BaserCore\Test\TestCase\Service;

use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Model\Table\ContentFoldersTable;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * BaserCore\Model\Table\ContentFoldersTable Test Case
 *
 * @property ContentFoldersService $ContentFoldersService
 * @property ContentFoldersTable $ContentFolders
 * @property ContentsTable $Contents
 */
class ContentFoldersServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var ContentFoldersService
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
        $this->ContentFoldersService = new ContentFoldersService();
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
        unset($this->ContentFoldersService);
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
        $contentFolder = $this->ContentFoldersService->get(1);
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
        $contentFolder = $this->ContentFoldersService->getTrash(10);
        $this->assertEquals('削除済みフォルダー(親)', $contentFolder->folder_template);
        $this->assertEquals(10, $contentFolder->content->entity_id);
        $this->assertEquals('メインサイト', $contentFolder->content->site->display_name);
        // 論理削除されているコンテンツに紐付いている場合
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->expectExceptionMessage('Record not found in table "contents"');
        $this->ContentFoldersService->getTrash(1);
    }

    /**
     * Test getIndex
     *
     * @return void
     */
    public function testGetIndex()
    {
        $contentFolders = $this->ContentFoldersService->getIndex();
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
        $result = $this->ContentFoldersService->create($data, $option);
        $folderExpected = $this->ContentFoldersService->ContentFolders->find()->all()->last();
        $contentExpected = $this->Contents->find()->all()->last();
        $this->assertEquals($folderExpected->name, $result->name);
        $this->assertEquals("新しい フォルダー", $contentExpected->title);
    }

    /**
     * testCreateWithFailure
     * 新規作成時に失敗した場合をテスト
     * @param  array $postData
     * @param  array $errors
     * @return void
     * @dataProvider createWithFailureDataProvider
     */
    public function testCreateWithFailure($postData, $errors)
    {
        Router::setRequest($this->loginAdmin($this->getRequest()));
        try {
            $contentFolder = $this->ContentFoldersService->create($postData);
        } catch (PersistenceFailedException $e) {
            $contentFolder = $e->getEntity();
        }
        $this->assertEquals($errors, $contentFolder->getErrors());
    }
    public function createWithFailureDataProvider()
    {
        return [
            // contentがフィールドとして存在しない場合
            [
                ['folder_template' => 'テストcreate'],
                ['content' => ['_required' => '関連するコンテンツがありません']]
            ],
            // contentの中身が足りない場合
            [
                ['folder_template' => 'テストcreate', 'content' => []],
                ['content' => [
                    'title' => ['_required' => "タイトルを入力してください。"],
                    'name' => ['_required' => "nameフィールドが存在しません。"]
                    ]]
            ],
        ];
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {
        $content = $this->Contents->find()->where(['type' => 'ContentFolder', 'entity_id' => 10])->first();
        $this->assertTrue($this->ContentFoldersService->delete($content->entity_id));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentFoldersService->get($content->entity_id);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->Contents->get($content->id);
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        Router::setRequest($this->loginAdmin($this->getRequest()));
        $newContentFolder = $this->ContentFoldersService->getIndex(['folder_template' => "testEdit"])->first();
        $newContentFolder->folder_template = "testUpdate";
        $newContentFolder->content->title = "contentFolderTestUpdate";
        $newContentFolder->content->name = "contentFolderTestUpdate";
        $oldContentFolder = $this->ContentFoldersService->get($newContentFolder->id);
        $result = $this->ContentFoldersService->update($oldContentFolder, $newContentFolder->toArray());
        $this->assertEquals("testUpdate", $result->folder_template);
        $this->assertEquals("contentFolderTestUpdate", $result->content->name);
    }

    /**
     * testUpdateWithFailure
     * 新規作成時に失敗した場合をテスト
     * @param  array $postData
     * @param  array $errors
     * @return void
     * @dataProvider updateWithFailureDataProvider
     */
    public function testUpdateWithFailure($postData, $errors)
    {
        Router::setRequest($this->loginAdmin($this->getRequest()));
        try {
            $contentFolder = $this->ContentFoldersService->getIndex(['folder_template' => "testEdit"])->first();
            $contentFolder = $this->ContentFoldersService->update($contentFolder, $postData);
        } catch (PersistenceFailedException $e) {
            $contentFolder = $e->getEntity();
        }
        $this->assertEquals($errors, $contentFolder->getErrors());
    }
    public function updateWithFailureDataProvider()
    {
        return [
            // contentがフィールドとして存在しない場合
            [
                ['folder_template' => 'テストupdate'],
                ['content' => ['_required' => '関連するコンテンツがありません']]
            ],
        ];
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
        $this->assertEquals($expected, $this->ContentFoldersService->getParentTemplate($id, $type));
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
        $result = $this->ContentFoldersService->getFolderTemplateList($id, $plugins);
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
        $this->assertFalse($this->ContentFoldersService->saveSiteRoot($site, true));
        // サイト新規作成の場合
        $site = $sites->get(3);
        $site->setNew(true);
        $site->alias = 'create';
        $contentFolder = $this->ContentFoldersService->saveSiteRoot($site);
        $this->assertEquals('create', $contentFolder->content->name);
        // サイト更新の場合
        $site = $sites->get(3);
        $site->alias = 'update';
        $contentFolder = $this->ContentFoldersService->saveSiteRoot($site, true);
        $this->assertEquals('update', $contentFolder->content->name);
        $updatedChild = $this->Contents->get(25);
        $this->assertEquals('/update/サイトID3の固定ページ', $updatedChild->url);
        // エラーが出る場合
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->expectExceptionMessage('Record not found in table "content_folders"');
        $site = $sites->get(6);
        $site->alias = 'update';
        $contentFolder = $this->ContentFoldersService->saveSiteRoot($site, true);

    }

    /**
     * test getList
     */
    public function test_getList()
    {
        $result = $this->ContentFoldersService->getList();
        $this->assertContains('testEdit', $result);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->assertEquals([], $this->ContentFoldersService->getNew()->toArray());
    }

}
