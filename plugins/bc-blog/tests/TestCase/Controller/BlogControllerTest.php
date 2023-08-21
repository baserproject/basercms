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
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\BlogController;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Service\Front\BlogFrontServiceInterface;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
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
//        $event = new Event('Controller.beforeFilter', $this->BlogController);
//        $this->BlogController->beforeFilter($event);
    }

    /**
     * test index
     */
    public function test_index()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogContentFactory::make(['id' => 1, 'template' => 'default', 'description' => 'description test 1'])->persist();
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1', 'title' => 'blog post'])->persist();
        ContentFactory::make(['plugin' => 'BcBlog', 'status' => true, 'type' => 'BlogContent'])
            ->treeNode(1, 1, null, 'test', '/test/', 1, true)->persist();

        //正常系実行
        $request = $this->getRequest()->withAttribute('currentContent', ContentFactory::get(1));
        $controller = new BlogController($request);
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

        $controller->index($blogFrontService, $blogContentsService, $blogPostsService);
        $vars = $this->_controller->viewBuilder()->getVars();
        dd($vars);
        $this->assertCount(1, $vars);
        //異常系実行
        $this->post("/baser/admin/bc-blog/blog_comments/index/99");
        //リダイレクトを確認
        $this->assertResponseCode(404);


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
        BlogContentFactory::make(['id' => 1,
            'template' => 'default',
            'list_direction' => 'DESC',
            'description' => 'description test 1'])->persist();
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1', 'title' => 'blog post'])->persist();
        ContentFactory::make(['plugin' => 'BcBlog', 'status' => true, 'type' => 'BlogContent'])
            ->treeNode(1, 1, null, 'test', '/test/', 1, true)->persist();
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
        BlogContentFactory::make([
            'id' => '2',
            'name' => 'release',
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'widget_area' => '2',
            'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
            'use_content' => '1',
            'created' => '2015-08-10 18:57:47',
            'status' => 1,
            'modified' => NULL,
        ])->persist();
        //正常系実行
        $blogFrontService = $this->getService(BlogFrontServiceInterface::class);
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $request = $this->getRequest()->withAttribute('currentContent', ContentFactory::get(1));
        $controller = new BlogController($request);

        $controller->setRequest($request->withParam('pass', ['release']));
        $controller->viewBuilder()->setVar('crumbs', []);
        $controller->archives($blogFrontService, $blogContentsService, $blogPostsService, 'category');
        $vars = $this->_controller->viewBuilder()->getVars();
        dd($vars);


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

        //正常系実行

        //異常系実行


    }

    /**
     * test ajax_add_comment
     */
    public function test_ajax_add_comment()
    {
        //準備

        //正常系実行

        //異常系実行


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
