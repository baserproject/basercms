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
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Api\BlogCommentsController;
use BcBlog\Service\BlogCommentsServiceInterface;
use BcBlog\Test\Factory\BlogCommentFactory;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogCommentsControllerTest
 * @property BlogCommentsController $BlogCommentsController
 */
class BlogCommentsControllerTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;

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
        'plugin.BaserCore.Factory/Dblogs',
        'plugin.BcBlog.Factory/BlogComments',
        'plugin.BcBlog.Factory/BlogContents',
        'plugin.BaserCore.Factory/Contents',
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
    public function test_index()
    {
        // ５件コメントを作成する
        BlogCommentFactory::make([], 5)->persist();

        // クエリはトークンの以外で何も設定しない場合、全てのコメントを取得する
        $this->get('/baser/api/bc-blog/blog_comments/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // コメント一覧は全て５件が返す
        $this->assertCount(5, $result->blogComments);

        // クエリを設定し(limit = 4)、該当の結果が返す
        $this->get('/baser/api/bc-blog/blog_comments/index.json?limit=4&token=' . $this->accessToken);
        $result = json_decode((string)$this->_response->getBody());
        // コメント一覧は４件が返す
        $this->assertCount(4, $result->blogComments);
    }

    /**
     * test view
     */
    public function test_view()
    {
        // ブログコメントのデータを作成する
        BlogCommentFactory::make(['id' => 1, 'message' => 'いいね！'])->persist();
        // 単一ブログコメント取得APIを叩く
        $this->get('/baser/api/bc-blog/blog_comments/view/1.json?token=' . $this->accessToken);
        // OKレスポンスを確認する
        $this->assertResponseOk();
        // レスポンスのデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, $result->blogComment->id);
        $this->assertEquals('いいね！', $result->blogComment->message);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        // コメントを作成する
        BlogCommentFactory::make(['id' => 1, 'no' => 1])->persist();

        // APIを叩く
        $this->post('/baser/api/bc-blog/blog_comments/delete/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // 削除されたコメントのidを確認する
        $this->assertEquals(1, $result->blogComment->id);
        // レスポンスのメッセージを確認する
        $this->assertEquals('ブログコメント「1」を削除しました。', $result->message);
    }

    /**
     * test batch
     */
    public function test_batch()
    {
        // サービスクラス
        $blogCommentsService = $this->getService(BlogCommentsServiceInterface::class);
        $dblogsService = $this->getService(DblogsServiceInterface::class);

        // データ生成
        BlogCommentFactory::make([
            'id' => 21,
            'name' => 'blog-comment-batch',
            'blog_content_id' => 21,
            'status' => false,
        ])->persist();
        BlogCommentFactory::make([
            'id' => 22,
            'name' => 'blog-comment-batch',
            'blog_content_id' => 21,
            'status' => false,
        ])->persist();

        //// 公開バッチ処理コール
        $this->post('/baser/api/bc-blog/blog_comments/batch.json?token=' . $this->accessToken, [
            'batch' => 'publish',
            'batch_targets' => [21, 22]
        ]);
        $this->assertResponseOk();
        // 処理完了メッセージ
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);
        // データが更新されていること
        $datas = $blogCommentsService->getIndex([])->all();
        foreach ($datas as $value) {
            $this->assertTrue($value->status);
        }
        // dblogsが生成されていること
        $dblogsData = $dblogsService->getDblogs(1)->toArray()[0];
        $this->assertEquals('ブログコメント「21, 22」を 公開 しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogComments', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        //// 非公開バッチ処理コール
        $this->post('/baser/api/bc-blog/blog_comments/batch.json?token=' . $this->accessToken, [
            'batch' => 'unpublish',
            'batch_targets' => [21, 22]
        ]);
        $this->assertResponseOk();
        // 処理完了メッセージ
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);
        // データが更新されていること
        $datas = $blogCommentsService->getIndex([])->all();
        foreach ($datas as $value) {
            $this->assertFalse($value->status);
        }
        // dblogsが生成されていること
        $dblogsData = $dblogsService->getDblogs(1)->toArray()[0];
        $this->assertEquals('ブログコメント「21, 22」を 非公開に しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogComments', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        //// 削除バッチ処理コール
        $this->post('/baser/api/bc-blog/blog_comments/batch.json?token=' . $this->accessToken, [
            'batch' => 'delete',
            'batch_targets' => [21, 22]
        ]);
        $this->assertResponseOk();
        // 処理完了メッセージ
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);
        // データ削除されていること
        $data = $blogCommentsService->getIndex([])->count();
        $this->assertEquals(0, $data);
        // dblogsが生成されていること
        $dblogsData = $dblogsService->getDblogs(1)->toArray()[0];
        $this->assertEquals('ブログコメント「21, 22」を 削除 しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogComments', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        // error
        // 無効なキーを指定
        $this->post('/baser/api/bc-blog/blog_comments/batch.json?token=' . $this->accessToken, [
            'batch' => 'new',
            'batch_targets' => [1, 2]
        ]);
        $this->assertResponseCode(500);
    }

}
