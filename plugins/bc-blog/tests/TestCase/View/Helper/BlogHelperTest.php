<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\View\Helper;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\RootContentScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Model\Entity\BlogTag;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Service\BlogTagsServiceInterface;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostBlogTagFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Factory\BlogTagFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcBlog\Test\Scenario\BlogTagsScenario;
use BcBlog\Test\Scenario\MultiSiteBlogPostScenario;
use BcBlog\Test\Scenario\MultiSiteBlogScenario;
use BcBlog\View\BlogFrontAppView;
use BcBlog\View\Helper\BlogHelper;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Throwable;

/**
 * Blog helper library.
 *
 * @property BlogHelper $Blog
 */
class BlogHelperTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(BlogContentScenario::class,
            1,  // id
            1, // siteId
            1, // parentId
            'news', // name
            '/news/', // url
            'test title'
        );
        $view = new BlogFrontAppView($this->getRequest());
        $blogContent = BlogContentFactory::get(1);
        $blogContent->content = ContentFactory::get(1);
        $view->set('blogContent', $blogContent);
        $this->Blog = new BlogHelper($view);
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
     * Test __construct
     */
    public function test__construct()
    {
        $this->assertEquals(1, $this->Blog->currentContent->id);
    }
    /**
     * test setContent
     * @throws Throwable
     */
    public function test_setContent()
    {
        $this->Blog->setContent(22);
        $this->assertNull($this->Blog->currentBlogContent);
        $this->Blog->setContent(1);
        $this->assertEquals(1, $this->Blog->currentBlogContent->id);
    }

    /**
     * ブログIDを取得する
     */
    public function testGetCurrentBlogId()
    {
        //準備
        $this->Blog->setContent(1);
        //正常系実行
        $result = $this->Blog->getCurrentBlogId();
        $this->assertEquals(1, $result);
    }

    /**
     * ブログのコンテンツ名を取得する
     * @throws \Throwable
     */
    public function testGetBlogName()
    {
        //準備
        $this->Blog->setContent(1);
        //正常系実行
        $result = $this->Blog->getBlogName();
        $this->assertEquals('news', $result);
    }

    /**
     * タイトルを取得する
     */
    public function testGetTitle()
    {
        //準備
        $this->Blog->setContent(1);
        //正常系実行
        $result = $this->Blog->getTitle();
        $this->assertEquals('test title', $result);
    }

    /**
     * ブログの説明文を取得する
     */
    public function testGetDescription()
    {
        //準備
        $this->Blog->setContent(1);
        //正常系実行
        $result = $this->Blog->getDescription();
        $this->assertEquals('ディスクリプション', $result);
    }

    /**
     * test descriptionExists
     */
    public function test_descriptionExists()
    {
        //準備
        $this->Blog->setContent(1);
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent'])
            ->treeNode(2, 1, 1, 'name', '/test', 2, true)->persist();
        BlogContentFactory::make(['id' => 2, 'template' => 'homePage2'])->persist();
        //正常系実行: true
        $result = $this->Blog->descriptionExists();
        $this->assertTrue($result);
        //正常系実行: false
        $this->Blog->setContent(3);
        $result = $this->Blog->descriptionExists();
        $this->assertFalse($result);
    }


    /**
     * 記事タイトルを取得する
     */
    public function testGetPostTitle()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $site = SiteFactory::get(1);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', $site));
        $post = new BlogPost([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'posted' => '2023-01-27 12:57:59',
        ]);
        $result = $this->Blog->getPostTitle($post, false, ['escape' => false]);
        $this->assertEquals('プレスリリース', $result);
        $result = $this->Blog->getPostTitle($post, true);
        $this->assertEquals('<a href="/news/archives/release">プレスリリース</a>', $result);

    }

    public static function getPostTitleDataProvider()
    {
        return [
            ['test-name', true, [], '<a href="/news/archives/4">test-name</a>'],
            ['test-name', false, [], 'test-name'],
            ['<script></script>', false, [], '&lt;script&gt;&lt;/script&gt;'],
            ['<script></script>', true, [], '<a href="/news/archives/4">&lt;script&gt;&lt;/script&gt;</a>'],
            ['test-name<br>2行目', false, ['escape' => false], 'test-name<br>2行目'],
            ['test-name<br>2行目', true, ['escape' => false], '<a href="/news/archives/4">test-name<br>2行目</a>'],
        ];
    }

    /**
     * 記事へのリンクを取得する
     */
    public function testGetPostLink()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $site = SiteFactory::get(1);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', $site));
        $post = new BlogPost([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'posted' => '2023-01-27 12:57:59',
        ]);
        $result = $this->Blog->getPostLink($post, 'test-title');
        $this->assertEquals('<a href="/news/archives/release">test-title</a>', $result);
    }

    /**
     * ブログ記事のURLを取得する
     *
     * @param int $blogContentId ブログコンテンツID
     * @param string $baseUrl ベースURL
     * @param bool $useBase ベースとなるURLを付与するかどうか
     * @param string $expects 期待値
     * @dataProvider getPostLinkUrlDataProvider
     */
    public function testGetPostLinkUrl($blogContentId, $baseUrl, $useBase, $expects)
    {
        $this->truncateTable('contents');
        $this->truncateTable('blog_contents');
        $this->truncateTable('blog_posts');

        // データ生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        // ブログ記事を取得
        $post = BlogPostFactory::find()->where(
            ['blog_content_id' => $blogContentId]
        )->first();

        // 現在のサイトを指定
        $site = SiteFactory::get(1);
        $request = $this->getRequest(
            '/', [], 'GET', $baseUrl ? ['base' => $baseUrl] : []
        )->withAttribute('currentSite', $site);
        $this->Blog->getView()->setRequest($request);
        $siteUrl = Configure::read('BcEnv.siteUrl');
        Configure::write('BcEnv.siteUrl', 'https://main.com');

        $full = true;
        if($useBase) $full = false;

        // テスト対象メソッド
        /** @var BlogPost $post */
        $result = $this->Blog->getPostLinkUrl($post, $useBase, $full);
        $this->assertEquals($expects, $result, '記事へのリンクを正しく取得できません');
        Configure::write('BcEnv.siteUrl', $siteUrl);
    }

    public static function getPostLinkUrlDataProvider()
    {
        return [
            'コンテンツURLなし' => [11, '', false, ''],
            'ベースURLなし' => [6, '', false, 'https://main.com/news/archives/release'],
            'ベースURLあり、URL付与あり' => [6, '/sub', true, '/sub/news/archives/release'],
            'ベースURLあり、フルURL' => [6, '/sub', false, 'https://main.com/sub/news/archives/release'],
        ];
    }

    /**
     * 記事の本文を取得する
     *
     */
    public function testGetPostContent()
    {
        // 準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $site = SiteFactory::get(1);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', $site));
        $post = new BlogPost([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'name' => 'release',
            'content' => 'リリースコンテンツ',
            'detail' => 'detail test',
            'title' => 'プレスリリース',
            'status' => 1,
            'posted' => '2023-01-27 12:57:59',
        ]);
        // ファイルからコンテンツを取得
        $result = $this->Blog->getPostContent($post);
        $this->assertEquals('

<div class="post-body">リリースコンテンツ</div>
<div id="post-detail">detail test</div>
', $result);
        // blog_post_contentからコンテンツを取得
        $result = $this->Blog->getPostContent($post, true, false, 4);
        $this->assertEquals('リリース', $result);
        // blog_post_content_more からコンテンツを取得
        $result = $this->Blog->getPostContent($post, true, true, 2);
        $this->assertEquals('リリ

<p class="more">
<a href="/news/archives/1#post-detail">≫ 続きを読む</a></p>
', $result);
    }

    public static function getPostContentDataProvider()
    {
        return [
            [true, false, false, '<div class="post-body">test-content</div><div id="post-detail">test-detail</div>'],
            [false, false, false, '<div class="post-body">test-content</div>'],
            [false, true, false, '<div class="post-body">test-content</div><p class="more"><a href="/news/archives/3#post-detail">≫ 続きを読む</a></p>'],
            [false, false, 10, 'test-conte'],
        ];
    }

    /**
     * 詳細情報を取得する
     */
    public function testGetPostDetail()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $site = SiteFactory::get(1);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', $site));
        $post = new BlogPost([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'name' => 'release',
            'content' => 'リリースコンテンツ',
            'title' => 'プレスリリース',
            'detail' => 'detail リリース',
            'status' => 1,
            'posted' => '2023-01-27 12:57:59',
        ]);

        $result = $this->Blog->getPostDetail($post);
        $this->assertEquals('detail リリース', $result);

        //6文字限定
        $options = ['cut' => 6];
        $result = $this->Blog->getPostDetail($post, $options);
        $this->assertEquals('detail', $result);
    }

    /**
     * 詳細情報を出力する cut option利用時
     */
    public function testPostDetailCut()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $BlogPost = ClassRegistry::init('BlogPost');
        $post = $BlogPost->find('first', ['conditions' => ['BlogPost.id' => 1]]);

        $this->expectOutputString('詳細が入ります。詳細が入ります。詳細が入ります。詳細が入りま');

        //30文字限定
        $options = [
            'cut' => 30
        ];
        $this->Blog->postDetail($post, $options);
    }

    /**
     * 記事が属するカテゴリ名を取得する
     */
    public function testGetCategory()
    {
        // テストデータを作る
        BlogPostFactory::make([
            'id' => 1,
            'name' => 'test name',
            'blog_category_id' => 1,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'name' => 'category_name',
            'title' => 'category title',
            'lft' => 1,
            'rght' => 1,
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'name' => 'test name 2',
            'blog_category_id' => 2,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 2,
            'blog_content_id' => 2,
            'name' => '',
            'title' => 'category title 2',
            'lft' => 2,
            'rght' => 2,
        ])->persist();
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));

        // サービスを取得する
        /** @var BlogPostsService $service */
        $service = $this->getService(BlogPostsServiceInterface::class);

        // テストを実行する
        $blogPost = $service->get(1, ['contain' => ['BlogCategories']]);
        $result = $this->Blog->getCategory($blogPost);
        $this->assertEquals('<a href="/news/archives/category/category_name">category title</a>', $result);
        // URLを作成しない場合のテスト
        $result = $this->Blog->getCategory($blogPost, ['link' => false]);
        $this->assertEquals('category title', $result);
        // カテゴリは空になるテスト
        $blogPost = $service->get(2, ['contain' => ['BlogCategories']]);
        $result = $this->Blog->getCategory($blogPost);
        $this->assertEmpty($result);
    }

    /**
     * タグを取得する
     *
     * @dataProvider getTagDataProvider
     */
    public function testGetTag()
    {
        //データ準備
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 3,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'posted' => '2015-01-27 12:57:59',
        ])->persist();
        $this->loadFixtureScenario(BlogTagsScenario::class);
        BlogPostBlogTagFactory::make(['id' => 1, 'blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['id' => 2, 'blog_post_id' => 1, 'blog_tag_id' => 2])->persist();

        $site = SiteFactory::get(1);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', $site));
        $BlogPostsService = $this->getService(BlogPostsServiceInterface::class);
        //'link'=>false
        $post = $BlogPostsService->BlogPosts->get(1, ['contain' => ['BlogTags']]);
        $result = $this->Blog->getTag($post, ['link'=>false]);
        $this->assertEquals('tag1', $result[0]['name']);
        //'link'=>true
        $post = $BlogPostsService->BlogPosts->get(1, ['contain' => ['BlogTags']]);
        $result = $this->Blog->getTag($post, ['link'=>true]);
        $this->assertEquals('<a href="/news/archives/tag/tag1">tag1</a> , <a href="/news/archives/tag/tag2">tag2</a>', $result);
    }

    public static function getTagDataProvider()
    {
        return [
            [['separator' => ' , '], '<a href="/news/archives/tag/test1">test1</a> , <a href="/news/archives/tag/test2">test2</a>'],
            [['tag' => false], [
                ['name' => 'test1', 'url' => '/news/archives/tag/test1'],
                ['name' => 'test2', 'url' => '/news/archives/tag/test2']
            ]]
        ];
    }

    /**
     * カテゴリ一覧へのURLを取得する
     *
     * @param int $blogCategoryId ブログカテゴリーID
     * @param int $base URLベース
     * @param bool $useBase URLベースを利用するかどうか
     * @param string $expected 期待値
     * @dataProvider getCategoryUrlDataProvider
     */
    public function testGetCategoryUrl($blogCategoryId, $base, $useBase, $expected)
    {
        $this->truncateTable('contents');
        $this->truncateTable('blog_contents');
        $this->truncateTable('blog_categories');
        $this->loadFixtureScenario(MultiSiteBlogScenario::class);

        $blogContent = BlogContentFactory::get(6);
        $blogContent->content = ContentFactory::get(6);
        $this->Blog->getView()->set('blogContent', $blogContent);

        $siteUrl = Configure::read('BcEnv.siteUrl');
        Configure::write('BcEnv.siteUrl', 'https://main.com');
        Configure::write('BcEnv.host', 'main.com');
        $this->Blog->getView()->setRequest($this->getRequest('/', [], 'GET', $base? ['base' => $base] : []));
        $options = [
            'base' => $useBase
        ];

        $result = $this->Blog->getCategoryUrl($blogCategoryId, $options);
        $this->assertEquals($expected, $result, 'カテゴリ一覧へのURLを正しく取得できません');

        Configure::write('BcEnv.siteUrl', $siteUrl);
    }

    public static function getCategoryUrlDataProvider()
    {
        return [
            [1, '', false, '/news/archives/category/release'],
            [1, '/sub', false, '/news/archives/category/release'],
            [1, '/sub', true, '/sub/news/archives/category/release'],
            [2, '', false, '/news/archives/category/release/child'],
            [3, '', false, '/news/archives/category/child-no-parent'],
            [4, '', false, 'https://main.com/s/news2/archives/category/smartphone_release'],
            [5, '', false, 'https://main.com/en/news3/archives/category/english_release'],
            [6, '', false, 'http://example.com/news4/archives/category/another_domain_release'],
            [7, '', false, 'http://sub.main.com/news5/archives/category/sub_domain_release'],
        ];
    }

    /**
     * 登録日
     */
    public function testGetPostDate()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $site = SiteFactory::get(1);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', $site));
        $post = new BlogPost([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'name' => 'release',
            'content' => 'リリースコンテンツ',
            'title' => 'プレスリリース',
            'detail' => 'detail リリース',
            'status' => 1,
            'posted' => '2023-01-27 12:57:59',
        ]);
        $result = $this->Blog->getPostDate($post);
        $this->assertEquals('2023/01/27', $result);
    }

    /**
     * カテゴリーの一覧をリストタグで取得する
     * @param int $depth 階層
     * @param boolean $count 件数を表示するかどうか
     * @param array $options オプション
     * @param string $expected 期待値
     * @dataProvider getCategoryListDataProvider
     */
    public function testGetCategoryList($depth, $count, $options, $expected)
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $site = SiteFactory::get(1);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', $site));
        BlogCategoryFactory::make(['id' => 1, 'title' => 'title 1', 'name' => 'name-1', 'blog_content_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        BlogCategoryFactory::make(['id' => 2, 'parent_id'=> 1, 'title' => 'title 2', 'name' => 'name-2', 'lft' => 1, 'rght' => 2, 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 3, 'parent_id'=> 2, 'title' => 'title 3', 'name' => 'name-3', 'lft' => 1, 'rght' => 2, 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 4, 'title' => 'title 4', 'name' => 'name-4', 'blog_content_id' => 2])->persist();
        BlogPostFactory::make(['id' => 1, 'posted'=> '2015-01-27 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 1, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 2, 'posted'=> '2015-01-28 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 1, 'user_id'=>1, 'status' => true])->persist();
        $categories = $this->Blog->getCategories(['blogContentId'=>1, 'viewCount' => $count]);
        $result = $this->Blog->getCategoryList($categories, $depth, $count, $options);
        $this->assertEquals($expected, trim($result));
    }

    public static function getCategoryListDataProvider()
    {
        return [
            [
                1,
                false,
                [],
                '<ul class="bc-blog-category-list depth-1">
        <li class="bc-blog-category-list__item">
      <a href="/news/archives/category/name-1/name-2/name-3" current="1">title 1</a>          </li>
</ul>'
            ],
            [
                0,
                false,
                [],
                ''
            ],
            [
                2,
                true,
                [],
                '<ul class="bc-blog-category-list depth-1">
        <li class="bc-blog-category-list__item">
      <a href="/news/archives/category/name-1/name-2/name-3" current="1">title 1(2)</a>          </li>
</ul>'
            ],
        ];
    }

    /**
     * 前の記事へのリンクを出力する
     * @param int $blogContentId ブログコンテンツID
     * @param int $id 記事ID
     * @param int $posts_date 日付
     * @dataProvider prevLinkDataProvider
     */
    public function testPrevLink($blogContentId, $id, $posts_date, $expected)
    {
        //データ生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'alias_id' => 1])
            ->treeNode($blogContentId, 1, 3, 'news-2', '/news/', $blogContentId)->persist();
        BlogContentFactory::make(['id' => $blogContentId])->persist();
        SiteFactory::make()->main()->persist();

        BlogPostFactory::make([
            'id' => 1,
            'no' => 1,
            'blog_content_id' => $blogContentId,
            'title' => 'title 1',
            'posted' => $posts_date
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'no' => 2,
            'blog_content_id' => $blogContentId,
            'title' => 'title 2',
            'posted' => $posts_date
        ])->persist();
        BlogPostFactory::make([
            'id' => 3,
            'no' => 3,
            'blog_content_id' => 30,
            'title' => 'title 3',
            'posted' => $posts_date
        ])->persist();

        //currentContentをリセット
        $view = new BlogFrontAppView($this->getRequest());
        $blogContent = BlogContentFactory::get($blogContentId);
        $blogContent->content = ContentFactory::get($blogContentId);
        $view->set('blogContent', $blogContent);
        $this->Blog = new BlogHelper($view);

        $this->expectOutputString($expected);
        $this->Blog->prevLink(BlogPostFactory::get($id));
    }

    public static function prevLinkDataProvider()
    {
        return [
            [4, 2, '9000-08-10 18:58:07', '<a href="/news/archives/1" class="prev-link">≪ title 1</a>'],
            [4, 1, '1000-08-10 18:58:07', ''],
            [3, 3, '9000-08-10 18:58:07', ''],
            [3, 2, '1000-08-10 18:58:07', '<a href="/news/archives/1" class="prev-link">≪ title 1</a>'],
        ];
    }

    /**
     * test hasPrevLink
     */
    public function test_hasPrevLink()
    {
        //データ生成
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 3,
            'no' => 100,
            'title' => 'blog post 1',
            'posted' => '2020-10-02 09:00:00',
            'status' => 1,
            'publish_begin' => '2021-10-01 09:00:00',
            'publish_end' => '9999-11-01 09:00:00'
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'blog_content_id' => 3,
            'no' => 101,
            'title' => 'blog post 2',
            'posted' => '2022-10-02 09:00:00',
            'status' => 1,
            'publish_begin' => '2021-02-01 09:00:00',
            'publish_end' => '9999-12-01 09:00:00'
        ])->persist();
        BlogPostFactory::make([
            'id' => 3,
            'blog_content_id' => 3,
            'no' => 102,
            'title' => 'blog post 3',
            'posted' => '2022-08-02 09:00:00',
            'status' => 1,
            'publish_begin' => '2021-05-06 09:00:00',
            'publish_end' => '9999-02-01 09:00:00'
        ])->persist();
        //true戻りケース
        $result = $this->Blog->hasPrevLink(BlogPostFactory::get(2));
        $this->assertTrue($result);
        //false戻りケース
        $result = $this->Blog->hasPrevLink(BlogPostFactory::get(1));
        $this->assertFalse($result);
        //異常系
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->Blog->hasPrevLink(BlogPostFactory::get(111));
    }

    /**
     * test hasNextLink
     */
    public function test_hasNextLink()
    {
        //データ生成
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 3,
            'posted' => '2022-10-02 09:00:00',
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'blog_content_id' => 3,
            'posted' => '2022-10-02 09:00:00',
        ])->persist();
        //true戻りケース
        $result = $this->Blog->hasNextLink(BlogPostFactory::get(1));
        $this->assertTrue($result);
        //false戻りケース
        $result = $this->Blog->hasNextLink(BlogPostFactory::get(2));
        $this->assertFalse($result);
        //異常系
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->Blog->hasNextLink(BlogPostFactory::get(111));
    }

    /**
     * 次の記事へのリンクを出力する
     * @param int $blogContentId ブログコンテンツID
     * @param int $id 記事ID
     * @param int $posts_date 日付
     * @dataProvider nextLinkDataProvider
     */
    public function testNextLink($blogContentId, $id, $posts_date, $expected)
    {
        //データ生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'alias_id' => 1])
            ->treeNode($blogContentId, 1, 3, 'news-2', '/news/', $blogContentId)->persist();
        BlogContentFactory::make(['id' => $blogContentId])->persist();
        SiteFactory::make()->main()->persist();

        BlogPostFactory::make([
            'id' => 1,
            'no' => 1,
            'blog_content_id' => $blogContentId,
            'title' => 'title 1',
            'posted' => $posts_date
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'no' => 2,
            'blog_content_id' => $blogContentId,
            'title' => 'title 2',
            'posted' => $posts_date
        ])->persist();
        BlogPostFactory::make([
            'id' => 3,
            'no' => 3,
            'blog_content_id' => 30,
            'title' => 'title 3',
            'posted' => $posts_date
        ])->persist();

        //currentContentをリセット
        $view = new BlogFrontAppView($this->getRequest());
        $blogContent = BlogContentFactory::get($blogContentId);
        $blogContent->content = ContentFactory::get($blogContentId);
        $view->set('blogContent', $blogContent);
        $this->Blog = new BlogHelper($view);

        $this->expectOutputString($expected);
        $this->Blog->nextLink(BlogPostFactory::get($id));
    }

    public static function nextLinkDataProvider()
    {
        return [
            [4, 3, '9000-08-10 18:58:07', ''],
            [4, 1, '1000-08-10 18:58:07', '<a href="/news/archives/2" class="next-link">title 2 ≫</a>'],
            [2, 3, '9000-08-10 18:58:07', ''],
            [3, 3, '1000-08-10 18:58:07', ''], // 存在しないブログコンテンツ
        ];
    }

    /**
     * ブログテンプレートを取得
     *
     * @param string $theme テーマ名
     * @param array $expected 期待値
     * @dataProvider getBlogTemplatesDataProvider
     */
    public function testGetBlogTemplates($theme, $expected)
    {
        SiteFactory::make([
            'id' => 1,
            'theme' => $theme
        ])->persist();
        $result = $this->Blog->getBlogTemplates();
        $this->assertEquals($expected, $result);
    }

    public static function getBlogTemplatesDataProvider()
    {
        return [
            ['BcThemeSample', ['default' => 'default']]
        ];
    }

    /**
     * 公開状態を取得する
     */
    public function testAllowPublish()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $data = [
            'status' => true,
            'publish_begin' => '2015-08-10 18:58:07',
            'publish_end' => '9000-08-10 18:58:07'
        ];
        $result = $this->Blog->allowPublish($data);
        $this->assertEquals($result, 1, '公開状態を正しく取得できません');

        $data['status'] = 0;
        $result = $this->Blog->allowPublish($data);
        $this->assertEquals($result, 0, '公開状態を正しく取得できません');
    }

    /**
     * 記事中の画像を取得する
     *
     * @param int $num 何枚目の画像か順番を指定
     * @param boolean $link 詳細ページへのリンクをつけるかどうか
     * @param array $expected 期待値
     * @dataProvider getPostImgDataProvider
     */
    public function testGetPostImg($num, $link, $expected)
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogPostFactory::make([
            'id' => 111,
            'name' => 'test-name ',
            'no' => 1,
            'blog_content_id' => 1,
            'content' => '<img src="test1.jpg"><img src="test2.jpg">',
        ])->persist();
        $post = BlogPostFactory::get(111);
        $options = [
            'num' => $num,
            'link' => $link,
        ];
        $result = $this->Blog->getPostImg($post, $options);
        $this->assertEquals($expected, $result);
    }

    public static function getPostImgDataProvider()
    {
        return [
            [1, false, '<img src="/img/test1.jpg" alt="test-name ">'],
            [2, false, '<img src="/img/test2.jpg" alt="test-name ">'],
            [1, true, '<a href="/news/archives/1"><img src="/img/test1.jpg" alt="test-name "></a>'],
            [3, false, ''],
        ];
    }


    /**
     * 親カテゴリを取得する
     */
    public function testGetParentCategory()
    {
        // テストデータを作る
        //親カテゴリー
        BlogPostFactory::make([
            'id' => 11,
            'blog_category_id' => 11,
            'blog_content_id' => 1,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 11,
            'blog_content_id' => 1,
            'parent_id' => 0,
        ])->persist();
        //子カテゴリー
        BlogPostFactory::make([
            'id' => 12,
            'blog_category_id' => 12,
            'blog_content_id' => 1,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 12,
            'parent_id' => 11,
            'blog_content_id' => 1,
        ])->persist();
        $BlogPostsService = $this->getService(BlogPostsServiceInterface::class);

        //正常系
        $result = $this->Blog->getParentCategory($BlogPostsService->get(12));
        //戻り値を確認
        $this->assertEquals(11, $result->id);

        //異常系
        $result = $this->Blog->getParentCategory($BlogPostsService->get(11));
        //戻り値を確認
        $this->assertEmpty($result);
    }

    /**
     * アイキャッチ画像を取得する
     */
    public function testGetEyeCatch()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(RootContentScenario::class, 2, 1, null, '', '/');
        // テストデータを作る
        BlogPostFactory::make([
            'id' => 1,
            'name' => 'test name',
            'blog_category_id' => 1,
            'eye_catch' => 'test-eye_catch.jpg',
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'name' => 'test name',
            'blog_category_id' => 1,
        ])->persist();


        $this->getRequest();
        $post = BlogPostFactory::get(1);

        // $optionはデフォルト値のテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'escape' => true];
        $result = $this->Blog->getEyeCatch($post, $options);
        $expected = '/\/files\/blog\/1\/blog_posts\/test-eye_catch.jpg/';
        $this->assertMatchesRegularExpression($expected, $result, 'アイキャッチ画像を正しく取得できません');

        // aのタグを設定しないテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'link' => false];
        $result = $this->Blog->getEyeCatch($post, $options);
        $expected = '/\<a href/';
        // link=falseの場合Aタグがない確認する
        $this->assertDoesNotMatchRegularExpression($expected, $result, 'アイキャッチ画像を正しく取得できません');

        // alt属性のテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'alt' => 'テスト属性'];
        $result = $this->Blog->getEyeCatch($post, $options);
        $expected = '/alt="テスト属性"/';
        $this->assertMatchesRegularExpression($expected, $result, 'アイキャッチ画像を正しく取得できません');

        // 横幅と高さのテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'width' => '100', 'height' => '150'];
        $result = $this->Blog->getEyeCatch($post, $options);
        $expected = '/width="100" height="150"/';
        $this->assertMatchesRegularExpression($expected, $result, 'アイキャッチ画像を正しく取得できません');

        // 画像が存在しない場合に表示する画像のテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'noimage' => 'no_image.jpg'];
        $result = $this->Blog->getEyeCatch(BlogPostFactory::get(2), $options);
        $expected = '/\<img src\=\"\/img\/no_image\.jpg\"/';
        $this->assertMatchesRegularExpression($expected, $result, 'アイキャッチ画像を正しく取得できません');

        // 一時保存データと画像サイズのテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'tmp' => true, 'imgsize' => 'mobile_thumb'];
        $result = $this->Blog->getEyeCatch($post, $options);
        $expected = '/\<img src\=\"\/baser-core\/uploads\/tmp\/mobile_thumb\/test-eye_catch_jpg\"/';
        $this->assertMatchesRegularExpression($expected, $result, 'アイキャッチ画像を正しく取得できません');

        // class属性のテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'class' => 'thumb-image'];
        $result = $this->Blog->getEyeCatch($post, $options);
        $expected = '/class="thumb-image"/';
        $this->assertMatchesRegularExpression($expected, $result, 'アイキャッチ画像を正しく取得できません');

        // outputはurlのテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'output' => 'url'];
        $result = $this->Blog->getEyeCatch($post, $options);
        $expected = '/files/blog/1/blog_posts/test-eye_catch.jpg?';
        $this->assertEquals($expected, substr($result, 0, strlen($expected)));

        // outputはtagのテスト
        $options = ['table' => 'BcBlog.BlogPosts', 'output' => 'tag', 'escape' => true];
        $result = $this->Blog->getEyeCatch($post, $options);
        $expected = '/\<img/';
        $this->assertMatchesRegularExpression($expected, $result, 'アイキャッチ画像を正しく取得できません');
    }

    /**
     * メールフォームプラグインのフォームへのリンクを生成する
     */
    public function testMailFormLink()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->expectOutputString('<a href="/test-contentsName">test-title</a>');
        $this->Blog->mailFormLink('test-title', 'test-contentsName');
    }

    /**
     * 文字列から制御文字を取り除く
     */
    public function testRemoveCtrlChars()
    {
        $string = "\ebaserCMS \t\tHello \v\vWorld\0\f";
        $result = $this->Blog->removeCtrlChars($string);
        //戻り値を確認
        $this->assertEquals('baserCMS Hello World', $result);
    }

    /**
     * カテゴリ取得
     */
    public function testGetCategories()
    {
        //データ準備
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogPostFactory::make(['id' => 1, 'posted'=> '2015-01-27 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 1, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 2, 'posted'=> '2015-01-28 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 2, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 3, 'posted'=> '2015-01-28 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 3, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 4, 'posted'=> '2015-01-28 12:57:59', 'blog_content_id'=> 2, 'blog_category_id'=> 4, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 5, 'posted'=> '2015-01-28 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 5, 'user_id'=>1, 'status' => true])->persist();
        BlogPostFactory::make(['id' => 6, 'posted'=> '2013-01-28 12:57:59', 'blog_content_id'=> 1, 'blog_category_id'=> 5, 'user_id'=>1, 'status' => true])->persist();
        BlogCategoryFactory::make(['id' => 1, 'title' => 'title 1', 'name' => 'name-1', 'blog_content_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        BlogCategoryFactory::make(['id' => 2, 'parent_id'=> 1, 'title' => 'title 2', 'name' => 'name-2', 'lft' => 1, 'rght' => 2, 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 3, 'parent_id'=> 2, 'title' => 'title 3', 'name' => 'name-3', 'lft' => 1, 'rght' => 2, 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 4, 'title' => 'title 4', 'name' => 'name-4', 'blog_content_id' => 2])->persist();
        BlogCategoryFactory::make(['id' => 5,  'parent_id'=> 3,'title' => 'title 5', 'name' => 'name-5', 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 6,  'parent_id'=> 3,'title' => 'title 6', 'name' => 'name-6', 'blog_content_id' => 1])->persist();
        // １階層、かつ、siteId=0
        $categories = $this->Blog->getCategories(['siteId' => 1]);
        $this->assertCount(1, $categories);
        // サイトフィルター解除
        $categories = $this->Blog->getCategories(['siteId' => false]);
        $this->assertEquals(2, count($categories));
        // 深さ指定（子）
        $categories = $this->Blog->getCategories(['siteId' => 1, 'depth' => 2])->toArray();
        $this->assertEquals(2, $categories[0]->children->toArray()[0]->id);
        // 深さ指定（孫）
        $categories = $this->Blog->getCategories(['siteId' => 1, 'depth' => 3])->toArray();
        $this->assertEquals(3, $categories[0]->children->toArray()[0]->children->toArray()[0]->id);
        // ブログコンテンツID指定
        $categories = $this->Blog->getCategories(['siteId' => null, 'blogContentId' => 1]);
        $this->assertEquals(1, count($categories));
        // 並べ替え指定
        $categories = $this->Blog->getCategories(['siteId' => null, 'order' => 'BlogCategories.name'])->toArray();
        $this->assertEquals(1, $categories[0]->id);
        // 親指定
        $categories = $this->Blog->getCategories(['parentId' => 2])->toArray();
        $this->assertEquals(3, $categories[0]->id);
        // ID指定
        $categories = $this->Blog->getCategories(['id' => 3])->toArray();
        $this->assertEquals('title 3', $categories[0]->title);

        // 正常:　$blogContentId = 1
        $result = $this->Blog->getCategories(['blogContentId'=>1]);
        $this->assertCount(1, $result);

        // 存在しないID
        $result = $this->Blog->getCategories(['blogContentId'=>0]);
        $this->assertEmpty($result);

        // option depth 2
        $result = $this->Blog->getCategories(['blogContentId'=>1, 'depth' => 2]);
        $this->assertEquals('name-2', $result->toArray()[0]->children->toArray()[0]->name);

        // option type year
        $result = $this->Blog->getCategories(['blogContentId'=>1, 'type' => 'year']);
        $this->assertEquals('name-5', $result['2013'][0]->name);

        // option viewCount true
        $result = $this->Blog->getCategories(['blogContentId'=>1, 'viewCount' => true]);
        $this->assertEquals(1, $result->toArray()[0]->count);

        // option limit true
        $result = $this->Blog->getCategories(['blogContentId'=>1, 'type' => 'year', 'limit' => 1, 'viewCount' => true]);
        $this->assertEquals(1, $result['2015'][0]->count);
    }

    /**
     * 記事中のタグで指定したIDの内容を取得する
     */
    public function testGetHtmlById()
    {
        //データ準備
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogPostFactory::make(
            [
                'id' => 123,
                'name' => 'test-name ',
                'blog_content_id'=> 1,
                'content' => '<p id="test-id1">test-content1</p><div id="test-id2">test-content1</div>',
                'detail' => '<p id="test-id22">test-content2</p>',
                'status' => true
            ])->persist();
        $post = BlogPostFactory::get(123);
        $result = $this->Blog->getHtmlById($post, 'test-id1');
        $this->assertEquals('test-content1', $result);
        $result = $this->Blog->getHtmlById($post, 'test-id123');
        $this->assertEquals('', $result);
    }
    /**
     * 子カテゴリを持っているかどうか
     *
     * BlogCategory::hasChild() のラッピングのため、テストはスルー
     *
     * public function testHasChildCategory() {
     * $this->markTestIncomplete('このメソッドは、BlogCategory::hasChild() をラッピングしているメソッドのためスキップします。');
     * }
     */

    /**
     * タグリストを取得する
     *
     * @param mixed $expected
     * @param mixed $name
     * @param array $options
     * @dataProvider getTagListDataProvider
     */
    public function testGetTagList($expected, $name, $options = [])
    {
        // 準備
        $this->truncateTable('blog_contents');
        $this->truncateTable('contents');
        $this->truncateTable('sites');
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);
        $this->loadFixtureScenario(BlogTagsScenario::class);
        BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 2, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 2, 'blog_tag_id' => 2])->persist();

        // 実行
        $result = $this->Blog->getTagList($name, $options);
        if ($result->count()) {
            $result = Hash::extract($result->toArray(), '{n}.name');
        } else {
            $result = null;
        }
        $this->assertEquals($expected, $result);
    }

    public static function getTagListDataProvider()
    {
        return [
            [['tag1'], 'news'],
            [['tag1', 'tag2'], '/s/news/'],
            [['tag1', 'tag2', 'tag3'], null],
            [['tag1', 'tag2'], null, ['siteId' => 2]],
        ];
    }

    /**
     * タグリストを出力する
     *
     * @param string $expected
     * @param mixed $name
     * @param array $options
     * @dataProvider tagListDataProvider
     */
    public function testTagList($expected, $name, $options = [])
    {
        $this->truncateTable('blog_contents');
        $this->truncateTable('contents');
        $this->truncateTable('sites');
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);
        $this->loadFixtureScenario(BlogTagsScenario::class);
        BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 2, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 2, 'blog_tag_id' => 2])->persist();

        $this->expectOutputRegex($expected);
        $this->Blog->tagList($name, $options);
    }


    public static function tagListDataProvider()
    {
        return [
            ['/(?=\/tag\/tag1).*?(?!.*\/tag\/tag2).*?(?!.*\/tag\/tag3)/s', 'news'],
            ['/(?=\/tag\/tag1).*?(?=\/tag\/tag2).*?(?!.*\/tag\/tag3)/s', '/s/news/'],
            ['/(?=\/tags\/tag1).*?(?=\/tags\/tag2).*?(?=\/tags\/tag3)/s', null],
            ['/(?=\/tag\/tag1).*?\(1\)/s', '/news/', ['postCount' => true]],
        ];
    }

    /**
     * test _mergePostCountToTagsData
     */
    public function test_mergePostCountToTagsData()
    {
        $this->truncateTable('blog_contents');
        $this->truncateTable('contents');
        $this->truncateTable('sites');

        //データ生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);
        $this->loadFixtureScenario(BlogTagsScenario::class);
        BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 2, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 2, 'blog_tag_id' => 2])->persist();

        //サービス
        $blogTagsService = $this->getService(BlogTagsServiceInterface::class);

        $tags = $blogTagsService->getIndex([])->all();
        $options = [
            'direction' => 'ASC',
            'sort' => 'name',
            'siteId' => null,
            'contentUrl' => ['/news/']
        ];
        //対象メソッドをコール
        $rs = $this->execPrivateMethod($this->Blog, '_mergePostCountToTagsData', [$tags, $options]);
        //戻り値を確認
        $this->assertCount(3, $rs);

        //post_count の値をチェック
        $tags = $rs->toArray();
        $this->assertEquals(1, $tags[0]->post_count);
    }

    /**
     * ブログタグ記事一覧へのリンクURLを取得する
     * @param string $expected
     * @param int $blogContentId
     * @param string $base
     * @dataProvider getTagLinkUrlDataProvider
     */
    public function testGetTagLinkUrl($blogContentId, $base, $expected)
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $tag = BlogTagFactory::get(1);
        /** @var BlogTag $tag */
        $url = $this->Blog->getTagLinkUrl($blogContentId, $tag, $base);
        $this->assertEquals($expected, $url);
    }

    public static function getTagLinkUrlDataProvider()
    {
        return [
            [1, false, 'https://localhost/news/archives/tag/tag1'],
            [0, false, '/tags/tag1'],
        ];
    }

    /**
     * タグ記事一覧へのリンクタグを取得する
     * @param string $expected
     * @param int $blogContentId
     * @param array $option
     * @dataProvider getTagLinkDataProvider
     */
    public function testGetTagLink1($expected, $blogContentId, $option)
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $site = SiteFactory::get(1);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', $site));
        $tag = BlogTagFactory::get(1);
        $url = $this->Blog->getTagLink($blogContentId, $tag, $option);
        $this->assertEquals($expected, $url);
    }

    public static function getTagLinkDataProvider()
    {
        return [
            ['<a href="/news/archives/tag/tag1">tag1</a>', 1, []],
            ['<a href="/tags/tag1">tag1</a>', 0, []],
            ['<a href="https://localhost/news/archives/tag/tag1">tag1</a>', 1, ['ssl'=>true]],
        ];
    }

    /**
     * タグ記事一覧へのリンクタグを出力する
     *
     * public function testTagLink() {
     * $this->markTestIncomplete('このメソッドは、BlogHelper::getTagLink() をラッピングしているメソッドのためスキップします。');
     * }
     */

    /**
     * ブログ記事一覧出力
     *
     * @param $currentUrl
     * @param string | array $contentsName 管理システムで指定したコンテンツ名
     * @param int $num 記事件数
     * @param array $options オプション
     * @param $expected string 期待値
     * @param $message string テスト失敗時に表示されるメッセージ
     * @dataProvider postsDataProvider
     * @todo $this->currentContent が初期状態で固定ページになっている場合に正常に動作するテストを追加する
     */
    public function testPosts($currentUrl, $contentsName, $num, $options, $expected, $message = null)
    {
        $this->truncateTable('contents');
        $this->truncateTable('blog_contents');
        $this->truncateTable('blog_posts');

        // データ生成
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            1, // parentId
            'news1', // name
            '/news/', // url,
            'News 1' // title
        );
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'release', 'lft' => 1, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 1, 'name' => 'child', 'lft' => 2, 'rght' => 3, 'parent_id' => 1])->persist();
        BlogPostFactory::make(['id'=> 1, 'blog_content_id' => 1, 'title' => 'title test'])->persist();
        BlogPostFactory::make(['blog_content_id' => 1, 'title' => 'title blog1', 'blog_category_id' => 1, 'posted' => '2016-01-27 12:57:59'])->persist();
        BlogPostFactory::make(['blog_content_id' => 1, 'title' => 'title blog2', 'blog_category_id' => 2, 'posted' => '2017-01-27 12:57:59'])->persist();
        BlogPostFactory::make(['blog_content_id' => 1, 'blog_category_id' => 1, 'posted' => '2017-03-27 12:57:59'])->persist();
        BlogTagFactory::make(['id' => 1, 'name' => '新製品'])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 1])->persist();

        $this->expectOutputRegex($expected);

        if ($currentUrl) {
            $this->Blog->request = $this->getRequest($currentUrl);
        }

        $this->Blog->posts($contentsName, $num, $options);
        $this->expectOutputString($expected);
    }

    public static function postsDataProvider()
    {
        return [
            ['', '/news/', 5, [], '/post-1.*post-2.*post-3/s', '記事が出力されません'], // 通常
            ['', 'news2', 5, [], '/(?=no-data)/', '存在しないコンテンツが存在しています'],    // 存在しないコンテンツ
            ['', '/news/', 2, [], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', '記事の件数を正しく指定できません'], // 件数指定
            ['', '/news/', 5, ['category' => 'release'], '/post-1.*post-2.*post-3/s', '記事のカテゴリを正しく指定できません'], // カテゴリ指定（子カテゴリあり）
            ['', '/news/', 5, ['category' => 'child'], '/post-1/s', '記事のカテゴリを正しく指定できません'], // カテゴリ指定（子カテゴリなし）
            ['', '/news/', 5, ['tag' => '新製品'], '/^(?!.*post-3).*(?=post-1).*(?!.post-2).*/s', '記事のタグを正しく指定できません'], // tag指定
            ['', '/news/', 5, ['tag' => 'テスト'], '/記事がありません/', '記事のタグを正しく指定できません'], // 存在しないtag指定
            ['', '/news/', 5, ['year' => '2016'], '/^(?!.*post-3).*(?=post-1).*(?!.post-2).*/s', '記事の年を正しく指定できません'], // 年指定
            ['', '/news/', 5, ['year' => '2017'], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', '記事の年を正しく指定できません'], // 年指定
            ['', '/news/', 5, ['year' => '2999'], '/記事がありません/', '記事の年を正しく指定できません'], // 記事がない年指定  OK
            ['', '/news/', 5, ['month' => '1'], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', '記事の月を正しく指定できません'], // 月指定
            ['', '/news/', 5, ['day' => '27'], '/post-1.*post-2.*post-3/s', '記事の日を正しく指定できません'], // 日指定
            ['', '/news/', 5, ['year' => '2016', 'month' => '01', 'day' => '27'], '/^(?!.*post-3).*(?=post-1).*(?!.post-2).*/s', '記事の年月日を正しく指定できません'], // 年月日指定
            ['', '/news/', 5, ['id' => 1], '/^(?!.*post-3).*(?=post-1).*(?!.*post-2).*/s', '記事のIDを正しく指定できません'], // ID指定
            ['', '/news/', 5, ['id' => 99], '/記事がありません/', '記事のIDを正しく指定できません'], // 存在しないID指定  OK
            ['', '/news/', 5, ['keyword' => 'title test'], '/^(?!.*post-3).*(?=post-1).*(?!.*post-2).*/s', '記事のキーワードを正しく指定できません'], // キーワード指定
            ['', '/news/', 5, ['keyword' => '記事のキーワード'], '/(?=no-data)/', '記事のキーワードを正しく指定できません'], // キーワード指定
            ['', '/news/', 5, ['contentsTemplate' => 'default'], '/post-1.*post-2.*post-3/s', 'contentsTemplateを正しく指定できません'], // contentsTemplateを指定
            ['', '/news/', 5, ['template' => 'archives', 'data' => ['blogArchiveType' => 'yearly', 'year' => '2016']], '/bs-blog-title/s', 'templateを正しく指定できません'], // template指定
            ['', '/news/', 5, ['direction' => 'ASC'], '/post-1.*post-2.*post-3/s', 'templateを正しく指定できません'], // 昇順指定
            ['', '/news/', 5, ['direction' => 'DESC'], '/post-1.*post-2.*post-3/s', 'templateを正しく指定できません'], // 降順指定
            ['', '/news/', 5, ['sort' => 'posted', 'direction' => 'ASC'], '/post-1.*post-2.*post-3/s', 'sortを正しく指定できません'], // modifiedでソート
            ['', '/news/', 2, ['page' => 1], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', 'pageを正しく指定できません'], // ページ指定
            ['', '/news/', 2, ['page' => 2], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', 'pageを正しく指定できません'], // ページ指定
            ['/s/', '/news/', 2, ['page' => 2], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', 'pageを正しく指定できません'], // ページ指定
            ['/service', '/news/', 2, [], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 失敗
            ['/news/', '', 2, ['contentsTemplate' => 'default'], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 成功
            ['/s/news/', '/news/', 2, [], '/^(?!.*post-3).*(?=post-1).*(?=post-2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 成功
        ];
    }

    /**
     * ブログ記事を取得する
     */
    public function testGetPosts()
    {
        $this->truncateTable('contents');
        $this->truncateTable('blog_contents');
        $this->truncateTable('blog_posts');

        // データ生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        //$contentsNameを設定しない場合、currentContentを取得
        $rs = $this->Blog->getPosts([], 1, []);
        $this->assertCount(1, $rs);

        //$contentsNameを設定した場合、
        $rs = $this->Blog->getPosts(['/news/'])->toArray();
        $this->assertEquals('プレスリリース', $rs[0]['title']);

        //$contentsNameを間違った場合、
        $rs = $this->Blog->getPosts(['news5'])->toArray();
        $this->assertCount(0, $rs);
    }

    /**
     * コンテンツ名を解析して検索条件を設定する
     */
    public function testParseContentName()
    {
        //$optionsはデフォルト場合
        $rs = $this->Blog->parseContentName('news', []);
        //戻り値を確認
        $this->assertEquals(['/news/'], $rs['contentUrl']);
        $this->assertEquals([], $rs['contentId']);
        $this->assertArrayNotHasKey('autoSetCurrentBlog', $rs);

        //$optionsはある場合
        $options = [
            'contentUrl' => ['index/news'],
            'contentId' => [1],
            'autoSetCurrentBlog' => false
        ];
        $rs = $this->Blog->parseContentName('news', $options);
        //戻り値を確認
        $this->assertEquals(['index/news', '/news/'], $rs['contentUrl']);
        $this->assertEquals([1], $rs['contentId']);
        $this->assertArrayNotHasKey('autoSetCurrentBlog', $rs);

        //autoSetCurrentBlog = true; contentUrl&contentId == null 場合、
        $rs = $this->Blog->parseContentName(null, []);
        //戻り値を確認
        $this->assertEquals('/news/', $rs['contentUrl']);
        $this->assertEquals(1, $rs['contentId']);
        $this->assertArrayNotHasKey('autoSetCurrentBlog', $rs);

        //autoSetCurrentBlog = false; contentUrl&contentId == null 場合、
        $options = [
            'autoSetCurrentBlog' => false
        ];
        $rs = $this->Blog->parseContentName(null, $options);
        //戻り値を確認
        $this->assertEquals([], $rs['contentUrl']);
        $this->assertEquals([], $rs['contentId']);
        $this->assertArrayNotHasKey('autoSetCurrentBlog', $rs);

        //autoSetCurrentBlog = false; contentUrl&contentId == null 場合、
        $options = [
            'autoSetCurrentBlog' => true
        ];
        $rs = $this->Blog->parseContentName(null, $options);
        //戻り値を確認
        $this->assertEquals('/news/', $rs['contentUrl']);
        $this->assertEquals(1, $rs['contentId']);
        $this->assertArrayNotHasKey('autoSetCurrentBlog', $rs);
    }

    /**
     * 全ブログコンテンツの基本情報を取得する
     *
     * @return void
     */
    public function testGetContents()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // 復数ブログのデータを取得
        $this->loadFixtures('ContentMultiBlog', 'BlogPostBlogBaserHelper');

        // 全件取得
        $blogs = $this->Blog->getContents();
        $this->assertEquals(3, count($blogs));
        $this->assertEquals(2, $blogs[0]['Content']['id']);
        // デフォルトでは記事数を取得しない
        $this->assertFalse(isset($blogs[0]['BlogContent']['post_count']));

        // ソート順を変更
        $options = [
            'sort' => 'Content.id DESC',
            'siteId' => 0
        ];
        $blogs = $this->Blog->getContents('', $options);
        $this->assertEquals(3, $blogs[0]['Content']['id']);

        // 記事数を取得
        $options = [
            'postCount' => true,
        ];
        $blogs = $this->Blog->getContents('', $options);
        $this->assertEquals(3, $blogs[0]['BlogContent']['post_count']);
        $this->assertEquals(0, $blogs[1]['BlogContent']['post_count']);

        // ブログ指定 1つなので、配列に梱包されてない
        $blogs = $this->Blog->getContents('news');
        $this->assertEquals('news', $blogs['Content']['name']);

        // IDで取得
        $blogs = $this->Blog->getContents(2);
        $this->assertEquals('topics', $blogs['Content']['name']);

        // 復数指定取得
        $blogs = $this->Blog->getContents(['topics', 'news']);
        $this->assertEquals(2, count($blogs));
    }

    /**
     * 現在のページがブログプラグインかどうかを判定する
     *
     * @param bool $expected 期待値
     * @param string $url リクエストURL
     * @return void
     * @dataProvider isBlogDataProvider
     */
    public function testIsBlog($expected, $url)
    {
        if (!$expected) {
            //currentContentを変更
            $view = new BlogFrontAppView($this->getRequest($url));
            $this->Blog = new BlogHelper($view);
        }
        $this->Blog->getView()->setRequest($this->getRequest($url));
        //戻り値を確認
        $this->assertEquals($expected, $this->Blog->isBlog());
    }

    public static function isBlogDataProvider()
    {
        return [
            //PC
            [false, '/'],
            [false, '/index'],
            [false, '/contact/index'],
            [true, '/news/index'],
            // モバイルページ
            [false, '/m/'],
            [false, '/m/index'],
            [false, '/m/contact/index'],
            [true, '/m/news/index'],
            // スマートフォンページ
            [false, '/s/'],
            [false, '/s/index'],
            [false, '/s/contact/index'],
            [true, '/s/news/index']
        ];
    }

    /**
     * ブログコンテンツのURLを取得する
     *
     * 別ドメインの場合はフルパスで取得する
     */
    public function testGetContentsUrl()
    {
        //データ生成
        $this->loadFixtureScenario(InitAppScenario::class);
        //ブログコンテンツのURLメソッドをコール
        $rs = $this->Blog->getContentsUrl(1);
        //戻る値を確認
        $this->assertEquals('https://localhost/news/', $rs);
    }

    /**
     * 指定したブログコンテンツIDが、現在のサイトと同じかどうか判定する
     */
    public function testIsSameSiteBlogContent()
    {
        //データ生成
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'alias_id' => 1])
            ->treeNode(2, 1, 2, 'news-2', '/news-2/', 2)->persist();
        BlogContentFactory::make(['id' => 2])->persist();
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'alias_id' => 1])
            ->treeNode(3, 2, 3, 'news-2', '/news-2/', 3)->persist();
        BlogContentFactory::make(['id' => 3])->persist();

        //currentContentをリセット
        $view = new BlogFrontAppView($this->getRequest());
        $blogContent = BlogContentFactory::get(1);
        $blogContent->content = ContentFactory::get(2);
        $view->set('blogContent', $blogContent);
        $this->Blog = new BlogHelper($view);

        //現在のサイトと同じいテスト
        $this->assertTrue($this->Blog->isSameSiteBlogContent(2));

        //現在のサイト異なるテスト
        $this->assertFalse($this->Blog->isSameSiteBlogContent(3));
    }

    /**
     * getCategoryByName
     * @dataProvider getCategoryByNameDataprovider
     */
    public function testGetCategoryByName($blogCategoryId, $type, $pass, $name, $expects)
    {
        //データ生成
        BlogCategoryFactory::make([
            'blog_content_id' => 1,
            'name' => 'child',
        ])->persist();

        SiteFactory::make(['id' => 1])->persist();
        $this->Blog->getView()->setRequest($this->getRequest('/')->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);

        //pass param
        $this->Blog->getView()->setRequest($this->getRequest()->withParam('pass', $pass));
        $result = $this->Blog->getCategoryByName($blogCategoryId, $name);

        //check result
        $this->assertEquals($expects, (bool)$result);
    }

    public static function getCategoryByNameDataprovider()
    {
        return [
            [1, 'category', ['child'], '', true],
            [1, 'hoge', '', 'child', true],
            [1, 'hoge', '', '', false]
        ];
    }

    /**
     * 記事件数を取得する
     */
    public function testGetPostCount()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->View->loadHelper('Paginator');
        $this->View->Paginator->request = $this->_getRequest('/news/');
        $this->View->Paginator->request->params = [
            'paging' => [
                'BlogPost' => [
                    'count' => 10
                ]
            ]
        ];
        $this->assertEquals(10, $this->Blog->getPostCount());
    }

    /**
     * ブログのアーカイブタイプを取得する
     * @dataProvider getBlogArchiveTypeDataProvider
     */
    public function testGetBlogArchiveType($url, $type, $expects)
    {
        $this->Blog->request = $this->getRequest($url);
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);
        $result = $this->Blog->getBlogArchiveType();
        $this->assertEquals($type, $result);

        $isArchive = false;
        if ($expects) {
            $isArchive = true;
        }
        $this->assertEquals($expects, $isArchive);
    }

    public static function getBlogArchiveTypeDataProvider()
    {
        return [
            ['/news/archives/category/release', 'category', true],
            ['/news/archives/tag/新製品', 'tag', true],
            ['/news/archives/archives/date/2016', 'yearly', true],
            ['/news/archives/archives/date/2016/02', 'monthly', true],
            ['/news/archives/archives/date/2016/02/10', 'daily', true],
            ['/news/archives/hoge', 'hoge', false], // 存在しないアーカイブの場合
            ['/news/', '', false],
        ];
    }

    /**
     * タグ別記事一覧ページ判定
     * @dataProvider isTagDataProvider
     */
    public function testIsTag($type, $expects)
    {
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);
        $result = $this->Blog->isTag();
        $this->assertEquals($expects, $result);
    }


    /**
     * test isCategory
     * @dataProvider isCategoryDataProvider
     *
     */
    public function test_isCategory($type, $expects)
    {
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);
        $result = $this->Blog->isCategory();
        $this->assertEquals($expects, $result);
    }

    public static function isCategoryDataProvider()
    {
        return [
            ['category', true],
            ['tag', false],
            ['yearly', false],
            ['monthly', false],
            ['daily', false],
            ['hoge', false], // 存在しないアーカイブの場合
            ['', false], // アーカイブ指定がない場合
        ];
    }

    /**
     * test isArchive
     * @dataProvider isArchiveDataProvider
     */
    public function test_isArchive($type, $expects)
    {
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);
        $result = $this->Blog->isArchive();
        $this->assertEquals($expects, $result);
    }

    public static function isArchiveDataProvider()
    {
        return [
            ['category', true],
            ['tag', true],
            ['yearly', true],
            ['monthly', true],
            ['daily', true],
            ['hoge', true], // 存在しないアーカイブの場合
            ['', false], // アーカイブ指定がない場合
        ];
    }


    /**
     * test isDate
     * @dataProvider isDateDataProvider
     */
    public function test_isDate($type, $expects)
    {
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);
        $result = $this->Blog->isDate();
        $this->assertEquals($expects, $result);
    }

    public static function isDateDataProvider()
    {
        return [
            ['category', false],
            ['tag', false],
            ['yearly', false],
            ['monthly', false],
            ['daily', true],
            ['hoge', false], // 存在しないアーカイブの場合
            ['', false], // アーカイブ指定がない場合
        ];
    }

    /**
     * test isDate
     * @dataProvider isMonthDataProvider
     */
    public function test_isMonth($type, $expects)
    {
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);
        $result = $this->Blog->isMonth();
        $this->assertEquals($expects, $result);
    }

    public static function isMonthDataProvider()
    {
        return [
            ['category', false],
            ['tag', false],
            ['yearly', false],
            ['monthly', true],
            ['daily', false],
            ['hoge', false], // 存在しないアーカイブの場合
            ['', false], // アーカイブ指定がない場合
        ];
    }

    /**
     * test isDate
     * @dataProvider isYearDataProvider
     */
    public function test_isYear($type, $expects)
    {
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);
        $result = $this->Blog->isYear();
        $this->assertEquals($expects, $result);
    }

    public static function isYearDataProvider()
    {
        return [
            ['category', false],
            ['tag', false],
            ['yearly', true],
            ['monthly', false],
            ['daily', false],
            ['hoge', false], // 存在しないアーカイブの場合
            ['', false], // アーカイブ指定がない場合
        ];
    }

    /**
     * test isSingle
     */
    public function test_isSingle()
    {
        SiteFactory::make(['id' => 1])->persist();
        //param is empty
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $result = $this->Blog->isSingle();
        $this->assertFalse($result);

        //param is not empty
        $this->Blog->getView()->setRequest($this->getRequest('/news/archives/1')->withAttribute('currentSite', SiteFactory::get(1)));
        $result = $this->Blog->isSingle();
        $this->assertTrue($result);

        //BlogArchiveType is not empty
        $this->Blog->getView()->setRequest($this->getRequest('/news/archives/2016/02/10/post-1')->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', 'daily');
        $result = $this->Blog->isSingle();
        $this->assertFalse($result);

    }

    public static function isTagDataProvider()
    {
        return [
            ['category', false],
            ['tag', true],
            ['yearly', false],
            ['monthly', false],
            ['daily', false],
            ['hoge', false], // 存在しないアーカイブの場合
            ['', false], // アーカイブ指定がない場合
        ];
    }

    /**
     * isHome
     */
    public function test_isHome()
    {
        SiteFactory::make(['id' => 1])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        //param is empty
        $result = $this->Blog->isHome();
        $this->assertFalse($result);
        //param is not empty
        $this->Blog->getView()->setRequest($this->getRequest('/news/'));
        $result = $this->Blog->isHome();
        $this->assertTrue($result);
    }

    /**
     * 現在のブログタグアーカイブのブログタグ情報を取得する
     * @dataProvider getCurrentBlogTagDataProvider
     */
    public function testGetCurrentBlogTag($url, $type, $isTag, $expects)
    {
        //create data test
        BlogTagFactory::make([
            'id' => '1',
            'name' => '新製品',
        ])->persist();

        SiteFactory::make(['id' => 1])->persist();
        $this->Blog->getView()->setRequest($this->getRequest($url)->withAttribute('currentSite', SiteFactory::get(1)));
        $this->Blog->getView()->set('blogArchiveType', $type);
        //check blog isTag
        $result = $this->Blog->isTag();
        $this->assertEquals($isTag, $result);

        //check data expects
        $result = $this->Blog->getCurrentBlogTag();
        $actual = (!empty($result)) ? $result->toArray() : [];
        unset($actual['created']);
        unset($actual['modified']);
        $this->assertEquals($expects, $actual);
    }

    public static function getCurrentBlogTagDataProvider()
    {
        return [
            ['/news/archives/tag/新製品', 'tag', true,
                [
                    'id' => '1',
                    'name' => '新製品',
                ]
            ],
            ['/news/archives/tag/test1', 'tag', true, []],
            ['/news/archives/category/test2', 'category', false, []],
        ];
    }

    /**
     * test getCategoryName
     */
    public function test_getCategoryName()
    {
        // テストデータを作る
        BlogPostFactory::make([
            'id' => 1,
            'name' => 'test name',
            'blog_category_id' => 1,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'name' => 'category_name',
            'title' => 'category title',
            'lft' => 1,
            'rght' => 1,
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'name' => 'test name',
            'blog_category_id' => 2,
        ])->persist();
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));

        // サービスを取得する
        /** @var BlogPostsService $service */
        $service = $this->getService(BlogPostsServiceInterface::class);

        // カテゴリある場合
        $blogPost = $service->get(1, ['contain' => ['BlogCategories']]);
        $result = $this->Blog->getCategoryName($blogPost);
        $this->assertEquals('category_name', $result);
        //　カテゴリなし場合
        $blogPost = $service->get(2, ['contain' => ['BlogCategories']]);
        $result = $this->Blog->getCategoryName($blogPost);
        $this->assertEmpty($result);

    }

    public function test_getCategoryTitle()
    {
        // テストデータを作る
        BlogPostFactory::make([
            'id' => 1,
            'name' => 'test name',
            'blog_category_id' => 1,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'name' => 'category_name',
            'title' => 'category title',
            'lft' => 1,
            'rght' => 1,
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'name' => 'test name',
            'blog_category_id' => 2,
        ])->persist();
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));

        // サービスを取得する
        /** @var BlogPostsService $service */
        $service = $this->getService(BlogPostsServiceInterface::class);

        // カテゴリある場合
        $blogPost = $service->get(1, ['contain' => ['BlogCategories']]);
        $result = $this->Blog->getCategoryTitle($blogPost);
        $this->assertEquals('category title', $result);
        //　カテゴリなし場合
        $blogPost = $service->get(2, ['contain' => ['BlogCategories']]);
        $result = $this->Blog->getCategoryTitle($blogPost);
        $this->assertEmpty($result);
    }

    /**
     * getPostId
     */
    public function test_getPostId()
    {
        // テストデータを作る
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->Blog->getView()->setRequest($this->getRequest()->withAttribute('currentSite', SiteFactory::get(1)));
        $post = new BlogPost([
            'id' => 1,
            'blog_content_id' => 1,
            'name' => 'release',
        ]);

        //check exits id
        $result = $this->Blog->getPostId($post);
        $this->assertEquals(1, $result);

        //check not exits id
        $post = new BlogPost([]);
        $result = $this->Blog->getPostId($post);
        $this->assertEmpty($result);
    }
}
