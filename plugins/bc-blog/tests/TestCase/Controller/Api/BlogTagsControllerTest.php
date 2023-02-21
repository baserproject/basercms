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

namespace BcBlog\Test\TestCase\Controller\Api;

use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Api\BlogTagsController;
use BcBlog\Test\Factory\BlogTagFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogTagsControllerTest
 * @property BlogTagsController $BlogTagsController
 */
class BlogTagsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BcBlog.Factory/BlogTags',
        'plugin.BaserCore.Factory/Dblogs',
    ];

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
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     */
    public function testIndex()
    {
        // ５件タグを作成する
        BlogTagFactory::make([])->persist();
        BlogTagFactory::make([])->persist();
        BlogTagFactory::make([])->persist();
        BlogTagFactory::make([])->persist();
        BlogTagFactory::make([])->persist();

        // クエリはトークンの以外で何も設定しない場合、全てのタグを取得する
        $this->get('/baser/api/bc-blog/blog_tags/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // タグ一覧は全て５件が返す
        $this->assertCount(5, $result->blogTags);

        // クエリを設定し(limit = 4)、該当の結果が返す
        $this->get('/baser/api/bc-blog/blog_tags/index.json?limit=4&token=' . $this->accessToken);
        $result = json_decode((string)$this->_response->getBody());
        // タグ一覧は４件が返す
        $this->assertCount(4, $result->blogTags);
    }

    /**
     * test view
     */
    public function testView()
    {
        // ブログタグのデータを作成する
        BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();
        // 単一ブログタグー取得APIを叩く
        $this->post('/baser/api/bc-blog/blog_tags/view/1.json?token=' . $this->accessToken);
        // OKレスポンスを確認する
        $this->assertResponseOk();
        // レスポンスのデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, $result->blogTag->id);
        $this->assertEquals('tag1', $result->blogTag->name);
    }

    /**
     * test add
     */
    public function testAdd()
    {
        // ブログタグ名
        $data = [
            'name' => 'test tag add',
        ];
        // ブログタグ登録コール
        $this->post('/baser/api/bc-blog/blog_tags/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログタグ「test tag add」を追加しました。', $result->message);
        $this->assertEquals(1, $result->blogTag->id);
        $this->assertEquals('test tag add', $result->blogTag->name);
        $this->assertNotEmpty($result->blogTag->created);
        $this->assertNotEmpty($result->blogTag->modified);

        // error
        // 同じブログタグを登録
        $this->post('/baser/api/bc-blog/blog_tags/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
    }

    /**
     * test edit
     */
    public function testEdit()
    {
        // タグのデータを作成する
        BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();

        // 編集が成功のテスト
        $this->post('/baser/api/bc-blog/blog_tags/edit/1.json?token=' . $this->accessToken, ['name' => 'tag2']);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // 成功のメッセージを確認する
        $this->assertEquals('ブログタグ「tag2」を更新しました。', $result->message);
        // 編集した後のタグデータを確認する
        $this->assertEquals(1, $result->blogTag->id);
        $this->assertEquals('tag2', $result->blogTag->name);

        // 編集が失敗のテスト
        $this->post('/baser/api/bc-blog/blog_tags/edit/1.json?token=' . $this->accessToken, ['name' => '']);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        // 編集が失敗の場合、データが変わらないことを確認する
        $this->assertEquals('tag2', $result->blogTag->name);
        // レスポンスのメッセージとエラーメッセージを確認する
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('ブログタグを入力してください。', $result->errors->name->_empty);
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        // タグのデータを作成する
        BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();

        // タグ削除APIを叩く
        $this->post('/baser/api/bc-blog/blog_tags/delete/1.json?token=' . $this->accessToken);
        // レスポンスのステータスを確認する
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのメッセージを確認する
        $this->assertEquals('ブログタグ「tag1」を削除しました。', $result->message);
        // 削除されたタグのidを確認する
        $this->assertEquals(1, $result->blogTag->id);
    }

    /**
     * test batch
     */
    public function testBatch()
    {
        // delete以外のHTTPメソッドには500エラーを返す
        $this->post('/baser/api/bc-blog/blog_tags/batch.json?token=' . $this->accessToken, ['batch' => 'test']);
        $this->assertResponseCode(500);

        // データを作成する
        BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();
        BlogTagFactory::make(['id' => 2, 'name' => 'tag2'])->persist();

        $data = [
            'batch' => 'delete',
            'batch_targets' => [1, 2],
        ];
        $this->post('/baser/api/bc-blog/blog_tags/batch.json?token=' . $this->accessToken, $data);
        // バッチの実行が成功
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのメッセージを確認する
        $this->assertEquals('一括処理が完了しました。', $result->message);
        // DBログに保存するかどうか確認する
        $dbLogService = $this->getService(DblogsServiceInterface::class);
        $dbLog = $dbLogService->getDblogs(1)->toArray()[0];
        $this->assertEquals('ブログタグ「tag1」、「tag2」を 削除 しました。', $dbLog->message);
        $this->assertEquals(1, $dbLog->id);
        $this->assertEquals('BlogTags', $dbLog->controller);
        $this->assertEquals('batch', $dbLog->action);
    }

}
