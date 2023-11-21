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

use App\View\AppView;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\RootContentScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcBlog\Test\Scenario\MultiSiteBlogPostScenario;
use BcBlog\Test\Scenario\MultiSiteBlogScenario;
use BcBlog\View\Helper\BlogHelper;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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
            '/news/' // url
        );
        $view = new AppView();
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
     * ブログIDを取得する
     */
    public function testGetCurrentBlogId()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->Blog->getCurrentBlogId();
        $expects = '1';
        $this->assertEquals($expects, $result, 'ブログIDを正しく取得できません');
    }

    /**
     * ブログのコンテンツ名を取得する
     */
    public function testGetBlogName()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->Blog->getBlogName();
        $expects = 'news';
        $this->assertEquals($expects, $result, 'ブログのコンテンツ名を正しく取得できません');
    }

    /**
     * タイトルを取得する
     */
    public function testGetTitle()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->Blog->getTitle();
        $expects = '新着情報';
        $this->assertEquals($expects, $result, 'タイトルを正しく取得できません');
    }

    /**
     * ブログの説明文を取得する
     */
    public function testGetDescription()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->Blog->getDescription();
        $expects = 'baserCMS inc. [デモ] の最新の情報をお届けします。';
        $this->assertEquals($expects, $result, 'ブログの説明文を正しく取得できません');
    }

    /**
     * 記事タイトルを取得する
     * @param string $name タイトル
     * @param bool $link リンクをつけるかどうか
     * @param array $options オプション
     * @param string $expected 期待値
     * @dataProvider getPostTitleDataProvider
     */
    public function testGetPostTitle($name, $link, $options, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $post = ['BlogPost' => [
            'blog_content_id' => 1,
            'name' => $name,
            'no' => 4,
        ]];
        $result = $this->Blog->getPostTitle($post, $link, $options);
        $this->assertEquals($expected, $result, '記事タイトルを正しく取得できません');
    }

    public function getPostTitleDataProvider()
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $post = ['BlogPost' => [
            'blog_content_id' => 1,
            'no' => 3,
        ]];
        $result = $this->Blog->getPostLink($post, 'test-title');
        $this->assertEquals('<a href="/news/archives/3">test-title</a>', $result, '記事へのリンクを正しく取得できません');
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

    public function getPostLinkUrlDataProvider()
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
     * @param bool $moreText 詳細データを表示するかどうか
     * @param bool $moreLink 詳細ページへのリンクを表示するかどうか
     * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力
     * @param string $expected
     * @dataProvider getPostContentDataProvider
     */
    public function testGetPostContent($moreText, $moreLink, $cut, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $post = ['BlogPost' => [
            'content' => 'test-content',
            'detail' => 'test-detail',
            'no' => 3,
            'blog_content_id' => 1
        ]];
        $result = $this->Blog->getPostContent($post, $moreText, $moreLink, $cut);
        $this->assertEquals($result, $expected, '記事の本文を正しく取得できません');
    }

    public function getPostContentDataProvider()
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $BlogPost = ClassRegistry::init('BlogPost');
        $post = $BlogPost->find('first', ['conditions' => ['BlogPost.id' => 1]]);

        $result = $this->Blog->getPostDetail($post);
        $expects = $post['BlogPost']['detail'];
        $this->assertEquals($expects, $result);

        //30文字限定
        $options = ['cut' => 30];
        $result = $this->Blog->getPostDetail($post, $options);
        $expects = '詳細が入ります。詳細が入ります。詳細が入ります。詳細が入りま';
        $this->assertEquals($expects, $result);
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
    public function testGetTag($options, $expects)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $post = [
            'BlogTag' => [
                ['name' => 'test1'],
                ['name' => 'test2'],
            ],
            'BlogContent' => [
                'name' => 'news'
            ]
        ];
        $result = $this->Blog->getTag($post, $options);
        $this->assertEquals($expects, $result, 'タグを正しく取得できません');
    }

    public function getTagDataProvider()
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
        $this->Blog->getView()->setRequest($this->getRequest('/', [], 'GET', $base? ['base' => $base] : []));
        $options = [
            'base' => $useBase
        ];

        $result = $this->Blog->getCategoryUrl($blogCategoryId, $options);
        $this->assertEquals($result, $expected, 'カテゴリ一覧へのURLを正しく取得できません');

        Configure::write('BcEnv.siteUrl', $siteUrl);
    }

    public function getCategoryUrlDataProvider()
    {
        return [
            [1, '', false, '/news/archives/category/release'],
            [1, '/sub', false, '/news/archives/category/release'],
            [1, '/sub', true, '/sub/news/archives/category/release'],
            [2, '', false, '/news/archives/category/release/child'],
            [3, '', false, '/news/archives/category/child-no-parent'],
            [4, '', false, 'https://main.com/s/news2/archives/category/smartphone_release'],
            [5, '', false, 'https://main.com/en/news3/archives/category/english_release'],
            [6, '', false, 'https://example.com/news4/archives/category/another_domain_release'],
            [7, '', false, 'https://sub.main.com/news5/archives/category/sub_domain_release'],
        ];
    }

    /**
     * 登録日
     */
    public function testGetPostDate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $post = ['BlogPost' => [
            'posts_date' => '2015-08-10 18:58:07'
        ]];
        $this->Blog->getPostDate($post);
    }

    /**
     * カテゴリーの一覧をリストタグで取得する
     *
     * @param int $depth 階層
     * @param boolean $count 件数を表示するかどうか
     * @param array $options オプション
     * @param string $expected 期待値
     * @dataProvider getCategoryListDataProvider
     */
    public function testGetCategoryList($depth, $count, $options, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        /* @var BlogCategory $BlogCategory */
        $BlogCategory = ClassRegistry::init('BcBlog.BlogCategory');
        $categories = $BlogCategory->getCategoryList(1, ['viewCount' => true, 'depth' => 3, 'siteId' => 0]);
        $result = $this->Blog->getCategoryList($categories, $depth, $count, $options);
        $this->assertEquals($result, $expected, 'カテゴリーの一覧をリストタグで正しく取得できません');
    }

    public function getCategoryListDataProvider()
    {
        return [
            [
                3,
                false,
                [],
                '<ul class="bc-blog-category-list depth-1"><li class="bc-blog-category-list__item"><a href="/news/archives/category/release">プレスリリース</a><ul class="bc-blog-category-list depth-2"><li class="bc-blog-category-list__item"><a href="/news/archives/category/release/child">子カテゴリ</a></li></ul></li><li class="bc-blog-category-list__item"><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ</a></li></ul>'
            ],
            [
                1,
                false,
                [],
                '<ul class="bc-blog-category-list depth-1"><li class="bc-blog-category-list__item"><a href="/news/archives/category/release">プレスリリース</a></li><li class="bc-blog-category-list__item"><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ</a></li></ul>'
            ],
            [
                0,
                false,
                [],
                ''
            ],
            [
                3,
                true,
                [],
                '<ul class="bc-blog-category-list depth-1"><li class="bc-blog-category-list__item"><a href="/news/archives/category/release">プレスリリース(3)</a><ul class="bc-blog-category-list depth-2"><li class="bc-blog-category-list__item"><a href="/news/archives/category/release/child">子カテゴリ(2)</a></li></ul></li><li class="bc-blog-category-list__item"><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ(0)</a></li></ul>'
            ],
        ];
    }

    /**
     * 前の記事へのリンクを出力する
     *
     * @param int $blogContentId ブログコンテンツID
     * @param int $id 記事ID
     * @param int $posts_date 日付
     * @dataProvider prevLinkDataProvider
     */
    public function testPrevLink($blogContentId, $id, $posts_date, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->expectOutputString($expected);
        $post = ['BlogPost' => [
            'blog_content_id' => $blogContentId,
            'id' => $id,
            'posts_date' => $posts_date
        ]];
        $this->Blog->prevLink($post);
    }

    public function prevLinkDataProvider()
    {
        return [
            [1, 4, '9000-08-10 18:58:07', '<a href="/news/archives/4" class="prev-link">≪ ４記事目</a>'],
            [1, 3, '1000-08-10 18:58:07', ''],
            [2, 2, '9000-08-10 18:58:07', '<a href="/" class="prev-link">≪ ８記事目</a>'],    // 存在しないブログコンテンツ
            [2, 1, '1000-08-10 18:58:07', ''],
        ];
    }

    /**
     * 次の記事へのリンクを出力する
     *
     * @param int $blogContentId ブログコンテンツID
     * @param int $id 記事ID
     * @param int $posts_date 日付
     * @dataProvider nextLinkDataProvider
     */
    public function testNextLink($blogContentId, $id, $posts_date, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->expectOutputString($expected);
        $post = ['BlogPost' => [
            'blog_content_id' => $blogContentId,
            'id' => $id,
            'posts_date' => $posts_date
        ]];
        $this->Blog->nextLink($post);
    }

    public function nextLinkDataProvider()
    {
        return [
            [1, 1, '9000-08-10 18:58:07', ''],
            [1, 2, '1000-08-10 18:58:07', '<a href="/news/archives/1" class="next-link">ホームページをオープンしました ≫</a>'],
            [2, 3, '9000-08-10 18:58:07', ''],
            [2, 4, '1000-08-10 18:58:07', '<a href="/" class="next-link">７記事目 ≫</a>'], // 存在しないブログコンテンツ
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->Blog->BcBaser->siteConfig['theme'] = $theme;
        $result = $this->Blog->getBlogTemplates();
        $this->assertEquals($result, $expected, 'ブログテンプレートを正しく取得できません');
    }

    public function getBlogTemplatesDataProvider()
    {
        return [
            ['nada-icons', ['default' => 'default']]
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $post = ['BlogPost' => [
            'blog_content_id' => 1,
            'name' => 'test-name ',
            'content' => '<img src="test1.jpg"><img src="test2.jpg">',
            'detail' => '',
            'no' => '',
        ]];
        $options = [
            'num' => $num,
            'link' => $link,
        ];
        $result = $this->Blog->getPostImg($post, $options);
        $this->assertEquals($expected, $result, '記事中の画像を正しく取得できません');
    }

    public function getPostImgDataProvider()
    {
        return [
            [1, false, '<img src="/img/test1.jpg" alt="test-name "/>'],
            [2, false, '<img src="/img/test2.jpg" alt="test-name "/>'],
            [1, true, '<a href="/news/archives/"><img src="/img/test1.jpg" alt="test-name "/></a>'],
            [3, false, ''],
        ];
    }

    /**
     * 記事中のタグで指定したIDの内容を取得する
     */
    public function testGetHtmlById()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $post = ['BlogPost' => [
            'content' => '<p id="test-id1">test-content1</p><div id="test-id2">test-content1</div>',
            'detail' => '<p id="test-id1">test-content2</p>',
        ]];
        $result = $this->Blog->getHtmlById($post, 'test-id1');
        $expected = 'test-content1';
        $this->assertEquals($expected, $result, '記事中のタグで指定したIDの内容を正しく取得できません');
    }

    /**
     * 親カテゴリを取得する
     */
    public function testGetParentCategory()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $message = '正しく親カテゴリーを取得できません';
        $post = ['BlogCategory' => ['id' => 1]];
        $result = $this->Blog->getParentCategory($post);
        $this->assertEmpty($result, $message);

        $post['BlogCategory']['id'] = 2;
        $result = $this->Blog->getParentCategory($post);
        $this->assertEquals('release', $result['BlogCategory']['name'], $message);
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * カテゴリ取得
     */
    public function testGetCategories()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->loadFixtures('BlogCategoryTree');
        // １階層、かつ、siteId=0
        $categories = $this->Blog->getCategories(['siteId' => 0]);
        $this->assertEquals(1, count($categories));
        // サイトフィルター解除
        $categories = $this->Blog->getCategories(['siteId' => false]);
        $this->assertEquals(2, count($categories));
        // 深さ指定（子）
        $categories = $this->Blog->getCategories(['siteId' => 0, 'depth' => 2]);
        $this->assertEquals(1, count($categories[0]['BlogCategory']['children']));
        // 深さ指定（孫）
        $categories = $this->Blog->getCategories(['depth' => 3]);
        $this->assertEquals(1, count($categories[0]['BlogCategory']['children'][0]['BlogCategory']['children']));
        // ブログコンテンツID指定
        $categories = $this->Blog->getCategories(['siteId' => null, 'blogContentId' => 1]);
        $this->assertEquals(1, count($categories));
        // 並べ替え指定
        $categories = $this->Blog->getCategories(['siteId' => null, 'order' => 'BlogCategory.name']);
        $this->assertEquals(4, $categories[0]['BlogCategory']['id']);
        // 親指定
        $categories = $this->Blog->getCategories(['parentId' => 2]);
        $this->assertEquals(3, $categories[0]['BlogCategory']['id']);
        // スレッド形式
        $categories = $this->Blog->getCategories(['threaded' => true]);
        $this->assertEquals(3, $categories[0]['children'][0]['children'][0]['BlogCategory']['id']);
        // ID指定
        $categories = $this->Blog->getCategories(['id' => 3]);
        $this->assertEquals('孫カテゴリ', $categories[0]['BlogCategory']['title']);
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->loadFixtures('BlogPostBlogTagFindCustomPrams');
        $this->loadFixtures('BlogPostsBlogTagBlogTagFindCustomPrams');
        $this->loadFixtures('BlogTagBlogTagFindCustomPrams');
        $this->loadFixtures('BlogContentBlogTagFindCustomPrams');
        $this->loadFixtures('ContentBlogTagFindCustomPrams');
        $result = $this->Blog->getTagList($name, $options);
        if ($result) {
            $result = Hash::extract($result, '{n}.BlogTag.name');
        }
        $this->assertEquals($expected, $result);
    }

    public function getTagListDataProvider()
    {
        return [
            [['タグ１'], 'blog1'],
            [['タグ１', 'タグ２'], 'blog2'],
            [['タグ１', 'タグ２', 'タグ３', 'タグ４', 'タグ５'], null],
            [['タグ１', 'タグ２', 'タグ３'], null, ['siteId' => 2]],
            [['タグ１', 'タグ２', 'タグ３'], ['/s/blog3/']],
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->loadFixtures('BlogPostBlogTagFindCustomPrams');
        $this->loadFixtures('BlogPostsBlogTagBlogTagFindCustomPrams');
        $this->loadFixtures('BlogTagBlogTagFindCustomPrams');
        $this->loadFixtures('BlogContentBlogTagFindCustomPrams');
        $this->loadFixtures('ContentBlogTagFindCustomPrams');
        $this->expectOutputRegex($expected);
        $this->Blog->tagList($name, $options);
    }


    public function tagListDataProvider()
    {
        return [
            ['/(?=\/tag\/タグ１).*?(?!.*\/tag\/タグ２).*?(?!.*\/tag\/タグ３)/s', 'blog1'],
            ['/(?=\/tag\/タグ１).*?(?=\/tag\/タグ２).*?(?=\/tag\/タグ３)/s', '/s/blog3/'],
            ['/(?=\/tags\/タグ１).*?(?=\/tags\/タグ２).*?(?=\/tags\/タグ３).*?(?=\/tags\/タグ４).*?(?=\/tags\/タグ５)/s', null],
            ['/(?=\/tag\/タグ１).*?\(2\)/s', 'blog1', ['postCount' => true]],
        ];
    }

    /**
     * ブログタグ記事一覧へのリンクURLを取得する
     *
     * @param string $expected
     * @param int $blogContentId
     * @param string $name
     * @dataProvider getTagLinkUrlDataProvider
     */
    public function testGetTagLinkUrl($currentUrl, $blogContentId, $name, $base, $useBase, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $siteUrl = Configure::read('BcEnv.siteUrl');
        Configure::write('BcEnv.siteUrl', 'http://main.com');
        $this->loadFixtures('ContentBcContentsRoute', 'SiteBcContentsRoute', 'BlogContentMultiSite', 'BlogPostBlogTagFindCustomPrams', 'BlogPostsBlogTagBlogTagFindCustomPrams', 'BlogTagBlogTagFindCustomPrams');
        BcSite::flash();
        $this->Blog->request = $this->_getRequest($currentUrl);
        $this->Blog->request->base = $base;
        $url = $this->Blog->getTagLinkUrl($blogContentId, ['BlogTag' => ['name' => $name]], $useBase);
        Configure::write('BcEnv.siteUrl', $siteUrl);
        $this->assertEquals($expected, $url);
    }

    public function getTagLinkUrlDataProvider()
    {
        return [
            ['/', 1, 'タグ１', '', false, '/news/archives/tag/タグ１'],
            ['/', 1, 'タグ１', '/sub', false, '/news/archives/tag/タグ１'],
            ['/', 1, 'タグ１', '/sub', true, '/sub/news/archives/tag/タグ１'],
            ['/en/', 3, 'タグ２', '', false, '/en/news/archives/tag/タグ２'],
            ['/', 4, 'タグ２', '', false, 'http://sub.main.com/news/archives/tag/タグ２'],
            ['/', null, 'タグ１', '', false, '/tags/タグ１'],
            ['/s/', null, 'タグ２', '', false, '/s/tags/タグ２']
        ];
    }

    /**
     * タグ記事一覧へのリンクタグを取得する
     *
     * @param string $expected
     * @param string $currentUrl
     * @param int $blogContentId
     * @param $name
     * @dataProvider getTagLinkDataProvider
     */
    public function testGetTagLink($expected, $currentUrl, $blogContentId, $name)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->Blog->request = $this->_getRequest($currentUrl);
        $this->loadFixtures('BlogPostBlogTagFindCustomPrams');
        $this->loadFixtures('BlogPostsBlogTagBlogTagFindCustomPrams');
        $this->loadFixtures('BlogTagBlogTagFindCustomPrams');
        $this->loadFixtures('BlogContentBlogTagFindCustomPrams');
        $this->loadFixtures('ContentBlogTagFindCustomPrams');
        $url = $this->Blog->getTagLink($blogContentId, ['BlogTag' => ['name' => $name]]);
        $this->assertEquals($expected, $url);
    }

    public function getTagLinkDataProvider()
    {
        return [
            ['<a href="/news/archives/tag/タグ１">タグ１</a>', '/', 1, 'タグ１'],
            ['<a href="/s/blog3/archives/tag/タグ２">タグ２</a>', '/s/', 3, 'タグ２'],
            ['<a href="/tags/タグ１">タグ１</a>', '/', null, 'タグ１'],
            ['<a href="/s/tags/タグ２">タグ２</a>', '/s/', null, 'タグ２']
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
     * @param string | array $contentsName 管理システムで指定したコンテンツ名
     * @param int $num 記事件数
     * @param array $options オプション
     * @param expected string 期待値
     * @param message string テスト失敗時に表示されるメッセージ
     * @dataProvider postsDataProvider
     * @todo $this->currentContent が初期状態で固定ページになっている場合に正常に動作するテストを追加する
     */
    public function testPosts($currentUrl, $contentsName, $num, $options, $expected, $message = null)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->loadFixtures('BlogPostBlogBaserHelper', 'BlogPostsBlogTag');
        $this->View->loadHelper('BcTime');
        $url = null;
        if ($contentsName) {
            if (!is_array($contentsName)) {
                $contentsName = [$contentsName];
            }
            $url = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $contentsName[0]) . '/';
        }
        if ($currentUrl) {
            $this->Blog->request = $this->_getRequest($currentUrl);
        }
        $this->expectOutputRegex($expected);
        $this->Blog->posts($contentsName, $num, $options);
    }

    public function postsDataProvider()
    {
        return [
            ['', 'news', 5, [], '/name1.*name2.*name3/s', '記事が出力されません'], // 通常
            ['', 'news2', 5, [], '/(?=no-data)/', '存在しないコンテンツが存在しています'],    // 存在しないコンテンツ
            ['', 'news', 2, [], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'], // 件数指定
            ['', 'news', 5, ['category' => 'release'], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事のカテゴリを正しく指定できません'], // カテゴリ指定（子カテゴリあり）
            ['', 'news', 5, ['category' => 'child'], '/^(?!.*name3).*(?!.*name1).*(?=name2).*/s', '記事のカテゴリを正しく指定できません'], // カテゴリ指定(子カテゴリなし)
            ['', 'news', 5, ['tag' => '新製品'], '/^(?!.*name3).*(?!.*name1).*(?=name2).*/s', '記事のタグを正しく指定できません'], // tag指定
            ['', 'news', 5, ['tag' => 'テスト'], '/記事がありません/', '記事のタグを正しく指定できません'], // 存在しないtag指定
            ['', 'news', 5, ['year' => '2016'], '/^(?!.*name1).*(?=name2).*(?=name3).*/s', '記事の年を正しく指定できません'], // 年指定
            ['', 'news', 5, ['year' => '2017'], '/^(?!.*name3).*(?!.*name2).*(?=name1).*/s', '記事の年を正しく指定できません'], // 年指定
            ['', 'news', 5, ['year' => '2999'], '/記事がありません/', '記事の年を正しく指定できません'], // 記事がない年指定
            ['', 'news', 5, ['month' => '2'], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の月を正しく指定できません'], // 月指定
            ['', 'news', 5, ['day' => '2'], '/^(?!.*name1).*(?=name2).*(?=name3).*/s', '記事の日を正しく指定できません'], // 日指定
            ['', 'news', 5, ['year' => '2016', 'month' => '02', 'day' => '02'], '/^(?!.*name1).*(?!.*name3).*(?=name2).*/s', '記事の年月日を正しく指定できません'], // 年月日指定
            ['', 'news', 5, ['id' => 2], '/^(?!.*name1).*(?!.*name3).*(?=name2).*/s', '記事のIDを正しく指定できません'], // ID指定
            ['', 'news', 5, ['id' => 99], '/記事がありません/', '記事のIDを正しく指定できません'], // 存在しないID指定
            ['', 'news', 5, ['keyword' => '1'], '/^(?!.*name2).*(?!.*name3).*(?=name1).*/s', '記事のキーワードを正しく指定できません'], // キーワード指定
            ['', 'news', 5, ['keyword' => 'content'], '/name1.*name2.*name3/s', '記事のキーワードを正しく指定できません'], // キーワード指定
            ['', null, 5, ['contentsTemplate' => 'default'], '/name1.*name2.*name3/s', 'contentsTemplateを正しく指定できません'], // contentsTemplateを指定
            ['', 'news', 5, ['template' => 'archives'], '/プレスリリース/s', 'templateを正しく指定できません'], // template指定
            ['', 'news', 5, ['direction' => 'ASC'], '/name3.*name2.*name1/s', 'templateを正しく指定できません'], // 昇順指定
            ['', 'news', 5, ['direction' => 'DESC'], '/name1.*name2.*name3/s', 'templateを正しく指定できません'], // 降順指定
            ['', 'news', 5, ['sort' => 'posts_date', 'direction' => 'ASC'], '/name3.*name2.*name1/s', 'sortを正しく指定できません'], // modifiedでソート
            ['', 'news', 2, ['page' => 1], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', 'pageを正しく指定できません'], // ページ指定
            ['', 'news', 2, ['page' => 2], '/^.+?<span class=\"title\">(?!.*name1).*(?!.*name2).*(?=name3).*/s', 'pageを正しく指定できません'], // ページ指定
            ['/s/', 'news', 2, ['page' => 2], '/^.+?<span class=\"title\">name3<\/span>.*/s', 'pageを正しく指定できません'], // ページ指定
            ['/service', 'news', 2, [], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 失敗
            ['/news/', '', 2, ['contentsTemplate' => 'default'], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 成功
            ['/s/news/', 'news', 2, [], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 成功
        ];
    }

    /**
     * ブログ記事を取得する
     */
    public function testGetPosts()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンテンツ名を解析して検索条件を設定する
     */
    public function testParseContentName()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->Blog->request = $this->_getRequest($url);
        $this->assertEquals($expected, $this->Blog->isBlog());
    }

    public function isBlogDataProvider()
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 指定したブログコンテンツIDが、現在のサイトと同じかどうか判定する
     */
    public function testIsSameSiteBlogContent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * testGetCategoryByName
     * @dataProvider getCategoryByName
     */
    public function testGetCategoryByName($blogCategoryId, $type, $pass, $name, $expects)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->Blog->request = $this->_getRequest('/');
        $this->View->set('blogArchiveType', $type);
        $this->Blog->request->params['pass'][1] = $pass;
        $result = $this->Blog->getCategoryByName($blogCategoryId, $name);
        $this->assertEquals($expects, (bool)$result);
    }

    public function getCategoryByName()
    {
        return [
            [1, 'category', 'child', '', true],
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->Blog->request = $this->_getRequest($url);
        $this->View->set('blogArchiveType', $type);
        $result = $this->Blog->getBlogArchiveType();
        $this->assertEquals($type, $result);

        $isArchive = false;
        if ($expects) {
            $isArchive = true;
        }
        $this->assertEquals($expects, $isArchive);
    }

    public function getBlogArchiveTypeDataProvider()
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->View->set('blogArchiveType', $type);
        $result = $this->Blog->isTag();
        $this->assertEquals($expects, $result);
    }

    public function isTagDataProvider()
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
     * 現在のブログタグアーカイブのブログタグ情報を取得する
     * @dataProvider getCurrentBlogTagDataProvider
     */
    public function testGetCurrentBlogTag($url, $type, $isTag, $expects)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->Blog->request = $this->_getRequest($url);
        $this->View->set('blogArchiveType', $type);

        $result = $this->Blog->isTag();
        $this->assertEquals($isTag, $result);

        $result = $this->Blog->getCurrentBlogTag();
        $this->assertEquals($expects, $result);
    }

    public function getCurrentBlogTagDataProvider()
    {
        return [
            ['/news/archives/tag/新製品', 'tag', true, [
                'BlogTag' => [
                    'name' => '新製品',
                    'id' => '1',
                    'created' => '2015-08-10 18:57:47',
                    'modified' => null,
                ],
            ]],
            ['/news/archives/tag/test1', 'tag', true, []],
            ['/news/archives/category/test2', 'category', false, []],
        ];
    }
}
