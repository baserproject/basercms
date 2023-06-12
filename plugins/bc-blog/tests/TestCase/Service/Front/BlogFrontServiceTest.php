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

use BaserCore\Controller\BcFrontAppController;
use BaserCore\Controller\ContentFoldersController;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
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
use BcBlog\Test\Scenario\MultiSiteBlogPostScenario;
use BcBlog\Test\Scenario\MultiSiteBlogScenario;
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
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BcBlog.Factory/BlogContents',
        'plugin.BcBlog.Factory/BlogTags',
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogCategories',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
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
        // サービスクラス
        $blogContentService = $this->getService(BlogContentsServiceInterface::class);
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

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

        //パラメーターの準備
        $request = $this->getRequest();

        //対象メソッドをコル
        $rs = $this->BlogFrontService->getViewVarsForIndex($request, $blogContentService->get(1), $blogPostsService->getIndex([])->all());

        //戻る値を確認
        $this->assertArrayHasKey('blogContent', $rs);
        $this->assertArrayHasKey('posts', $rs);
        $this->assertFalse($rs['single']);
        $this->assertNull($rs['editLink']); //ログインしない場合、Nullを返す
        $this->assertArrayHasKey('currentWidgetAreaId', $rs);

        //ログインした場合
        $this->loginAdmin($request);
        //対象メソッドをコル
        $rs = $this->BlogFrontService->getViewVarsForIndex($request, $blogContentService->get(1), $blogPostsService->getIndex([])->all());
        //編集リンクを返す
        $this->assertEquals($rs['editLink'], [
            'prefix' => 'Admin',
            'plugin' => 'BcBlog',
            'controller' => 'BlogContents',
            'action' => 'edit',
            1
        ]);
    }

    /**
     * test getViewVarsForIndexRss
     */
    public function test_getViewVarsForIndexRss()
    {
        // サービスクラス
        $blogContentService = $this->getService(BlogContentsServiceInterface::class);
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

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

        //パラメーターの準備
        $request = $this->getRequest()->withQueryParams([
            'Site' => SiteFactory::get(1),
            'Content' => ContentFactory::get(1)
        ]);
        $this->loginAdmin($request);

        //対象メソッドをコル
        $rs = $this->BlogFrontService->getViewVarsForIndexRss($request, $blogContentService->get(1), $blogPostsService->getIndex([])->all());

        //戻る値を確認
        $this->assertArrayHasKey('blogContent', $rs);
        $this->assertArrayHasKey('posts', $rs);
        $this->assertArrayHasKey('channel', $rs);
        $this->assertNotNull($rs['channel']['title']);
        $this->assertNotNull($rs['channel']['description']);
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
        ContentFactory::make(['entity_id' => 1, 'plugin' => 'BcBlog', 'type' => 'BlogContent'])->persist();
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
        ContentFactory::make(['entity_id' => 1, 'plugin' => 'BcBlog', 'type' => 'BlogContent'])->persist();
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
        $this->getRequest('/baser/admin');
        // サービスクラス
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $blogContentsService = $this->getService(BlogContentsServiceInterface::class);

        // データ生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/');
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1'])->persist();

        //// 正常系のテスト
        // サービスメソッドを呼ぶ
        $result = $this->BlogFrontService->getViewVarsForArchivesByAuthor(
            $blogPostsService->getIndex([])->all(),
            'name',
            $blogContentsService->get(1)
        );

        // view 用変数が設定されているか確認
        $this->assertArrayHasKey('posts', $result);
        $this->assertArrayHasKey('blogArchiveType', $result);
        $this->assertArrayHasKey('author', $result);
        $this->assertArrayHasKey('currentWidgetAreaId', $result);
        // posts の確認
        $this->assertEquals($result['posts']->count(), 1);
        // blogArchiveTypeの確認
        $this->assertEquals($result['blogArchiveType'], 'author');
        // author の確認
        $this->assertEquals($result['author']->id, 1);
        $this->assertEquals($result['author']->name, 'name');

        //// 異常系のテスト
        // Author が存在しない場合は例外とする
        $this->expectException("Cake\Http\Exception\NotFoundException");
        $this->BlogFrontService->getViewVarsForArchivesByAuthor(
            $blogPostsService->getIndex([])->all(),
            'author name test',
            $blogContentsService->get(1)
        );
    }

    /**
     * test getCategoryCrumbs
     */
    public function test_getCategoryCrumbs()
    {
        //データ生成
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/');
        BlogPostFactory::make([])->publish(1, 1)->persist();
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => 'title add parent',
            'name' => 'name-add-parent',
            'rght' => 1,
            'lft' => 4,
            'status' => true
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 2,
            'blog_content_id' => 1,
            'title' => 'title add child',
            'name' => 'name-add-child',
            'rght' => 2,
            'lft' => 3,
            'status' => true
        ])->persist();

        //$isCategoryPage = true & $count > 1
        $rs = $this->BlogFrontService->getCategoryCrumbs(
            "https://basercms.net/",
            1
        );
        //戻る値を確認
        $this->assertEquals('title add child', $rs[0]['name']);
        $this->assertEquals('https://basercms.net/archives/category/name-add-child', $rs[0]['url']);

        //$isCategoryPage = true & $count = 1
        $rs = $this->BlogFrontService->getCategoryCrumbs(
            "test",
            2
        );
        //戻る値を確認
        $this->assertEquals([], $rs);

        //$isCategoryPage = false & $count = 1
        $rs = $this->BlogFrontService->getCategoryCrumbs(
            "https://basercms.net/",
            2,
            false
        );
        //戻る値を確認
        $this->assertEquals('title add child', $rs[0]['name']);
        $this->assertEquals('https://basercms.net/archives/category/name-add-child', $rs[0]['url']);
    }

    /**
     * test setupPreviewForArchives
     */
    public function test_setupPreviewForArchives()
    {
        // データを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'title' => 'blog post title',
            'status' => true,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => BlogPostFactory::get(1)->get('blog_category_id'),
            'blog_content_id' => 1,
            'title' => 'blog post category title',
            'name' => 'name-post',
            'rght' => 1,
            'lft' => 2,
            'status' => true,
        ])->persist();
        BlogContentFactory::make([
            'id' => 1,
            'description' => 'test プレビュー用のセットアップ',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'auth_captcha' => '1',
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
            'title' => 'content title',
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
            'status' => true,
        ])->persist();
        $postBlogContent = [
            'detail_draft' => 'preview detail_draft',
            'description' => 'test preview description',
            'template' => 'default-2',
            'title' => 'preview title',
            'url' => '/preview',
            'content' => '<p>some string <p/>',
        ];
        $controller = new BcFrontAppController(
            $this->getRequest('/test')
                ->withParam('entityId', 1)
                ->withParam('pass', [1])
                ->withQueryParams(['preview' => 'draft'])
                ->withParsedBody($postBlogContent)
        );
        $controller->viewBuilder()->setVar('crumbs', []);

        // サービスクラスを呼ぶ
        $this->BlogFrontService->setupPreviewForArchives($controller);
        // テンプレートを確認
        $this->assertEquals('Blog/default/single', $controller->viewBuilder()->getTemplate());
        // view変数が設定されているか確認
        $vars = $controller->viewBuilder()->getVars();
        $this->assertArrayHasKey('post', $vars);
        $this->assertArrayHasKey('blogContent', $vars);
        $this->assertArrayHasKey('editLink', $vars);
        $this->assertArrayHasKey('commentUse', $vars);
        $this->assertArrayHasKey('single', $vars);
        $this->assertArrayHasKey('crumbs', $vars);
        // ブログ記事がpostデータにより書き換えられているか確認
        $this->assertEquals('preview title', $vars['post']->title);
        $this->assertEquals('default-2', $vars['post']->template);
        $this->assertEquals('test preview description', $vars['post']->description);
        $this->assertEquals('preview detail_draft', $vars['post']->detail);

        $this->assertEquals('test プレビュー用のセットアップ', $vars['blogContent']->description);
        $this->assertEquals('default', $vars['blogContent']->template);

        $this->assertEquals('/test', $vars['blogContent']->content->url);
        $this->assertEquals('content title', $vars['blogContent']->content->title);
    }

    /**
     * test getViewVarsForBlogAuthorArchivesWidget
     */
    public function test_getViewVarsForBlogAuthorArchivesWidget()
    {
        // データを生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);
        UserFactory::make([
            'id' => BlogPostFactory::get(1)->user_id,
            'name' => 'name_test',
            'real_name_1' => 'real_name_1_test',
            'real_name_2' => 'real_name_2_test',
            'nickname' => 'nickname_test',
        ])->persist();

        // viewCountはTrue場合、
        $rs = $this->BlogFrontService->getViewVarsForBlogAuthorArchivesWidget(6, true);

        //戻る値を確認
        $this->assertEquals(6, $rs['blogContent']->id);
        $this->assertEquals('name_test', $rs['authors'][0]->name);
        $this->assertEquals(1, $rs['authors'][0]->count);

        //viewCountはFalse場合
        $rs = $this->BlogFrontService->getViewVarsForBlogAuthorArchivesWidget(6, false);
        $this->assertNull($rs['authors'][0]->count);
    }

    /**
     * test getViewVarsForBlogCalendarWidget
     * @dataProvider getViewVarsForBlogCalendarWidgetDataProvider
     */
    public function test_getViewVarsForBlogCalendarWidget($blogContentId, $year, $month, $nextExpected, $prevExpected)
    {
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);
        $rs = $this->BlogFrontService->getViewVarsForBlogCalendarWidget($blogContentId, $year, $month);
        $this->assertArrayHasKey('blogContent', $rs);
        $this->assertArrayHasKey('entryDates', $rs);
        $this->assertEquals($nextExpected, $rs['next']);
        $this->assertEquals($prevExpected, $rs['prev']);
    }

    private function getViewVarsForBlogCalendarWidgetDataProvider()
    {
        return [
            [6, 2014, 12, true, false],
            [7, 2016, 3, false, true],
            [6, 2015, 1, false, false],
        ];
    }

    /**
     * test getViewVarsForBlogCategoryArchivesWidget
     */
    public function test_getViewVarsForBlogCategoryArchivesWidget()
    {
        //データを生成
        $this->loadFixtureScenario(MultiSiteBlogScenario::class);
        //対象メソッドをコール
        $rs = $this->BlogFrontService->getViewVarsForBlogCategoryArchivesWidget(6);
        //戻る値を確認
        $this->assertEquals(6, $rs['blogContent']->id);
        $this->assertEquals(2, $rs['categories']->count());
    }

    /**
     * test getViewVarsForBlogYearlyArchivesWidget
     */
    public function test_getViewVarsForBlogYearlyArchivesWidget()
    {
        //データを生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        // サービスクラスを呼ぶ
        $rs = $this->BlogFrontService->getViewVarsForBlogYearlyArchivesWidget(6);

        //戻る値を確認
        $this->assertArrayHasKey('blogContent', $rs);
        $this->assertArrayHasKey('2015', $rs['postedDates']);
    }

    /**
     * test getViewVarsBlogMonthlyArchivesWidget
     */
    public function test_getViewVarsBlogMonthlyArchivesWidget()
    {
        //データを生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        // サービスクラスを呼ぶ
        $rs = $this->BlogFrontService->getViewVarsBlogMonthlyArchivesWidget(6);

        //戻る値を確認
        $this->assertArrayHasKey('blogContent', $rs);
        $this->assertArrayHasKey('201501', $rs['postedDates']);
    }

    /**
     * test getViewVarsRecentEntriesWidget
     */
    public function test_getViewVarsRecentEntriesWidget()
    {
        //データを生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        // サービスクラスを呼ぶ
        $rs = $this->BlogFrontService->getViewVarsRecentEntriesWidget(6);

        //戻る値を確認
        $this->assertArrayHasKey('blogContent', $rs);
        $this->assertEquals(1, $rs['recentEntries']->count());
    }
}
