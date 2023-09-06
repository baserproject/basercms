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
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;
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
use BcBlog\Test\Scenario\BlogContentScenario;
use BcBlog\Test\Scenario\BlogTagsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\Filesystem\File;
use BcBlog\Model\Entity\BlogContent;

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
            'status' => true,
            'lft' => 1,
            'rght' => 2,
            'type' => 'BlogContent'])
            ->treeNode(1, 1, null, 'test', '/test/', 1, true)->persist();
        $fullPath = BASER_PLUGINS . 'bc-front/templates/Blog/Blog/default';
        if (!file_exists($fullPath)){
            mkdir($fullPath, recursive: true);
        }
        $file = new File($fullPath .DS. 'index.php');
        $file->write('html');
        $file->close();
        //正常系実行
        $request = $this->getRequest()->withAttribute('currentContent', ContentFactory::get(1));
        $controller = new BlogController($request);
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

        $controller->index($blogFrontService, $blogContentsService, $blogPostsService);
        $vars = $controller->viewBuilder()->getVars();
        unlink($fullPath.DS.'index.php');
        $this->assertEquals('description test 1', $vars['blogContent']->description);
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

        //正常系実行

        //異常系実行


    }

    /**
     * test posts
     */
    public function test_posts()
    {
        //準備

        //正常系実行

        //異常系実行


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
        //準備

        //正常系実行

        //異常系実行


    }

}
