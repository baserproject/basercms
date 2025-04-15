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
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Api\BlogPostsController;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogPostsControllerTest
 * @property BlogPostsController $BlogPostsController
 */
class BlogPostsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

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
     * test index
     */
    public function test_index()
    {
        //準備
        //データを生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make(['id' => 11, 'blog_content_id' => 1, 'title' => 'bl title 11'])->persist();
        BlogPostFactory::make(['id' => 22, 'blog_content_id' => 1, 'title' => 'bl title 22'])->persist();

        //正常の時を確認
        //APIをコル
        $this->get('/baser/api/admin/bc-blog/blog_posts/index.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->blogPosts);
        $this->assertEquals('bl title 11', $result->blogPosts[0]->title);
    }


    /**
     * test add
     */
    public function test_add()
    {
        // postデータを生成
        $postData = [
            'user_id' => 1,
            'posted' => '2022-12-01 00:00:00',
            'blog_content_id' => 1,
            'title' => 'baserCMS inc. [デモ] の新しい記事',
            'content' => '記事の概要',
            'detail' => '記事の詳細',
        ];
        // APIを呼ぶ
        $this->post('/baser/api/admin/bc-blog/blog_posts/add.json?token=' . $this->accessToken, $postData);
        // レスポンスの確認
        $this->assertResponseOk();
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認
        $this->assertEquals('記事「baserCMS inc. [デモ] の新しい記事」を追加しました。', $result->message);
        //作成したBlogPostを確認
        $this->assertEquals('baserCMS inc. [デモ] の新しい記事', $result->blogPost->title);
        $this->assertEquals('記事の概要', $result->blogPost->content);
        $this->assertEquals('記事の詳細', $result->blogPost->detail);

        // 入力エラー
        // titleが空のpostデータを生成
        $postData = [
            'blog_content_id' => 1,
            'title' => '',
            'content' => '',
            'detail' => '',
        ];
        // APIを呼ぶ
        $this->post('/baser/api/admin/bc-blog/blog_posts/add.json?token=' . $this->accessToken, $postData);
        // レスポンスの確認
        $this->assertResponseCode(400);
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        //エラーメッセージを確認
        $this->assertEquals('タイトルを入力してください。', $result->errors->title->_empty);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        //データを生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1])->persist();

        //正常の時を確認
        //編集データーを生成
        $data = ['title' => 'blog post edit'];
        //APIをコル
        $this->post('/baser/api/admin/bc-blog/blog_posts/edit/1.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog post edit', $result->blogPost->title);
        $this->assertEquals('記事「blog post edit」を更新しました。', $result->message);

        //エラーを発生した場合を確認
        //APIをコル
        $this->post('/baser/api/admin/bc-blog/blog_posts/edit/1.json?token=' . $this->accessToken, ['name' => str_repeat('a', 256)]);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('スラッグは255文字以内で入力してください。', $result->errors->name->maxLength);
    }

    /**
     * test edit
     */
    public function test_copy()
    {
        //データを生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make(['id' => 1, 'title' => 'test', 'blog_content_id' => 1])->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/admin/bc-blog/blog_posts/copy/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログ記事「test」をコピーしました。', $result->message);
        $this->assertEquals('test_copy', $result->blogPost->title);

        //存在しないBlogPostIDをコビー場合、
        $this->post('/baser/api/admin/bc-blog/blog_posts/copy/100000.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test publish
     */
    public function test_publish()
    {
        //データーを生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make([])->unpublish(1, 1)->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/admin/bc-blog/blog_posts/publish/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/ブログ記事「.+」を公開状態にしました。/', $result->message);
        // データの変更を確認
        $blogPost = BlogPostFactory::get(1);
        $this->assertEquals(true, $blogPost['status']);
        $this->assertEquals(null, $blogPost['publish_begin']);
        $this->assertEquals(null, $blogPost['publish_end']);

        //存在しないBlogPostIDを公開場合、
        //APIをコル
        $this->post('/baser/api/admin/bc-blog/blog_posts/publish/2.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test unpublish
     */
    public function test_unpublish()
    {
        //データーを生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1])->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/admin/bc-blog/blog_posts/unpublish/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/ブログ記事「.+」を非公開状態にしました。/', $result->message);
        // 非公開状態を確認
        $blogPost = BlogPostFactory::get(1);
        $this->assertFalse($blogPost->status);
        $this->assertNull($blogPost->publish_begin);
        $this->assertNull($blogPost->publish_end);

        //存在しないBlogPostIDを非公開場合、
        //APIをコル
        $this->post('/baser/api/admin/bc-blog/blog_posts/unpublish/2.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test batch
     */
    public function test_batch()
    {
        // サービスクラス
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $dblogsService = $this->getService(DblogsServiceInterface::class);

        //// 正常系のテスト
        // 非公開状態のデータを生成
        SiteConfigFactory::make(['name' => 'content_types', 'value' => ''])->persist();
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make([])->unpublish(1, 1)->persist();
        BlogPostFactory::make([])->unpublish(2, 1)->persist();

        // 公開状態にするAPIを呼ぶ
        $this->post('/baser/api/admin/bc-blog/blog_posts/batch.json?token=' . $this->accessToken, [
            'batch' => 'publish',
            'batch_targets' => [1, 2]
        ]);
        $this->assertResponseOk();
        // 処理完了メッセージ
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);
        // データが公開状態に更新されていること
        $datas = $blogPostsService->getIndex([])->all();
        foreach ($datas as $value) {
            $this->assertTrue($value->status);
        }
        //IDを指定してタイトルリストを取得
        $names = $blogPostsService->getTitlesById([1, 2]);
        // dblogsが生成されていること
        $dblogsData = $dblogsService->getDblogs(1)->toArray()[0];
        $this->assertEquals('ブログ記事「' . implode('」、「', $names) . '」を 公開 しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogPosts', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        // 非公開状態にするAPIを呼ぶ
        $this->post('/baser/api/admin/bc-blog/blog_posts/batch.json?token=' . $this->accessToken, [
            'batch' => 'unpublish',
            'batch_targets' => [1, 2]
        ]);
        $this->assertResponseOk();
        // 処理完了メッセージ
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);
        // データが非公開状態に更新されていること
        $datas = $blogPostsService->getIndex([])->all();
        foreach ($datas as $value) {
            $this->assertFalse($value->status);
        }
        // dblogsが生成されていること
        $dblogsData = $dblogsService->getDblogs(1)->toArray()[0];
        $this->assertEquals('ブログ記事「' . implode('」、「', $names) . '」を 非公開に しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogPosts', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        // 削除するAPIを呼ぶ
        $this->post('/baser/api/admin/bc-blog/blog_posts/batch.json?token=' . $this->accessToken, [
            'batch' => 'delete',
            'batch_targets' => [1, 2]
        ]);
        $this->assertResponseOk();
        // 処理完了メッセージ
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);
        // データが削除されていること
        $data = $blogPostsService->getIndex([])->count();
        $this->assertEquals(0, $data);
        // dblogsが生成されていること
        $dblogsData = $dblogsService->getDblogs(1)->toArray()[0];
        $this->assertEquals('ブログ記事「' . implode('」、「', $names) . '」を 削除 しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('BlogPosts', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        //// 異常系のテスト
        // 無効なキーを指定してAPIを呼ぶ
        $this->post('/baser/api/admin/bc-blog/blog_posts/batch.json?token=' . $this->accessToken, [
            'batch' => 'error',
            'batch_targets' => [1, 2]
        ]);
        $this->assertResponseCode(500);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //データを生成
        SiteConfigFactory::make(['name' => 'content_types', 'value' => ''])->persist();
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1])->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/admin/bc-blog/blog_posts/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/ブログ記事「.+」を削除しました。/', $result->message);
        $this->assertNotNull($result->blogPost->title);

        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/admin/bc-blog/blog_posts/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test view
     */
    public function test_view()
    {
        //データを生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make(['id' => 1, 'title' => 'Nghiem', 'blog_content_id' => 1])->persist();

        //正常の時を確認
        //APIをコル
        $this->get('/baser/api/admin/bc-blog/blog_posts/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('Nghiem', $result->blogPost->title);
        //異常系実行
        $this->get('/baser/api/admin/bc-blog/blog_posts/view/111.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
    }

}
