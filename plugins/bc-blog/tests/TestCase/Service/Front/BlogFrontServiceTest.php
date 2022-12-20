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

namespace BcBlog\Test\TestCase\Service\Front;

use BaserCore\Controller\ContentFoldersController;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcBlog\Model\Entity\BlogContent;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Service\Front\BlogFrontService;
use BcBlog\Service\Front\BlogFrontServiceInterface;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Factory\BlogTagFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcBlog\Test\Scenario\MultiSiteBlogScenario;
use Cake\Datasource\EntityInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BlogFrontServiceTest
 * @property BlogFrontService $BlogFrontService
 */
class BlogFrontServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use ScenarioAwareTrait;

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
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcBlog.Factory/BlogContents',
        'plugin.BcBlog.Factory/BlogTags',
        'plugin.BcBlog.Factory/BlogPosts',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BlogFrontService = $this->getService(BlogFrontServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogFrontService);
        parent::tearDown();
    }


    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->BlogFrontService->BlogContentsService));
        $this->assertTrue(isset($this->BlogFrontService->BlogPostsService));
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test setupPreviewForIndex
     */
    public function test_setupPreviewForIndex()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogContentFactory::make([
            'id' => 1,
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'widget_area' => '2',
            'eye_catch_size' => BcUtil::serialize([
                'thumb_width' => 600,
                'thumb_height' => 600,
                'mobile_thumb_width' => 150,
                'mobile_thumb_height' => 150,
            ]),
            'use_content' => '1'
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'title' => 'news',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 1,
            'url' => '/test',
            'site_id' => 1,
            'alias_id' => null,
            'main_site_content_id' => null,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 2,
            'level' => 1,
            'status' => true

        ])->persist();
        $blogContent = [
            'description' => 'test',
            'template' => 'default-2',
            'content' => [
                'title' => 'preview title',
                'url' => '/preview',
            ]
        ];
        $controller = new ContentFoldersController(
            $this->getRequest('/test')
                ->withParam('entityId', 1)
                ->withParsedBody($blogContent)
        );

        $this->BlogFrontService->setupPreviewForIndex($controller);
        $this->assertEquals('Blog/default-2/index', $controller->viewBuilder()->getTemplate());

        $vars = $controller->viewBuilder()->getVars();
        $this->assertArrayHasKey('blogContent', $vars);
        $this->assertArrayHasKey('posts', $vars);
        $this->assertArrayHasKey('single', $vars);
        $this->assertArrayHasKey('editLink', $vars);
        $this->assertArrayHasKey('currentWidgetAreaId', $vars);
        $this->assertEquals(2, $vars['currentWidgetAreaId']);
        $this->assertEquals('test', $vars['blogContent']->description);
        $this->assertEquals('default-2', $vars['blogContent']->template);
        $this->assertEquals('/preview', $vars['blogContent']->content->url);
        $this->assertEquals('preview title', $vars['blogContent']->content->title);
    }

    /**
     * test getIndexTemplate
     */
    public function test_getIndexTemplate()
    {
        BlogContentFactory::make([
            'id' => 1,
            'template' => 'template-1'
        ])->persist();
        $BlogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $rs = $this->BlogFrontService->getIndexTemplate($BlogContentsService->get(1));
        $this->assertEquals($rs, 'Blog/template-1/index');
    }

    /**
     * test getSingleTemplate
     */
    public function test_getSingleTemplate()
    {
        // データ生成
        BlogContentFactory::make(['id' => 1, 'template' => 'template-1'])->persist();
        // ブログコンテンツの設定に依存するメソードをコール
        $rs = $this->BlogFrontService->getSingleTemplate(BlogContentFactory::get(1));
        //戻り値を確認
        $this->assertEquals($rs, 'Blog/template-1/single');
    }

    /**
     * test getArchivesTemplate
     */
    public function test_getArchivesTemplate()
    {
        // サービスクラス
        $BlogContentsService = $this->getService(BlogContentsServiceInterface::class);
        // データ生成
        BlogContentFactory::make(['id' => 1, 'template' => 'template-1'])->persist();
        // ブログコンテンツの設定に依存するメソードをコール
        $rs = $this->BlogFrontService->getArchivesTemplate($BlogContentsService->get(1));
        //戻り値を確認
        $this->assertEquals($rs, 'Blog/template-1/archives');
    }

    /**
     * test getViewVarsForSingle
     */
    public function test_getViewVarsForSingle()
    {
        // サービスクラス
        $BlogContentsService = $this->getService(BlogContentsServiceInterface::class);
        // データ生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/');
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'title' => 'blog post title',
            'status' => true
        ])->persist();
        BlogCategoryFactory::make([
            'id' => BlogPostFactory::get(1)->get('blog_category_id'),
            'blog_content_id' => 1,
            'title' => 'title add',
            'name' => 'name-add',
            'rght' => 1,
            'lft' => 2,
            'status' => true
        ])->persist();
        //リクエストバリューを設定
        $request = $this->loginAdmin($this->getRequest(), 1);
        // メソードをコール
        $rs = $this->BlogFrontService->getViewVarsForSingle(
            $request->withParam('pass', [1]),
            $BlogContentsService->get(1),
            ['blog', 'test']
        );

        //戻り値を確認

        //postの値を確認
        $this->assertEquals($rs['post']['title'], 'blog post title');
        //blogContentの値を確認
        $this->assertEquals($rs['blogContent']->content->name, 'test');
        //editLinkの値を確認
        $editLinkExpected = [
            'prefix' => 'Admin',
            'plugin' => 'BcBlog',
            'controller' => 'BlogPosts',
            'action' => 'edit',
            1,
            1
        ];
        $this->assertEquals($rs['editLink'], $editLinkExpected);
        //commentUseの値を確認
        $this->assertTrue($rs['commentUse']);
        //singleの値を確認
        $this->assertTrue($rs['single']);
        //crumbsの値を確認
        $crumbsExpected = [
            'blog',
            'test',
            [
                'name' => 'title add',
                'url' => '/archives/category/name-add'
            ]
        ];
        $this->assertEquals($rs['crumbs'], $crumbsExpected);


        //$noが存在しない場合、
        $this->expectException('Cake\Http\Exception\NotFoundException');
        $this->BlogFrontService->getViewVarsForSingle(
            $this->getRequest(),
            $BlogContentsService->get(1),
            ['blog', 'test']
        );
    }

    /**
     * test getViewVarsForArchivesByTag
     */
    public function test_getViewVarsForArchivesByTag()
    {
        //サービスをコル
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        // データ生成
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/');
        BlogContentFactory::make([
            'id' => 2,
            'template' => 'template-2',
            'list_direction' => 'DESC',
            'tag_use' => true,
        ])->persist();
        BlogTagFactory::make([
            'id' => 1,
            'name' => 'Archives By Tag',
        ])->persist();

        // tag string
        $tag = 'Archives By Tag';

        // BlogContent
        /** @var BlogContent $blogContent */
        $blogContent = BlogContentFactory::get(2);

        //サービスメソッドコール
        $result = $this->BlogFrontService->getViewVarsForArchivesByTag($blogPostsService->getIndex([])->all(), $tag, $blogContent);
        //戻る値を確認
        $this->assertEquals(true, isset($result['posts']));
        $this->assertEquals('tag', $result['blogArchiveType']);
        $this->assertEquals(true, isset($result['blogTag']));

        //error　存在しないタグを設定する場合、
        $tag = 'error Archives By Tag'; // tag string
        $this->expectException('Cake\Http\Exception\NotFoundException');
        $this->BlogFrontService->getViewVarsForArchivesByTag($blogPostsService->getIndex([])->all(), $tag, $blogContent);
    }

    /**
     * test getViewVarsForArchivesByDate
     */
    public function test_getViewVarsForArchivesByDate()
    {
        // サービスクラス
        $BlogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

        // データ生成
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/');

        // BlogPost取得
        $posts = $blogPostsService->getIndex([])->all();

        $blogContent = $BlogContentsService->get(1);

        // 日別
        $result = $this->BlogFrontService->getViewVarsForArchivesByDate($posts, '2022', '1', '1', $blogContent);
        $this->assertEquals(true, isset($result['posts']));
        $this->assertEquals('daily', $result['blogArchiveType']);
        $this->assertEquals(true, isset($result['year']));
        $this->assertEquals(true, isset($result['month']));
        $this->assertEquals(1, $result['day']);

        // 月別
        $result = $this->BlogFrontService->getViewVarsForArchivesByDate($posts, '2022', '1', '', $blogContent);
        $this->assertEquals(true, isset($result['posts']));
        $this->assertEquals('monthly', $result['blogArchiveType']);
        $this->assertEquals(true, isset($result['year']));
        $this->assertEquals(1, $result['month']);
        $this->assertEquals(true, isset($result['day']));

        // 年別
        $result = $this->BlogFrontService->getViewVarsForArchivesByDate($posts, '2022', '', '', $blogContent);
        $this->assertEquals(true, isset($result['posts']));
        $this->assertEquals('yearly', $result['blogArchiveType']);
        $this->assertEquals(2022, $result['year']);
        $this->assertEquals(true, isset($result['month']));
        $this->assertEquals(true, isset($result['day']));

        //日付が存在しない場合、
        $this->expectException('Cake\Http\Exception\NotFoundException');
        $this->BlogFrontService->getViewVarsForArchivesByDate($posts, '', '', '1', $blogContent);
    }

    /**
     * test getViewVarsForArchivesByCategory
     */
    public function test_getViewVarsForArchivesByCategory()
    {
        // サービスクラス
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $BlogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $ContentsService = $this->getService(ContentsServiceInterface::class);

        // データ生成
        $this->loadFixtureScenario(MultiSiteBlogScenario::class);
        BlogPostFactory::make([
            'id' => '1',
            'blog_content_id' => '1'
        ])->persist();

        //正常の場合を確認
        $rs = $this->BlogFrontService->getViewVarsForArchivesByCategory(
            $blogPostsService->getIndex([])->all(),
            'release',
            $this->getRequest()->withParam('currentContent', $ContentsService->get(4)),
            $BlogContentsService->get(6),
            ['blog', 'test']
        );

        //戻り値を確認

        //postsが取得できるかどうかのを確認
        $this->assertEquals($rs['posts']->count(), 1);
        //blogArchiveTypeの戻り値を確認
        $this->assertEquals($rs['blogArchiveType'], 'category');
        //blogCategoryの戻り値を確認
        $this->assertEquals($rs['blogCategory']->name, 'release');
        $this->assertEquals($rs['blogCategory']->title, 'プレスリリース');
        //crumbsの戻り値を確認
        $this->assertEquals($rs['crumbs'], ['blog', 'test']);

        //異常の場合を確認
        $this->expectException("Cake\Http\Exception\NotFoundException");
        $this->BlogFrontService->getViewVarsForArchivesByCategory(
            $blogPostsService->getIndex([])->all(),
            'release-test',
            $this->getRequest()->withParam('currentContent', $ContentsService->get(4)),
            $BlogContentsService->get(6),
            ['blog', 'test']
        );
    }

    /**
     * test getViewVarsForArchivesByAuthor
     */
    public function test_getViewVarsForArchivesByAuthor()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getCategoryCrumbs
     */
    public function test_getCategoryCrumbs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test setupPreviewForArchives
     */
    public function test_setupPreviewForArchives()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsForBlogAuthorArchivesWidget
     */
    public function test_getViewVarsForBlogAuthorArchivesWidget()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsForBlogCalendarWidget
     */
    public function test_getViewVarsForBlogCalendarWidget()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsForBlogCategoryArchivesWidget
     */
    public function test_getViewVarsForBlogCategoryArchivesWidget()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsForBlogYearlyArchivesWidget
     */
    public function test_getViewVarsForBlogYearlyArchivesWidget()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsBlogMonthlyArchivesWidget
     */
    public function test_getViewVarsBlogMonthlyArchivesWidget()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsRecentEntriesWidget
     */
    public function test_getViewVarsRecentEntriesWidget()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
