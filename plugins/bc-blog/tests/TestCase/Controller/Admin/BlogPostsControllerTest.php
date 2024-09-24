<?php

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Controller\Admin;

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Admin\BlogPostsController;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogPostsControllerTest
 *
 * @property BlogPostsController $BlogPostsController
 */
class BlogPostsControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;
    use IntegrationTestTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BlogPostsController = new BlogPostsController($this->loginAdmin($this->getRequest()));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->BlogPostsController->BcAdminContents);
    }

    /**
     * test beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/');
        SiteConfigFactory::make(['name' => 'editor', 'value' => 'BaserCore.BcCkeditor'])->persist();

        $request = $this->getRequest('/baser/admin/bc-blog/blog_posts/index/1');
        $request = $this->loginAdmin($request);
        $blogPosts = new BlogPostsController($request);

        $event = new Event('filter');
        $blogPosts->beforeFilter($event);

        //$blogContentIdを指定しない場合。
        $this->expectExceptionMessage('不正なURLです。');
        $this->expectException('BaserCore\Error\BcException');
        $event = new Event('Controller.beforeFilter', $this->BlogPostsController);
        $this->BlogPostsController->beforeFilter($event);
    }
    /**
     * test beforeFilter
     */
    public function testBeforeFilter_content_is_null()
    {
        //コンテンツデータが存在しない場合。
        $this->expectExceptionMessage('コンテンツデータが見つかりません。');
        $this->expectException('BaserCore\Error\BcException');
        $request = $this->getRequest('/baser/admin/bc-blog/blog_posts/index/1111');
        $request = $this->loginAdmin($request);
        $blogPosts = new BlogPostsController($request);

        $event = new Event('filter');
        $blogPosts->beforeFilter($event);
    }

    /**
     * test index
     * [ADMIN] ブログ記事一覧表示
     */
    public function testIndex()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを作成
        $this->loadFixtureScenario(BlogContentScenario::class, 2, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '2', 'title' => 'blog post'])->persist();
        BlogPostFactory::make(['id' => '2', 'blog_content_id' => '2', 'title' => 'blog post'])->persist();

        //正常の場合を確認
        $this->post('/baser/admin/bc-blog/blog_posts/index/2?num=1&page=2');
        // ステータスを確認
        $this->assertResponseCode(200);

        //戻り値を確認
        $vars = $this->_controller->viewBuilder()->getVars();
        //blogContentが存在するのを確認
        $this->assertArrayHasKey('blogContent', $vars);
        //publishLinkを確認
        $this->assertEquals('https://localhost/test', $vars['publishLink']);
        //postsが存在するのを確認
        $this->assertEquals(1, count($vars['posts']));
        //パラメータクエリを確認
        $this->assertEquals(1, $this->_controller->getRequest()->getQuery('num'));

        //異常の場合を確認
        $this->post('/baser/admin/bc-blog/blog_posts/index/2?num=1&page=3');
        // ステータスを確認
        $this->assertResponseCode(302);
        // リダイレクトを確認
        $this->assertRedirect(['action' => 'index', 2]);
    }

    /**
     * test add
     * [ADMIN] ブログ記事追加処理
     */
    public function testAdd()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/');
        $this->post('/baser/admin/bc-blog/blog_posts/add/1', ['blog_content_id' => 2, 'title' => 'test', 'user_id' => 1, 'posted' => '2022-12-01 00:00:00']);
        $this->assertResponseCode(302);
        $this->assertFlashMessage('記事「test」を追加しました。');
        $this->assertRedirect(['action' => 'edit/1/1']);

        //失敗テスト
        $this->post('/baser/admin/bc-blog/blog_posts/add/1', ['blog_content_id' => 2, 'title' => '']);
        //レスポンスを確認
        $this->assertResponseCode(200);
        $errors = $this->_controller->viewBuilder()->getVars()['post']->getErrors();
        //エラー内容を確認
        $this->assertEquals(['_empty' => 'タイトルを入力してください。'], $errors['title']);
    }

    /**
     * test edit
     * [ADMIN] ブログ記事編集処理
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを作成
        SiteConfigFactory::make([
            'name' => 'content_types',
            'value' => ''
        ])->persist();
        SiteConfigFactory::make([
            'name' => 'editor',
            'value' => 'BaserCore.BcCkeditor'
        ])->persist();
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make([
            'id' => '1',
            'blog_content_id' => '1'
        ])->persist();

        $data = ['title' => 'blog post edit', 'name' => 'ホームページをオープンしました'];
        //正常の場合を確認
        $this->post('/baser/admin/bc-blog/blog_posts/edit/1/1', $data);
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertFlashMessage('記事「blog post edit」を更新しました。');
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BcBlog',
            'prefix' => 'Admin',
            'controller' => 'blog_posts',
            'action' => 'edit/1/1'
        ]);
        // データの変更を確認
        $blogPost = BlogPostFactory::get(1);
        $this->assertEquals('blog post edit', $blogPost['title']);
        $this->assertEquals('ホームページをオープンしました', $blogPost['name']);

        //エラーを発生した場合を確認
        $this->post('/baser/admin/bc-blog/blog_posts/edit/1/1', ['name' => str_repeat('a', 256)]);
        // ステータスを確認
        $this->assertResponseCode(200);
        // メッセージを確認
        $this->assertResponseContains('入力エラーです。内容を修正してください。');
    }

    /**
     * test delete
     * [ADMIN] ブログ記事削除処理
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // データ生成
        SiteConfigFactory::make(['name' => 'content_types', 'value' => ''])->persist();
        $this->loadFixtureScenario(BlogContentScenario::class, 2, 2, null, 'news', '/news');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 2])->persist();
        // 記事削除コール
        $this->delete('/baser/admin/bc-blog/blog_posts/delete/2/1');
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertMatchesRegularExpression('/ブログ記事「.+」を削除しました。/', $_SESSION["Flash"]["flash"][0]["message"]);
        // リダイレクトを確認
        $this->assertRedirect(['action' => 'index', 2]);
        // データ削除確認
        $result = BlogPostFactory::find()->where(['id' => 1])->count();
        $this->assertEquals(0, $result);

        // error
        $this->delete('/baser/admin/bc-blog/blog_posts/delete/2/1');
        // ステータスを確認
        $this->assertResponseCode(404);
    }

    /**
     * test unpublish
     * [ADMIN] ブログ記事を非公開状態にする
     */
    public function testUnpublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        // データ生成
        SiteConfigFactory::make(['name' => 'content_types', 'value' => ''])->persist();
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make([
            'id' => '1',
            'blog_content_id' => '1',
            'no' => '1',
            'name' => 'ホームページをオープンしました',
            'title' => 'test',
            'content' => 'content test',
            'detail' => 'detail test',
            'blog_category_id' => '1',
            'user_id' => '1',
            'status' => '1',
            'posts_date' => '2015-01-27 12:57:59',
            'content_draft' => '',
            'detail_draft' => '',
            'publish_begin' => null,
            'publish_end' => null,
            'exclude_search' => 0,
            'eye_catch' => 'template1.jpg',
            'created' => '2015-01-27 12:56:53',
            'modified' => '2015-01-27 12:57:59'
        ])->persist();

        $this->post('/baser/admin/bc-blog/blog_posts/unpublish/1/1');
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertFlashMessage('ブログ記事「test」を非公開状態にしました。');
        // リダイレクトを確認
        $this->assertRedirect(['action' => 'index', 1]);
        // 非公開状態を確認
        $blogPost = BlogPostFactory::get(1);
        $this->assertFalse($blogPost->status);
        $this->assertNull($blogPost->publish_begin);
        $this->assertNull($blogPost->publish_end);
    }

    /**
     * test publish
     * [ADMIN] ブログ記事を公開状態にする
     */
    public function testPublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // データ生成
        SiteConfigFactory::make([
            'name' => 'content_types',
            'value' => ''
        ])->persist();
        $this->loadFixtureScenario(BlogContentScenario::class, 3, 1, null, 'news', '/news');
        //非公開を設定
        BlogPostFactory::make([])->unpublish(1,3)->persist();

        // 公開設定コール
        $this->patch('/baser/admin/bc-blog/blog_posts/publish/3/1');
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertMatchesRegularExpression('/ブログ記事「.+」を公開状態にしました。/', $_SESSION["Flash"]["flash"][0]["message"]);
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BcBlog',
            'prefix' => 'Admin',
            'controller' => 'blog_posts',
            'action' => 'index/3'
        ]);
        // データの変更を確認
        $blogPost = BlogPostFactory::get(1);
        $this->assertEquals(true, $blogPost['status']);
        $this->assertEquals(null, $blogPost['publish_begin']);
        $this->assertEquals(null, $blogPost['publish_end']);

        // テスト失敗確認
        // 公開設定コール
        $this->patch('/baser/admin/bc-blog/blog_posts/publish/3/99');
        // ステータスを確認
        $this->assertResponseCode(404);
    }

    /**
     * test copy
     * [ADMIN] コピー
     */
    public function testCopy()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを作成
        SiteConfigFactory::make(['name' => 'content_types', 'value' => ''])->persist();
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(
            [
                'id' => '1',
                'blog_content_id' => '1',
                'title' => 'test',
                'content' => 'content test'
            ]
        )->persist();
        BlogPostFactory::make(['id' => 2, 'blog_content_id' => null])->persist();

        //実行成功のテスト
        $this->post('/baser/admin/bc-blog/blog_posts/copy/1/1');
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertMatchesRegularExpression('/ブログ記事「.+」をコピーしました。/', $_SESSION["Flash"]["flash"][0]["message"]);

        // リダイレクトを確認
        $this->assertRedirect(['action' => 'index', 1]);
        // データのコピーを確認
        $BlogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $copyBlogPost = $BlogPostsService->getIndex(['title' => 'test_copy'])->first();
        $this->assertEquals($copyBlogPost->content, 'content test');

        //実行失敗のテスト　BlogPostコンテンツ準備足りないのを利用
        $this->post('/baser/admin/bc-blog/blog_posts/copy/1/2');
        $this->assertResponseCode(404);
    }

}
