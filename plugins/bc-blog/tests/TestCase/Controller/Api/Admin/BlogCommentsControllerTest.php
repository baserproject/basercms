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

namespace BcBlog\Test\TestCase\Controller\Api\Admin;

use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Api\BlogCommentsController;
use BcBlog\Service\BlogCommentsServiceInterface;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogCommentsServiceScenario;
use BcBlog\Test\Scenario\BlogContentScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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
     * test view
     */
    public function test_view()
    {
        // 準備: コメントを作成する
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);
        // 正常系実行
        $this->get('/baser/api/admin/bc-blog/blog_comments/view/2.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // 取得されたコメントのidを確認する
        $this->assertEquals(2, $result->blogComment->id);
        //異常系実行
        $this->get('/baser/api/admin/bc-blog/blog_comments/view/111.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
    }


    /**
     * test delete
     */
    public function test_delete()
    {
        // コメントを作成する
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);
        // APIを叩く
        $this->post('/baser/api/admin/bc-blog/blog_comments/delete/1.json?token=' . $this->accessToken);
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
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);

        //// 公開バッチ処理コール
        $this->post('/baser/api/admin/bc-blog/blog_comments/batch.json?token=' . $this->accessToken, [
            'batch' => 'publish',
            'batch_targets' => [1, 2, 3]
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
        $this->assertEquals('ブログコメント「1, 2, 3」を 公開 しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogComments', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        //// 非公開バッチ処理コール
        $this->post('/baser/api/admin/bc-blog/blog_comments/batch.json?token=' . $this->accessToken, [
            'batch' => 'unpublish',
            'batch_targets' => [1, 2, 3]
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
        $this->assertEquals('ブログコメント「1, 2, 3」を 非公開に しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogComments', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        //// 削除バッチ処理コール
        $this->post('/baser/api/admin/bc-blog/blog_comments/batch.json?token=' . $this->accessToken, [
            'batch' => 'delete',
            'batch_targets' => [1, 2, 3]
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
        $this->assertEquals('ブログコメント「1, 2, 3」を 削除 しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogComments', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        // error
        // 無効なキーを指定
        $this->post('/baser/api/admin/bc-blog/blog_comments/batch.json?token=' . $this->accessToken, [
            'batch' => 'new',
            'batch_targets' => [11, 21]
        ]);
        $this->assertResponseCode(500);
    }

    /**
     * test add
     */
    public function test_add()
    {
//        SiteFactory::make()->main()->persist();
        SiteConfigFactory::make(['name' => 'email', 'value' => 'foo@example.com'])->persist();
        SiteConfigFactory::make(['name' => 'formal_name', 'value' => 'test'])->persist();
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'status' => true])->persist();
        $postData = [
            'blog_content_id' => 1,
            'blog_post_id' => 1,
            'message' => 'いいね！'
        ];

        $this->post('/baser/api/admin/bc-blog/blog_comments/add.json?token=' . $this->accessToken, $postData);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->blogComment);

        //blog_post_idを指定しない場合、
        $postData = [
            'blog_post_id' => '',
            'blog_content_id' => 1,
            'message' => 'いいね！'
        ];
        $this->post('/baser/api/admin/bc-blog/blog_comments/add.json?token=' . $this->accessToken, $postData);
        //ステータスを確認
        $this->assertResponseCode(500);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに blog_post_id が指定されていません。', $result->message);

        //blog_content_idを指定しない場合、
        $postData = [
            'blog_post_id' => 1,
            'blog_content_id' => '',
            'message' => 'いいね！'
        ];
        $this->post('/baser/api/admin/bc-blog/blog_comments/add.json?token=' . $this->accessToken, $postData);
        //ステータスを確認
        $this->assertResponseCode(500);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに blog_content_id が指定されていません。', $result->message);
    }

    /**
     * test index
     */
    public function test_index()
    {
        // 準備：データ生成
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);
        //正常系実行
        $this->get('/baser/api/admin/bc-blog/blog_comments/index.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(3, $result->blogComments);
        $this->assertEquals('baserCMS', $result->blogComments[0]->name);
        //異常系実行
        $this->get('/baser/api/admin/bc-blog/blog_comments/index.json?token=' . $this->refreshToken);
        $this->assertResponseCode(401);

    }
}
