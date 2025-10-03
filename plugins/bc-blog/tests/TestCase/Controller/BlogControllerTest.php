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

namespace BcBlog\Test\TestCase\Controller;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BcBlog\Controller\BlogController;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Service\Front\BlogFrontServiceInterface;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogCommentFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostBlogTagFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Factory\BlogTagFactory;
use BcBlog\Test\Scenario\MultiSiteBlogPostScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogControllerTest
 *
 * @property  BlogController $BlogController
 */
class BlogControllerTest extends BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
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
    public function testInitialize()
    {
        $this->BlogController = new BlogController($this->getRequest());
        // コンポーネント設定を確認するテスト
        $this->assertNotEmpty($this->BlogController->BcFrontContents);
        // configを確認するテスト
        $this->assertTrue($this->BlogController->BcFrontContents->getConfig('viewContentCrumb'));
    }

    /**
     * test beforeFilter
     */
    public function test_beforeFilter()
    {
        $this->BlogController = new BlogController($this->getRequest());
        $event = new Event('Controller.beforeFilter', $this->BlogController);
        $this->BlogController->beforeFilter($event);
        $config = $this->BlogController->FormProtection->getConfig('validate');
        $this->assertFalse($config);

    }

    /**
     * test index
     */
    public function test_index()
    {
        //準備
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogContentFactory::make(['id' => 1,
            'template' => 'default',
            'description' => 'description test 1'])->persist();
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1', 'title' => 'blog post'])->persist();
        ContentFactory::make(['plugin' => 'BcBlog',
            'entity_id' => 1,
            'status' => true,
            'lft' => 1,
            'rght' => 2,
            'type' => 'BlogContent'])
            ->treeNode(1, 1, null, 'test', '/test/', 1, true)->persist();
        $fullPath = BASER_PLUGINS . 'bc-front/templates/Blog/Blog/default';
        if (!file_exists($fullPath)) {
            mkdir($fullPath, recursive: true);
        }
        $file = new BcFile($fullPath . DS . 'index.php');
        $file->write('html');
        //正常系実行
        $request = $this->getRequest()->withAttribute('currentContent', ContentFactory::get(1));
        $controller = new BlogController($request);
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

        $controller->index($blogFrontService, $blogContentsService, $blogPostsService);
        $vars = $controller->viewBuilder()->getVars();
        unlink($fullPath . DS . 'index.php');
        $this->assertEquals('description test 1', $vars['blogContent']->description);

        //不要フォルダを削除
        (new BcFolder(BASER_PLUGINS . 'bc-front/templates/Blog'))->delete();

        //異常系実行
        $request = $this->getRequest()->withAttribute('currentContent', null);
        $controller = new BlogController($request);
        $this->expectException(RecordNotFoundException::class);
        $controller->index($blogFrontService, $blogContentsService, $blogPostsService);
    }

    /**
     * test archives
     */
    public function test_archives()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogTagFactory::make([[
            'id' => 1,
            'name' => 'tag1',
            'created' => '2022-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
        ContentFactory::make([
            'id' => 1,
            'url' => '/news/',
            'site_id' => 1,
            'status' => true,
            'tag_use' => true,
            'entity_id' => 1,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'lft' => '1',
            'rght' => '2',
            'publish_begin' => '2020-01-27 12:00:00',
            'layout_template' => 'default',
            'publish_end' => '9000-01-27 12:00:00'
        ])->persist();
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'blog_category_id' => 1,
            'no' => 1,
            'user_id' => 1,
            'name' => 'post1',
            'posted' => '2023-01-11 12:57:59',
            'status' => true])->persist();
        BlogContentFactory::make(['id' => 1,
            'tag_use' => true,
            'list_direction' => 'DESC',
            'template' => 'default'
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'lft' => 1,
            'rght' => 2,
        ])->persist();
        BlogPostBlogTagFactory::make(['id' => 1, 'blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        //正常系実行
        //type = 'category'
        $this->get('/news/archives/category/release');
        $this->assertResponseOk();
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('category', $vars['blogArchiveType']);
        $this->assertEquals('release', $vars['blogCategory']->name);
        $this->assertEquals('post1', $vars['posts']->toArray()[0]->name);
        //type = 'author'
        $this->get('/news/archives/author/1');
        $this->assertResponseOk();
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('author', $vars['blogArchiveType']);
        $this->assertEquals('name', $vars['author']->name);
        $this->assertEquals('post1', $vars['posts']->toArray()[0]->name);
        //type = 'tag'
        $this->get('/news/archives/tag/tag1');
        $this->assertResponseOk();
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('tag', $vars['blogArchiveType']);
        $this->assertEquals('tag1', $vars['blogTag']->name);
        $this->assertEquals('post1', $vars['posts']->toArray()[0]->name);
        //type = 'date'
        $this->get('/news/archives/date/2023');
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('yearly', $vars['blogArchiveType']);
        $this->assertEquals('post1', $vars['posts']->toArray()[0]->name);
    }

    /**
     * test posts
     */
    public function test_posts()
    {
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        // スラッグが設定されている記事
        $this->get('/news/archives/release');
        $this->assertResponseOk();
        $this->get('/news/archives/3');
        $this->assertRedirect('/news/archives/release');

        // 日本語のスラッグが設定されている記事
        $this->get('/news/archives/' . rawurlencode('日本語スラッグ'));
        $this->assertResponseOk();
        $this->get('/news/archives/4');
        $this->assertRedirect('/news/archives/' . rawurlencode('日本語スラッグ'));

        // スラッグが設定されていない記事
        $this->get('/news/archives/5');
        $this->assertResponseOk();

        // 404
        $this->get('/news/archives/9999');
        $this->assertResponseCode(404);


    }

    /**
     * test tags
     */
    public function test_tags()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        BlogTagFactory::make([[
            'id' => 1,
            'name' => 'tag1',
            'created' => '2022-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
        BlogTagFactory::make([[
            'id' => 2,
            'name' => 'tag2',
            'created' => '2022-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
        BlogTagFactory::make([[
            'id' => 3,
            'name' => 'tag3',
            'created' => '2022-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
        ContentFactory::make([
            'id' => 1,
            'url' => '/index',
            'site_id' => 1,
            'status' => true,
            'entity_id' => 1,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'lft' => '1',
            'rght' => '2',
            'publish_begin' => '2020-01-27 12:00:00',
            'publish_end' => '9000-01-27 12:00:00'
        ])->persist();
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'no' => 1, 'status' => true])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        ContentFactory::make([
            'id' => 2,
            'url' => '/index',
            'site_id' => 1,
            'status' => true,
            'entity_id' => 2,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'lft' => '3',
            'rght' => '4',
            'publish_begin' => '2020-01-27 12:00:00',
            'publish_end' => '9000-01-27 12:00:00'
        ])->persist();
        BlogPostFactory::make(['id' => 2, 'blog_content_id' => 2, 'no' => 2, 'status' => true])->persist();
        BlogContentFactory::make(['id' => 2])->persist();
        ContentFactory::make([
            'id' => 3,
            'url' => '/index',
            'site_id' => 1,
            'status' => true,
            'entity_id' => 3,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'lft' => '5',
            'rght' => '6',
            'publish_begin' => '2020-01-27 12:00:00',
            'publish_end' => '9000-01-27 12:00:00'
        ])->persist();
        BlogPostFactory::make(['id' => 3, 'blog_content_id' => 3, 'no' => 3, 'status' => true])->persist();
        BlogContentFactory::make(['id' => 3])->persist();
        BlogPostBlogTagFactory::make(['id' => 1, 'blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['id' => 2, 'blog_post_id' => 2, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['id' => 3, 'blog_post_id' => 3, 'blog_tag_id' => 2])->persist();
        //正常系実行
        $this->get('/bc-blog/blog/tags/tag1');
        $this->assertResponseSuccess();
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('tag1', $vars['tag']);
        $this->assertEquals(2, $vars['posts']->toArray()[0]->id);
    }

    /**
     * test ajax_add_comment
     */
    public function test_ajax_add_comment()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(InitAppScenario::class);
        SiteConfigFactory::make(['name' => 'email', 'value' => 'foo@example.com'])->persist();
        SiteConfigFactory::make(['name' => 'formal_name', 'value' => 'test'])->persist();
        BlogContentFactory::make(['id' => 1,
            'template' => 'default',
            'auth_captcha' => false,
            'description' => 'description test 1'])->persist();
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1', 'title' => 'blog post'])->persist();
        ContentFactory::make(['plugin' => 'BcBlog',
            'status' => true,
            'lft' => 1,
            'rght' => 2,
            'type' => 'BlogContent'])
            ->treeNode(1, 1, null, 'test', '/test/', 1, true)->persist();
        //正常系実行
        $this->post('/bc-blog/blog/ajax_add_comment',
            [
                'blog_content_id' => 1,
                'blog_post_id' => 1,
                'name'=>'name test',
                'email'=>'test@gmail.com',
                'auth_captcha' => '1',
                'message'=>'message test'
            ]);
        $this->assertResponseOk();
        $blogComment = BlogCommentFactory::get(1);
        $this->assertEquals('name test', $blogComment->name);
        $this->assertEquals('message test', $blogComment->message);
        //異常系実行
        $this->post('/bc-blog/blog/ajax_add_comment',
            [
                'blog_content_id' => 111,
                'blog_post_id' => 111,
                'name'=>'name test',
                'email'=>'test@gmail.com',
                'auth_captcha' => '1',
                'message'=>'message test'
            ]);
        $this->assertResponseCode(404);

    }

    /**
     * test captcha
     */
    public function test_captcha()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make(['url' => '/news/', 'site_id' => 1, 'entity_id' => 1, 'plugin' => 'BcBlog', 'type' => 'BlogContent',])->persist();

        ob_start();
        $this->get('/news/captcha/abc');
        $this->assertNotNull(ob_get_clean());
    }

}
