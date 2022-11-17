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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\Test\Factory\PageFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use Cake\Core\Configure;
use BaserCore\Service\ContentsService;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * ContentsControllerTest
 * @property ContentsService $ContentsService
 */
class ContentsControllerTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Service/SearchIndexesService/ContentsReconstruct',
        'plugin.BaserCore.Service/SearchIndexesService/PagesReconstruct',
        'plugin.BaserCore.Service/SearchIndexesService/ContentFoldersReconstruct',
        'plugin.BaserCore.Dblogs',
        'plugin.BcBlog.Factory/BlogContents'
    ];

    public $autoFixtures = false;

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures(
            'Users',
            'UserGroups',
            'UsersUserGroups',
            'Contents',
            'ContentFolders',
            'Sites',
            'SiteConfigs',
            'Pages',
//            'SearchIndexes'
        );
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
        $this->ContentsService = new ContentsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * testView
     *
     * @return void
     */
    public function testView(): void
    {
        $this->get('/baser/api/baser-core/contents/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('baserCMSサンプル', $result->content->title);
    }

    /**
     * testview_trash
     *
     * @return void
     */
    public function testView_trash(): void
    {
        $this->get('/baser/api/baser-core/contents/view_trash/16.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('削除済みフォルダー(親)', $result->trash->title);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        // indexテスト
        $this->get('/baser/api/baser-core/contents/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('', $result->contents[0]->name);
        // trashテスト
        $this->get('/baser/api/baser-core/contents/index/trash.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->contents[0]->deleted_date);
        // treeテスト
        $this->get('/baser/api/baser-core/contents/index/tree.json?site_id=1&token=' . $this->accessToken);
        $this->assertResponseOk();
        // tableテスト
        $this->get('/baser/api/baser-core/contents/index/table.json?site_id=1&folder_id=6&name=サービス&type=Page&token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(3, count($result->contents));
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        // 子要素を持たない場合
        $data = ['id' => 4];
        $this->post('/baser/api/baser-core/contents/delete.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("コンテンツ: トップページを削除しました。", $result->message);
        $this->get('/baser/api/baser-core/contents/view/4.json?token=' . $this->accessToken);
        $this->assertResponseError();
        // 子要素を持つ場合
        $data = ['id' => 6];
        $this->post('/baser/api/baser-core/contents/delete.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $this->get('/baser/api/baser-core/contents/view/6.json?token=' . $this->accessToken); // 親要素削除チェック
        $this->assertResponseError();
        $this->get('/baser/api/baser-core/contents/view/11.json?token=' . $this->accessToken); // 子要素削除チェック
        $this->assertResponseError();
    }

    /**
     * testtrash_empty
     *
     * @return void
     */
    public function testTrash_empty()
    {
        $this->post('/baser/api/baser-core/contents/trash_empty.json?type=ContentFolder&token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("ゴミ箱を空にしました。", $result->message);
        $this->get('/baser/api/baser-core/contents/index/trash.json?type=ContentFolder&token=' . $this->accessToken);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEmpty($result->contents);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $data = $this->ContentsService->getIndex(['name' => 'testEdit'])->first();
        $id = $data->id;
        $data->name = 'ControllerEdit';
        $data->site->name = 'ucmitz'; // site側でエラーが出るため
        $this->post("/baser/api/baser-core/contents/edit/${id}.json?token=" . $this->accessToken, $data->toArray());
        $this->assertResponseSuccess();
        // updateRelateSubSiteContentにより、連携されてるサイトに同コンテンツが生成されるため2個となる
        $query = $this->ContentsService->getIndex(['name' => 'ControllerEdit']);
        $this->assertEquals(2, $query->count());
    }

    /**
     * testTrash_return
     *
     * @return void
     */
    public function testTrash_return()
    {
        $id = $this->ContentsService->getTrashIndex()->first()->id;
        $this->get("/baser/api/baser-core/contents/trash_return/{$id}.json?token=" . $this->accessToken);
        $this->assertResponseOk();
        $this->assertNotEmpty($this->ContentsService->get($id));
    }

    /**
     * testChange_status
     * NOTE: publishとunPublishのテストを同じ場所に書くとupdateDataが走らないため分離
     * @return void
     */
    public function testChange_status_toUnpublish()
    {
        $this->loadFixtures(
            'Service\SearchIndexesService\ContentsReconstruct'
        );
        // 従来のフィクスチャで足りないデータを追加
        PageFactory::make([['id' => 1], ['id' => 4]])->persist();
        $data = ['id' => 1, 'status' => 'unpublish'];
        $this->patch("/baser/api/baser-core/contents/change_status.json?token=" . $this->accessToken, $data);
        $this->assertResponseOk();
        $this->assertFalse($this->ContentsService->get($data['id'])->status);
    }

    /**
     * testChange_status
     *
     * @return void
     */
    public function testChange_status_toPublish()
    {
        $this->loadFixtures(
            'Service\SearchIndexesService\ContentsReconstruct'
        );
        // 従来のフィクスチャで足りないデータを追加
        PageFactory::make([['id' => 1], ['id' => 4]])->persist();
        $content = $this->ContentsService->get(1);
        $this->ContentsService->update($content, ['id' => $content->id, 'status' => false, 'name' => 'test']);
        $data = ['id' => 1, 'status' => 'publish'];
        $this->patch("/baser/api/baser-core/contents/change_status.json?token=" . $this->accessToken, $data);
        $this->assertResponseOk();
        $this->assertTrue($this->ContentsService->get($data['id'])->status);
    }

    /**
     * testGet_full_url
     *
     * @return void
     */
    public function testGet_full_url()
    {
        $this->get("/baser/api/baser-core/contents/get_full_url/1.json?token=" . $this->accessToken);
        $this->assertResponseOk();
        $this->assertEquals("https://localhost/", json_decode((string)$this->_response->getBody())->fullUrl);
    }

    /**
     * testExists
     *
     * @return void
     */
    public function testExists()
    {
        $this->get("/baser/api/baser-core/contents/exists/1.json?token=" . $this->accessToken);
        $this->assertResponseOk();
        $this->assertTrue(json_decode($this->_response->getBody())->exists);
        $this->get("/baser/api/baser-core/contents/exists/100.json?token=" . $this->accessToken);
        $this->assertResponseOk();
        $this->assertFalse(json_decode($this->_response->getBody())->exists);
    }

    /**
     * リネーム
     *
     * 新規登録時の初回リネーム時は、name にも保存する
     */
    public function testRename()
    {
        BlogContentFactory::make(['id' => 31, 'description' => ''])->persist();
        $this->patch("/baser/api/baser-core/contents/rename.json?token=" . $this->accessToken);
        $this->assertResponseFailure();
        $data = ['id' => 6, 'title' => 'testRename'];
        $this->patch("/baser/api/baser-core/contents/rename.json?token=" . $this->accessToken, $data);
        $this->assertResponseOk();
        $this->assertStringContainsString('testRename', json_decode($this->_response->getBody())->message);
        $this->assertNotNull(json_decode($this->_response->getBody())->url);
    }

    /**
     * testAdd_alias
     *
     * @return void
     */
    public function testAdd_alias()
    {
        $content = $this->ContentsService->get(1);
        $data = [
            'content' => [
                "parent_id" => $content->parent_id,
                "title" => 'テストエイリアス',
                "plugin" => $content->plugin,
                "type" => $content->type,
                "site_id" => $content->site_id,
                "alias_id" => $content->id,
                "entity_id" => $content->entity_id,
            ]];
        $this->post("/baser/api/baser-core/contents/add_alias.json?token=" . $this->accessToken, $data);
        $this->assertResponseOk();
        $this->assertNotEmpty(json_decode($this->_response->getBody())->content);
        $this->assertEquals("テストエイリアス を作成しました。", json_decode($this->_response->getBody())->message);
    }

    /**
     * testGet_content_folder_list
     *
     * @return void
     */
    public function testGet_content_folder_list()
    {
        $this->get("/baser/api/baser-core/contents/get_content_folder_list/1.json?token=" . $this->accessToken);
        $this->assertResponseOk();
        $this->assertNotEmpty(json_decode($this->_response->getBody())->list);
    }

    /**
     * testGet_content_folder_list
     *
     * @return void
     */
    public function testIs_unique_content()
    {
        $this->post("/baser/api/baser-core/contents/is_unique_content.json?token=" . $this->accessToken);
        $this->assertResponseFailure();
        $this->post("/baser/api/baser-core/contents/is_unique_content.json?token=" . $this->accessToken, ['url' => 'aaaa']);
        $this->assertTrue(json_decode($this->_response->getBody()));
        $this->post("/baser/api/baser-core/contents/is_unique_content.json?token=" . $this->accessToken, ['url' => '/service/service2']);
        $this->assertFalse(json_decode($this->_response->getBody()));
    }

    /**
     * testMove
     *
     * @return void
     */
    public function testMove()
    {
        // postDataがない場合
        $this->patch("/baser/api/baser-core/contents/move.json?token=" . $this->accessToken);
        $this->assertEquals('データ保存中にエラーが発生しました。Record not found in table "contents" with primary key [NULL]', json_decode($this->_response->getBody())->message);
        // サービス1をサービス2の後ろに移動する場合
        $title = 'サービス１';
        $originEntity = $this->ContentsService->getIndex(['title' => $title])->first();
        $targetEntity = $this->ContentsService->getIndex(['title' => 'サービス３'])->first();
        $data = [
            // 移動元
            'origin' => [
                'id' => $originEntity->id,
                'parentId' => $originEntity->parent_id
            ],
            // 移動先
            'target' => [
                'id' => $targetEntity->id,
                'parentId' => "1",
                'siteId' => "1",
            ]
        ];
        $this->patch("/baser/api/baser-core/contents/move.json?token=" . $this->accessToken, $data);
        $this->assertEquals("コンテンツ「${title}」の配置を移動しました。\n/service/service1 > /service1", json_decode($this->_response->getBody())->message);
        $service2Left = $this->ContentsService->get(($originEntity->id + $targetEntity->id) / 2)->lft;
        $this->assertGreaterThan($service2Left, json_decode($this->_response->getBody())->content->lft);
    }

    /**
     * testBatch
     *
     * @return void
     */
    public function testBatch()
    {
        // 空データ送信
        $this->post('/baser/api/baser-core/contents/batch.json?token=' . $this->accessToken, []);
        $this->assertResponseFailure();
        // delete
        $data = [
            'batch' => 'delete',
            'batch_targets' => [1],
        ];
        $this->post('/baser/api/baser-core/contents/batch.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->ContentsService->get(1);
    }

    /**
     * testBatchUnpublish
     * NOTE: publishとunPublishのテストを同じ場所に書くとupdateDataが走らないため分離
     *
     * @return void
     */
    public function testBatchUnpublish()
    {
        BlogContentFactory::make(['id' => 31, 'description' => ''])->persist();
        // unpublish
        $data = [
            'batch' => 'unpublish',
            'batch_targets' => [6],
        ];
        $this->post('/baser/api/baser-core/contents/batch.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $content = $this->ContentsService->get(6);
        $this->assertFalse($content->status);
    }

    /**
     * testBatchUnpublish
     *
     * @return void
     */
    public function testBatchPublish()
    {
        $content = $this->ContentsService->get(4);
        $this->ContentsService->update($content, ['id' => $content->id, 'status' => false, 'name' => 'test']);
        // publish
        $data = [
            'batch' => 'publish',
            'batch_targets' => [6],
        ];
        $this->post('/baser/api/baser-core/contents/batch.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $content = $this->ContentsService->get(6);
        $this->assertTrue($content->status);
    }

}
