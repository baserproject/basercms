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

namespace BcBlog\Test\TestCase\Service;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostBlogTagFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Factory\BlogTagFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcBlog\Test\Scenario\BlogPostsAdminServiceScenario;
use BcBlog\Test\Scenario\MultiSiteBlogScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\I18n\FrozenTime;

/**
 * BlogPostsServiceTest
 *
 * @property BlogPostsService $BlogPostsService
 */
class BlogPostsServiceTest extends BcTestCase
{

    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogPostsService = $this->getService(BlogPostsServiceInterface::class);
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
     * test constructor
     */
    public function test__construct()
    {
        // テーブルを初期化のテスト
        $this->assertEquals('blog_posts', $this->BlogPostsService->BlogPosts->getTable());
    }

    /**
     * BlogPostsTable のファイルアップロードの設定を実施
     */
    public function testSetupUpload()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 単一データを取得する
     */
    public function testGet()
    {
        // データを生成
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])->persist();

//        ContentFactory::make(['id' => 1, 'site_id' => 1, 'type' => 'BlogContent', 'entity_id' => 1, 'title' => 'content title'])->persist();
        SiteFactory::make(['id' => 1, 'name' => 'site name'])->persist();
//        BlogContentFactory::make(['id' => 1, 'description' => 'baser blog description', 'tag_use' => true])->persist();
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'category name'])->persist();

        BlogTagFactory::make(['id' => 1])->persist();
        BlogTagFactory::make(['id' => 2])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 2])->persist();

        // サービスメソッドを呼ぶ
        // option status publish 公開状態にある
        $result = $this->BlogPostsService->get(1, ['status' => 'publish']);
        // 戻り値を確認
        $this->assertTrue($result->status);
        //BlogCategoryを取得できるか確認
        $this->assertEquals('category name', $result->blog_category->name);
        //blogTagsを取得できるか確認
        $this->assertCount(2, $result->blog_tags);
        //BlogContentを取得できるか確認
        $this->assertNotNull($result->blog_content);
        //BlogContentのコンテンツを取得できるか確認
        $this->assertNotNull($result->blog_content->content);
        //BlogPostのサイトを取得できるか確認
        $this->assertNotNull($result->blog_content->content->site);

        //BlogPostが非公開にする
        $this->BlogPostsService->unpublish(1);
        // 例外を表示
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        // $options の status を publish として、レコードの status が true でないデータを指定す
        $this->BlogPostsService->get(1, ['status' => 'publish']);
    }

    /**
     * ブログ記事一覧を取得する
     */
    public function testGetIndex()
    {
        // データを生成
        UserFactory::make(['id' => 2, 'name' => 'test user1'])->persist();
        UserFactory::make(['id' => 3, 'name' => 'test user2'])->persist();
        UserFactory::make(['id' => 4, 'name' => 'test user3'])->persist();
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1', 'user_id' => 2, 'title' => 'blog post1 user_id2'])->persist();
        BlogPostFactory::make(['id' => '2', 'blog_content_id' => '1', 'user_id' => 2, 'title' => 'blog post2 user_id2'])->persist();
        BlogPostFactory::make(['id' => '3', 'blog_content_id' => '1', 'user_id' => 2, 'title' => 'blog post3 user_id2'])->persist();
        BlogPostFactory::make(['id' => '4', 'blog_content_id' => '1', 'user_id' => 3, 'title' => 'blog post1 user_id3'])->persist();
        BlogPostFactory::make(['id' => '5', 'blog_content_id' => '1', 'user_id' => 3, 'title' => 'blog post2 user_id3'])->persist();

        // サービスメソッドを呼ぶ
        // num 取得件数 2件
        // direction 並び順 昇順
        // sort 並び順対象カラム id
        $result = $this->BlogPostsService->getIndex([
            'num' => '2',
            'direction' => 'ASC',
            'sort' => 'id',
        ]);

        // 戻り値を確認
        // 記事を取得できているか
        $this->assertInstanceOf(\Cake\ORM\Query::class, $result);
        $this->assertEquals(5, $result->count());
        $blogPosts = $result->all()->toArray();
        $this->assertCount(2, $blogPosts);
        $this->assertEquals('2', $blogPosts[0]->user_id);
        $this->assertEquals('blog post1 user_id2', $blogPosts[0]->title);
        $this->assertEquals('2', $blogPosts[1]->user_id);
        $this->assertEquals('blog post2 user_id2', $blogPosts[1]->title);

        // サービスメソッドを呼ぶ
        // id BlogPosts.id 4
        $result = $this->BlogPostsService->getIndex([
            'id' => '4',
        ]);

        // 戻り値を確認
        // 記事を取得できているか
        $this->assertInstanceOf(\Cake\ORM\Query::class, $result);
        $this->assertEquals(1, $result->count());
        $blogPosts = $result->all()->toArray();
        $this->assertCount(1, $blogPosts);
        $this->assertEquals('3', $blogPosts[0]->user_id);
        $this->assertEquals('blog post1 user_id3', $blogPosts[0]->title);

        // サービスメソッドを呼ぶ
        // 引数が空の場合でもデータが取得できること
        $result = $this->BlogPostsService->getIndex();
        // 戻り値を確認
        // 記事を取得できているか
        // 指定が無い場合は降順で取得される
        $this->assertInstanceOf(\Cake\ORM\Query::class, $result);
        $this->assertEquals(5, $result->count());
        $blogPosts = $result->all()->toArray();
        $this->assertCount(5, $blogPosts);
        $this->assertEquals('3', $blogPosts[0]->user_id);
        $this->assertEquals('blog post2 user_id3', $blogPosts[0]->title);
        $this->assertEquals('3', $blogPosts[1]->user_id);
        $this->assertEquals('blog post1 user_id3', $blogPosts[1]->title);
        $this->assertEquals('2', $blogPosts[2]->user_id);
        $this->assertEquals('blog post3 user_id2', $blogPosts[2]->title);
        $this->assertEquals('2', $blogPosts[3]->user_id);
        $this->assertEquals('blog post2 user_id2', $blogPosts[3]->title);
        $this->assertEquals('2', $blogPosts[4]->user_id);
        $this->assertEquals('blog post1 user_id2', $blogPosts[4]->title);
    }

    /**
     * コントロールソースを取得する
     */
    public function testGetDefaultValue()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $authUser['id'] = 1;
        $data = $this->BlogPost->getNew($authUser);
        $this->assertEquals($data['BlogPost']['user_id'], $authUser['id']);
        $this->assertMatchesRegularExpression('/' . '([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})' . '/', $data['BlogPost']['posts_date']);
        $this->assertEquals($data['BlogPost']['posts_date'], date('Y/m/d H:i:s'));
        $this->assertEquals($data['BlogPost']['status'], 0);
    }

    /**
     * カスタムファインダー　customParams
     *
     * @param array $options
     * @param mixed $expected
     * @dataProvider findIndexDataProvider
     */
    public function testFindIndex($type, $options, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        set_error_handler(function ($no, $str, $file, $line, $context) {
        });
        $result = $this->BlogPost->find('all', ...$options);
        if ($type == 'count') {
            $this->assertEquals($expected, count($result));
        } elseif ($type == 'name') {
            $this->assertEquals($expected, $result[0]['BlogPost']['name']);
        } elseif ($type == 'id') {
            $id = Hash::extract($result, '{n}.BlogPost.id');
            $this->assertEquals($expected, $id);
        }
    }

    public static function findIndexDataProvider()
    {
        return [
            ['count', [], 6],                                            // 公開状態全件取得
            ['count', ['preview' => true], 8],                            // 非公開も含めて全件取得
            ['count', ['contentId' => 1, 'category' => 'release'], 3],    // 親カテゴリ
            ['count', ['contentId' => 1, 'category' => 'child'], 2],    // 子カテゴリ
            ['count', ['category' => 'release', 'force' => true], 4],    // 親カテゴリ contentId指定なし、強制取得（カテゴリ名にマッチしたカテゴリIDに紐づくデータを取得）
            ['count', ['category' => 'hoge'], 0],                        // 存在しないカテゴリ
            ['count', ['num' => 2], 2],                                    // 件数指定
            ['count', ['listCount' => 3], 3],                            // 件数指定（非推奨）
            ['count', ['listCount' => 3, 'num' => 4], 4],                // 件数指定（num優先）
            ['count', ['tag' => '新製品'], 3],                            // タグ
            ['count', ['tag' => 'hoge'], 0],                            // 存在しないタグ
            ['count', ['year' => '2016'], 4],                                // 年
            ['count', ['year' => '2016', 'month' => 2], 4],                // 年月
            ['count', ['year' => 2016, 'month' => 2, 'day' => 10], 4],    // 年月日
            ['count', ['year' => 2016, 'month' => 2, 'day' => 1], 0],    // 年月日（対象なし）
            ['name', ['id' => 4], '４記事目'],                            // id（no）指定
            ['name', ['keyword' => '４記事'], '４記事目'],                // キーワード（１件ヒット）
            ['count', ['keyword' => '新商品を販売'], 5],                    // キーワード（復数件ヒット）
            ['name', ['keyword' => 'hoge 新商品'], '３記事目'],            // キーワード（復数キーワード）
            ['count', ['author' => 'basertest'], 5],                    // 作成者
            ['count', ['author' => 'admin'], 0],                        // 存在しない作成者
            ['id', ['sort' => 'id', 'category' => 'release', 'contentId' => 1], [3, 2, 1]],    // 並べ替え昇順
            ['id', ['sort' => 'id', 'direction' => 'DESC', 'category' => 'release', 'contentId' => 1], [3, 2, 1]],    // 並べ替え降順
            ['name', ['num' => 2, 'page' => 2], '４記事目'],                // ページ指定
            ['count', ['siteId' => 0], 6],                                // サイトID
            ['count', ['contentUrl' => '/news/'], 4],                    // コンテンツURL
            ['count', ['contentUrl' => ['/news/', '/topics/']], 6]        // コンテンツURL（復数）
        ];
    }

    /**
     * カテゴリ条件を生成する
     */
    public function testCreateCategoryCondition()
    {
        //データ　生成
        ContentFactory::make([
            'id' => '1',
            'url' => '/blog/',
            'name' => 'blog',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'site_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
            'site_root' => false,
            'status' => true
        ])->persist();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'widget_area' => '2',
            'use_content' => '1',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ])->persist();
        BlogPostFactory::make([
            'id' => '1',
            'blog_content_id' => 1,
            'title' => 'blog post',
            'blog_category_id' => 1
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

        //$blogContentIdがある場合、
        $result = $this->BlogPostsService->createCategoryCondition([], "release", 1);
        $this->assertEquals($result["BlogPosts.blog_category_id IN"][0], 1);

//        $blogContentIdがない、かつ$contentUrlがある場合、
        $result = $this->BlogPostsService->createCategoryCondition([], "release", null, '/abc', true);
        $this->assertEquals($result["BlogPosts.blog_category_id IN"][0], 1);

        //$blogContentIdがない、かつ$contentUrlがない、かつ$forceがTrue場合、
        $result = $this->BlogPostsService->createCategoryCondition([], "release", null, null, true);
        $this->assertEquals($result["BlogPosts.blog_category_id IN"][0], 1);
    }

    /**
     * タグ条件を生成する
     */
    public function testCreateTagCondition()
    {
        //データー生成
        BlogPostFactory::make([])->publish(1, 1)->persist();
        BlogPostFactory::make([])->publish(2, 1)->persist();

        BlogTagFactory::make(['id' => 3, 'name' => 'tag1'])->persist();
        BlogTagFactory::make(['id' => 4, 'name' => 'tag2'])->persist();

        BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 3])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 2, 'blog_tag_id' => 4])->persist();

        $blogPostsTable = $this->getTableLocator()->get('BcBlog.BlogPosts');

        // 単一：存在しているタグを確認場合
        $query = $blogPostsTable->find();
        $result = $this->BlogPostsService->createTagCondition($query, 'tag1');
        $this->assertEquals(1, $result->count());

        // 配列：存在しているタグを確認場合
        $query = $blogPostsTable->find();
        $result = $this->BlogPostsService->createTagCondition($query, ['tag1', 'tag2']);
        $this->assertEquals(2, $result->count());

        // 配列：存在しているタグと存在していないタグを確認場合
        $query = $blogPostsTable->find();
        $result = $this->BlogPostsService->createTagCondition($query, ['tag1111', 'tag1']);
        $this->assertEquals(1, $result->count());

        //配列：存在していないタグを確認場合
        $query = $blogPostsTable->find();
        $result = $this->BlogPostsService->createTagCondition($query, ['tag1111', 'tag22222']);
        $this->assertEquals(0, $result->count());
    }

    /**
     * キーワード条件を生成する
     */
    public function testCreateKeywordCondition()
    {
        $result = $this->BlogPostsService->createKeywordCondition([], "hello");
        //戻り値を確認
        $this->assertEquals("%hello%", $result['and'][0]['or'][0]['BlogPosts.title LIKE']);
        $this->assertEquals("%hello%", $result['and'][0]['or'][1]['BlogPosts.content LIKE']);
        $this->assertEquals("%hello%", $result['and'][0]['or'][2]['BlogPosts.detail LIKE']);

        //スペースを含むテストデータ
        $result = $this->BlogPostsService->createKeywordCondition([], "hello world");
        //戻り値を確認
        $this->assertEquals("%hello%", $result['and'][0]['or'][0]['BlogPosts.title LIKE']);
        $this->assertEquals("%hello%", $result['and'][0]['or'][1]['BlogPosts.content LIKE']);
        $this->assertEquals("%hello%", $result['and'][0]['or'][2]['BlogPosts.detail LIKE']);
        $this->assertEquals("%world%", $result['and'][1]['or'][0]['BlogPosts.title LIKE']);
        $this->assertEquals("%world%", $result['and'][1]['or'][1]['BlogPosts.content LIKE']);
        $this->assertEquals("%world%", $result['and'][1]['or'][2]['BlogPosts.detail LIKE']);
    }

    /**
     * 年月日条件を生成する
     */
    public function testCreateYearMonthDayCondition()
    {
        //データ 生成
        BlogPostFactory::make([
            'id' => '1',
            'name' => 'Duong Tai',
            'blog_content_id' => '2',
            'posted' => '2021-11-01 08:00:00',
            'user_id' => '1'
        ])->persist();

        $result = $this->BlogPostsService->createYearMonthDayCondition([], '2022', '11', '01');
        //戻り値を確認
        $this->assertEquals("2022", $result['YEAR(BlogPosts.posted)']);
        $this->assertEquals("11", $result['MONTH(BlogPosts.posted)']);
        $this->assertEquals("01", $result['DAY(BlogPosts.posted)']);
    }

    /**
     * 並び替え設定を生成する
     */
    public function testCreateOrder()
    {
        //昇順
        $result = $this->BlogPostsService->createOrder('test', 'ASC');
        $this->assertEquals('BlogPosts.test ASC, BlogPosts.id ASC', $result);
        //降順
        $result = $this->BlogPostsService->createOrder('test', 'DESC');
        $this->assertEquals('BlogPosts.test DESC, BlogPosts.id DESC', $result);
        //random
        $result = $this->BlogPostsService->createOrder('test', 'random');
        $this->assertEquals('RAND()', $result);
    }

    /**
     * ページ一覧用の検索条件を生成する
     * @dataProvider createIndexConditionsDataProvider
     */
     // TODO dataProvider内で、find() を実行しないように変更
     // dataProvider内で、find() を実行すると、DBの接続先が、test でなく、default になってしまい、
     // 全体実行の際に、他のテストに影響が出てしまうため。
//    public function testCreateIndexConditions($isLoadScenario, $query, $options, $expected)
//    {
//        $users = $this->getTableLocator()->get('BaserCore.Users');
//        if ($isLoadScenario) {
//            $this->loadFixtureScenario(MultiSiteBlogScenario::class);
//            BlogPostFactory::make([])->publish(1, 1)->persist();
//            BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();
//            BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
//            UserFactory::make(['id' => 1, 'name' => 'test_author'])->persist();
//        }
//
//        $result = $this->execPrivateMethod($this->BlogPostsService, "createIndexConditions", [$query, $options]);
//        $this->assertEquals($expected, $result);
//    }
//
//    public static function createIndexConditionsDataProvider(): array
//    {
//        $blogPost = new BlogPostsTable();
//        return [
//            // 空配列の結果テスト
//            [
//                false,
//                $blogPost->find('all'),
//                [],
//                $blogPost->find('all')
//            ],
//            //$params ID
//            [
//                false,
//                $blogPost->find('all'),
//                ['id' => 1],
//                $blogPost->find('all')->where(['BlogPosts.id' => 1])
//            ],
//            //$params タイトル
//            [
//                false,
//                $blogPost->find('all'),
//                ['title' => 'test title'],
//                $blogPost->find('all')->where(['BlogPosts.title LIKE' => '%test title%'])
//            ],
//            //$params ユーザーID
//            [
//                false,
//                $blogPost->find('all'),
//                ['user_id' => 1], $blogPost->find('all')->where(['BlogPosts.user_id' => 1])
//            ],
//            //$params ブログコンテンツID
//            [
//                false,
//                $blogPost->find('all'),
//                ['blog_content_id' => 1],
//                $blogPost->find('all')->where(['BlogPosts.blog_content_id' => 1])
//            ],
//            //$params サイトID
//            [
//                false,
//                $blogPost->find('all'),
//                ['site_id' => 1],
//                $blogPost->find('all')->where(['Contents.site_id' => 1])
//            ],
//            //$params URL
//            [
//                false,
//                $blogPost->find('all'),
//                ['contentUrl' => '/test'],
//                $blogPost->find('all')->contain(['BlogContents' => ['Contents']])->where(['Contents.url' => '/test'])
//            ],
//            //$params カテゴリID
//            [
//                true,
//                $blogPost->find('all'),
//                ['blog_category_id' => 1],
//                $blogPost->find('all')->where(['BlogPosts.blog_category_id IN' => [1, 2]])
//            ],
//            //$params カテゴリ名
//            [
//                true,
//                $blogPost->find('all'),
//                ['category' => 'release', 'force' => true],
//                $blogPost->find('all')->where(['BlogPosts.blog_category_id IN' => [1, 2]])
//            ],
//            //$params タグ名
//            [
//                true,
//                $blogPost->find('all'),
//                ['tag' => 'tag1'],
//                $blogPost->find('all')->where(['BlogPosts.id IN' => 1])
//            ],
//            //$params 年月日
//            [
//                false,
//                $blogPost->find('all'),
//                ['year' => 1994, 'month' => 8, 'day' => 21],
//                $blogPost->find('all')->where([
//                    'YEAR(BlogPosts.posted)' => 1994,
//                    'MONTH(BlogPosts.posted)' => 8,
//                    'DAY(BlogPosts.posted)' => 21
//                ])
//            ],
//            //$params No
//            [
//                false,
//                $blogPost->find('all'),
//                ['no' => 1, 'force' => true],
//                $blogPost->find('all')->where(['BlogPosts.no' => 1])
//            ],
//            // $params キーワード
//            [
//                false,
//                $blogPost->find('all'),
//                ['keyword' => 'test'],
//                $blogPost->find('all')->where([
//                    'and' => [
//                        0 => [
//                            'or' => [
//                                ['BlogPosts.title LIKE' => '%test%'],
//                                ['BlogPosts.content LIKE' => '%test%'],
//                                ['BlogPosts.detail LIKE' => '%test%']
//                            ]
//                        ]
//                    ]
//                ])],
//            //$params 作成者
//            [
//                true,
//                $blogPost->find('all'),
//                ['author' => 'test_author'],
//                $blogPost->find('all')->where(['BlogPosts.user_id' => 1])
//            ],
//        ];
//    }

    /**
     * 同じタグの関連投稿を取得する
     */
    public function testGetRelatedPosts()
    {
        //データ生成
        BlogPostFactory::make([])->publish(1, 1)->persist();
        BlogPostFactory::make([])->publish(2, 1)->persist();
        BlogPostFactory::make([])->publish(3, 2)->persist();

        BlogTagFactory::make(['id' => 1, 'name' => 'name blog tag'])->persist();
        BlogPostBlogTagFactory::make(['id' => 1, 'blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['id' => 2, 'blog_post_id' => 2, 'blog_tag_id' => 1])->persist();

        $blogPost = $this->BlogPostsService->BlogPosts->get(1, contain: ['BlogTags']);
        $result = $this->BlogPostsService->getRelatedPosts($blogPost)->toArray();
        //戻り値を確認
        $this->assertEquals(1, $result[0]["blog_content_id"]);
        $this->assertEquals(2, $result[0]["id"]);

        //blog_tagsがNULLを確認すること
        $blogPost = $this->BlogPostsService->BlogPosts->get(3, contain: ['BlogTags']);
        $result = $this->BlogPostsService->getRelatedPosts($blogPost);
        //結果はnullになる
        $this->assertCount(0, $result);
    }

    /**
     * 初期データ用のエンティティを取得
     */
    public function testGetNew()
    {
        //データ生成
        BlogPostFactory::make([])->publish(1, 1)->persist();

        // サービスメソッドを呼ぶ
        $result = $this->BlogPostsService->getNew(2, 1);
        //戻り値を確認

        //user_idが生成できるか確認
        $this->assertEquals("1", $result->user_id);

        //postedが生成できるか確認
        $this->assertEquals(\Cake\I18n\DateTime::now()->i18nFormat('yyyy-MM-dd'), $result->posted->i18nFormat('yyyy-MM-dd'));

        //statusが生成できるか確認
        $this->assertEquals(false, $result->status);

        //blog_content_idが生成できるか確認
        $this->assertEquals(2, $result->blog_content_id);
    }

    /**
     * 新規登録
     * BlogPostsService::create
     */
    public function testCreate()
    {
        // パラメータを生成
        $postData = [
            'title' => 'new blog',
            'user_id' => 1,
            'blog_content_id' => 1,
            'posted' => '2022-12-01 00:00:00',
            'publish_begin' => '2022-12-01 00:00:00',
            'publish_end' => '2022-12-31 23:59:59',
        ];
        // サービスメソッドを呼ぶ
        $entity = $this->BlogPostsService->create($postData);

        // 戻り値を確認
        $this->assertNotEmpty($entity);
        $this->assertInstanceOf('\Cake\Datasource\EntityInterface', $entity);
        $this->assertEquals(1, $entity->blog_content_id);
        $this->assertEquals('2022-12-01 00:00:00', $entity->posted->i18nFormat('yyyy-MM-dd HH:mm:ss'));
        $this->assertEquals('2022-12-01 00:00:00', $entity->publish_begin->i18nFormat('yyyy-MM-dd HH:mm:ss'));
        $this->assertEquals('2022-12-31 23:59:59', $entity->publish_end->i18nFormat('yyyy-MM-dd HH:mm:ss'));

        // blog_content_id を指定しなかった場合はエラーとなること
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('blog_content_id を指定してください。');
        // サービスメソッドを呼ぶ
        $this->BlogPostsService->create([]);
    }

    /**
     * 新規登録
     * BlogPostsService::create
     */
    public function testCreateExceptionTitle()
    {
        // パラメータを生成
        $postData = [
            'user_id' => 1,
            'blog_content_id' => 1,
            'posted' => '2022-12-01 00:00:00',
            'publish_begin' => '2022-12-01 00:00:00',
            'publish_end' => '2022-12-31 23:59:59',
        ];

        // title を指定しなかった場合はエラーとなること
        $this->expectException('Cake\ORM\Exception\PersistenceFailedException');
        $this->expectExceptionMessage('Entity save failure. Found the following errors (title._required: "タイトルを入力してください。"');
        // サービスメソッドを呼ぶ
        $this->BlogPostsService->create($postData);
    }

    /**
     * 新規登録
     * BlogPostsService::create
     * 投稿日エラーのテスト
     */
    public function testCreateExceptionPosted()
    {
        // パラメータを生成
        $postData = [
            'title' => 'new blog',
            'user_id' => 1,
            'blog_content_id' => 1,
            'posted' => '',
            'publish_begin' => '',
            'publish_end' => '',
        ];

        // postedが空の場合はエラーとなること
        $this->expectException('Cake\ORM\Exception\PersistenceFailedException');
        $this->expectExceptionMessage('Entity save failure. Found the following errors (posted._empty: "投稿日を入力してください。');
        // サービスメソッドを呼ぶ
        $this->BlogPostsService->create($postData);
    }

    /**
     * 新規登録
     * BlogPostsService::create
     * データ量エラーのテスト
     */
//    public function testCreateExceptionPostMaxSize()
//    {
    // TODO ローカルでは成功するが、GitHubActions上でうまくいかないためコメントアウト（原因不明）
    // データ量を超えていると仮定する
//        $postMaxSize = ini_get('post_max_size');
//        $_SERVER['REQUEST_METHOD'] = 'POST';
//        $_SERVER['CONTENT_LENGTH'] = BcUtil::convertSize($postMaxSize) + 1;
//
//        // データ量を超えている場合はエラーとなること
//        $this->expectException('BaserCore\Error\BcException');
//        $this->expectExceptionMessage("送信できるデータ量を超えています。合計で " . $postMaxSize . " 以内のデータを送信してください。");
//        // サービスメソッドを呼ぶ
//        $this->BlogPostsService->create([]);
//    }

    /**
     * ブログ記事を更新する
     */
    public function testUpdate()
    {
        //データ生成
        BlogPostFactory::make(['id' => 1, 'title' => 'post title 1'])->persist();

        // サービスメソッドを呼ぶ
        $result = $this->BlogPostsService->update(BlogPostFactory::get(1), ['title' => 'title of post 3']);
        // 戻り値を確認
        $this->assertEquals("title of post 3", $result["title"]);

        // titleの長さは256文字を指定しなかった場合はエラーとなること
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->expectExceptionMessage("タイトルは255文字以内で入力してください。");
        // サービスメソッドを呼ぶ
        $this->BlogPostsService->update(BlogPostFactory::get(1), ['title' => str_repeat('a', 256)]);
    }

    /**
     * 公開状態を取得する
     */
    public function testAllowPublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コントロールソースを取得する
     */
    public function testGetControlSource()
    {
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'title' => 'test title 4'])->persist();
        BlogTagFactory::make(['id' => 2, 'name' => 'tag name1'])->persist();
        BlogTagFactory::make(['id' => 3, 'name' => 'tag name2'])->persist();
        BlogPostBlogTagFactory::make(['blog_tag_id' => 2])->persist();
        BlogPostBlogTagFactory::make(['blog_tag_id' => 3])->persist();
        UserFactory::make(['id' => '1', 'name' => 'test 1', 'nickname' => 'James'])->persist();
        UserFactory::make(['id' => '2', 'nickname' => 'nyc'])->persist();

        //$field = blog_category_id　かつ　blogContentId = 1
        $result = $this->BlogPostsService->getControlSource('blog_category_id', ['blogContentId' => 1]);
        //戻り値を確認
        $this->assertEquals('test title 4', $result[1]);

        //$field = blog_category_id　かつ　配列　blogContentIdと他の変数　
        $result = $this->BlogPostsService->getControlSource('blog_category_id', ['blogContentId' => 1, 'empty' => 'sd']);
        //戻り値を確認
        $this->assertEquals(['' => 'sd', 1 => 'test title 4'], $result);

        //$field = blog_category_id　かつ　配列　存在しないblogContentId
        $result = $this->BlogPostsService->getControlSource('blog_category_id', ['blogContentId' => '2']);
        //戻り値を確認
        $this->assertEquals([], $result);

        //$field = user_id　かつ　空配列
        $result = $this->BlogPostsService->getControlSource('user_id');
        //戻り値を確認
        $this->assertEquals([1 => 'James', 2 => 'nyc'], $result);

        //$field = user_id　かつ　条件配列
        $result = $this->BlogPostsService->getControlSource('user_id', [['name' => 'test 1']]);
        //戻り値を確認
        $this->assertEquals([1 => 'James'], $result);

        //$field = blog_tag_id　かつ　空配列
        $result = $this->BlogPostsService->getControlSource('blog_tag_id');
        //戻り値を確認
        $this->assertEquals([2 => 'tag name1', 3 => 'tag name2'], $result);
    }

    /**
     * 記事を公開状態に設定する
     */
    public function testPublish()
    {
        // データを生成
        // データを作成する
        ContentFactory::make(['entity_id' => 1, 'plugin' => 'BcBlog', 'type' => 'BlogContent'])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make([])->unpublish(1, 1)->persist();

        // サービスメソッドを呼ぶ
        $entity = $this->BlogPostsService->publish(1);
        //戻る値を確認
        $this->assertTrue($entity->status);
        $this->assertNull($entity->publish_begin);
        $this->assertNull($entity->publish_end);
    }

    /**
     * 記事を非公開状態に設定する
     */
    public function testUnpublish()
    {
        // データを生成
        ContentFactory::make(['entity_id' => 1, 'plugin' => 'BcBlog', 'type' => 'BlogContent'])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make([])->publish(1, 1)->persist();

        // サービスメソッドを呼ぶ
        $entity = $this->BlogPostsService->unpublish(1);

        // 戻る値を確認
        $this->assertFalse($entity->status);
        $this->assertNull($entity->publish_begin);
        $this->assertNull($entity->publish_end);
    }

    /**
     * ブログ記事を削除する
     */
    public function testDelete()
    {
        //データ 生成
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'news1', '/news/');
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => 1])->persist();

        // //存在しているBlogPostIdを削除
        $result = $this->BlogPostsService->delete(1);
        //戻り値を確認
        $this->assertTrue($result);

        //存在しないBlogPostIdを削除
        $this->expectException(RecordNotFoundException::class);
        $this->BlogPostsService->get(1);
    }

    /**
     * ブログ記事をコピーする
     */
    public function testCopy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * IDからタイトルリストを取得する
     */
    public function testGetTitlesById()
    {
        //データ生成
        BlogPostFactory::make([
            'id' => 1,
            'title' => 'post title 1'
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'title' => 'post title 2'
        ])->persist();

        $result = $this->BlogPostsService->getTitlesById([1, 2]);
        //戻り値を確認
        $this->assertEquals(
            [
                1 => 'post title 1',
                2 => 'post title 2'
            ],
            $result
        );

        //存在しないIDを確認する場合、
        $result = $this->BlogPostsService->getTitlesById([3]);
        //結果はnullに戻る
        $this->assertEmpty($result);
    }

    /**
     * 一括処理
     */
    public function testBatch()
    {
        // データを生成
        BlogContentFactory::make([
            'id' => 5,
            'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
        ])->persist();
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '5', 'title' => 'test blog post batch'])->persist();
        BlogPostFactory::make(['id' => '2', 'blog_content_id' => '5', 'title' => 'test blog post batch'])->persist();
        BlogPostFactory::make(['id' => '3', 'blog_content_id' => '5', 'title' => 'test blog post batch'])->persist();

        //// 正常系のテスト

        // サービスメソッドを呼ぶ
        $result = $this->BlogPostsService->batch('delete', [1, 2, 3]);
        // 戻り値を確認
        $this->assertTrue($result);
        // データが削除されていることを確認
        $blogPosts = $this->BlogPostsService->BlogPosts->find()->where(['blog_content_id' => '5'])->toArray();
        $this->assertCount(0, $blogPosts);

        //// 異常系のテスト

        // delete で id が指定されていない場合は true を返すこと
        // サービスメソッドを呼ぶ
        $result = $this->BlogPostsService->batch('delete', []);
        // 戻り値を確認
        $this->assertTrue($result);

        // 存在しない処理を指定した場合は false を返すこと
        $this->assertFalse($this->BlogPostsService->batch('test', [1, 2, 3]));

        // 存在しない id を指定された場合は例外が発生すること
        // サービスメソッドを呼ぶ
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->BlogPostsService->batch('delete', [1, 2, 3]);
    }

    /**
     * カテゴリ別記事一覧を取得
     */
    public function testGetIndexByCategory()
    {
        // データを生成
        $this->loadFixtureScenario(MultiSiteBlogScenario::class);
        BlogPostFactory::make(['id' => 1, 'blog_category_id' => 1])->persist();
        BlogPostFactory::make(['id' => 2, 'blog_category_id' => 1])->persist();
        BlogPostFactory::make(['id' => 3, 'blog_category_id' => 3])->persist();
        // サービスメソッドを呼ぶ
        // カテゴリreleaseの記事を取得、id昇順
        $result = $this->BlogPostsService->getIndexByCategory('release', [
            'force' => true,
        ]);
        // 戻り値を確認
        // 指定した　カテゴリ で記事を取得できているか
        $this->assertCount(2, $result);
    }

    /**
     * 著者別記事一覧を取得
     */
    public function testGetIndexByAuthor()
    {
        // データを生成
        UserFactory::make(['id' => 2, 'name' => 'test author1'])->persist();
        UserFactory::make(['id' => 3, 'name' => 'test author2'])->persist();
        UserFactory::make(['id' => 4, 'name' => 'test author3'])->persist();
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1', 'user_id' => 2, 'title' => 'blog post1 by author1'])->persist();
        BlogPostFactory::make(['id' => '2', 'blog_content_id' => '1', 'user_id' => 2, 'title' => 'blog post2 by author1'])->persist();
        BlogPostFactory::make(['id' => '3', 'blog_content_id' => '1', 'user_id' => 2, 'title' => 'blog post3 by author1'])->persist();
        BlogPostFactory::make(['id' => '4', 'blog_content_id' => '1', 'user_id' => 3, 'title' => 'blog post1 by author3'])->persist();

        // サービスメソッドを呼ぶ
        // test author1 の記事を取得、id昇順
        $result = $this->BlogPostsService->getIndexByAuthor(2, [
            'direction' => 'ASC',
            'order' => 'id',
        ]);

        // 戻り値を確認
        // 指定した　author で記事を取得できているか
        $this->assertInstanceOf(\Cake\ORM\Query::class, $result);
        $this->assertEquals(3, $result->count());
        $blogPosts = $result->all()->toArray();
        $this->assertEquals('2', $blogPosts[0]->user_id);
        $this->assertEquals('blog post1 by author1', $blogPosts[0]->title);
        $this->assertEquals('2', $blogPosts[1]->user_id);
        $this->assertEquals('blog post2 by author1', $blogPosts[1]->title);
        $this->assertEquals('2', $blogPosts[2]->user_id);
        $this->assertEquals('blog post3 by author1', $blogPosts[2]->title);

        // サービスメソッドを呼ぶ
        // 記事が存在しない
        $result = $this->BlogPostsService->getIndexByAuthor(4, []);

        // 戻り値を確認
        // 指定した author の記事が存在しない
        $this->assertInstanceOf(\Cake\ORM\Query::class, $result);
        $this->assertEquals(0, $result->count());
    }

    /**
     * タグ別記事一覧を取得
     */
    public function testGetIndexByTag()
    {
        // データを生成
        BlogPostFactory::make([])->publish(1,1)->persist();
        BlogPostFactory::make([])->publish(2,1)->persist();

        BlogTagFactory::make(['id' => 1, 'name' => 'test tag1'])->persist();

        BlogPostBlogTagFactory::make(['blog_post_id' => 1, 'blog_tag_id' => 1])->persist();
        BlogPostBlogTagFactory::make(['blog_post_id' => 2, 'blog_tag_id' => 2])->persist();

        // サービスメソッドを呼ぶ
        // test tag1 の記事を取得、id昇順
        $result = $this->BlogPostsService->getIndexByTag('test tag1', []);

        // 戻り値を確認
        // 指定した　tag で記事を取得できているか
        $this->assertCount(1, $result);

        // サービスメソッドを呼ぶ
        // 記事が存在しない
        $result = $this->BlogPostsService->getIndexByTag('test tag0', []);

        // 戻り値を確認
        // 指定した tag の記事が存在しない
        $this->assertCount(0, $result);
    }

    /**
     * 日付別記事一覧を取得
     */
    public function testGetIndexByDate()
    {
        // データを生成
        BlogPostFactory::make([])->byPosted(1, '2022-12-01 00:00:00')->persist();
        BlogPostFactory::make([])->byPosted(2, '2022-12-02 00:00:00')->persist();
        BlogPostFactory::make([])->byPosted(3, '2022-11-02 00:00:00')->persist();

        // サービスメソッドを呼ぶ
        $result = $this->BlogPostsService->getIndexByDate('2022', '12', '01', []);
        // 戻り値を確認
        $this->assertCount(1, $result->toArray());

        // 年と月の値を入れる
        $result = $this->BlogPostsService->getIndexByDate('2022', '12', '', []);
        // 戻り値を確認);
        $this->assertCount(2, $result->toArray());

        // 年だけを入れる
        $result = $this->BlogPostsService->getIndexByDate('2022', '', '', []);
        // 戻り値を確認);
        $this->assertCount(3, $result->toArray());

        // 年月日が指定されていない場合は例外とする
        $this->expectException(\Cake\Http\Exception\NotFoundException::class);
        // サービスメソッドを呼ぶ
        $this->BlogPostsService->getIndexByDate('', '', '', []);
    }

    /**
     * 前の記事を取得する
     */
    public function testGetPrevPost()
    {
        //データ生成
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 3,
            'no' => 100,
            'title' => 'blog post 1',
            'posted' => '2022-10-02 09:00:00',
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

        // 投稿日が年月日時分秒が同一のデータの対応のため、投稿日が同じでIDが大きいデータを検索
        $result = $this->BlogPostsService->getPrevPost(BlogPostFactory::get(2));
        //戻り値を確認
        $this->assertEquals(1, $result->id);
        $this->assertEquals(3, $result->blog_content_id);
        $this->assertEquals("blog post 1", $result->title);

        // 投稿日が新しいデータを取得
        $result = $this->BlogPostsService->getPrevPost(BlogPostFactory::get(1));
        //戻り値を確認
        $this->assertEquals(3, $result->id);
        $this->assertEquals(3, $result->blog_content_id);
        $this->assertEquals("blog post 3", $result->title);

        //テスト posted 最大, 結果はnullに戻る
        $result = $this->BlogPostsService->getPrevPost(BlogPostFactory::get(3));
        //戻り値を確認
        $this->assertNull($result);
    }

    /**
     * 次の記事を取得する
     */
    public function testGetNextPost()
    {
        //データ生成
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 3,
            'no' => 100,
            'title' => 'blog post 1',
            'posted' => '2022-10-02 09:00:00',
            'status' => 0,
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

        //投稿日が年月日時分秒が同一のデータの対応のため、投稿日が同じでIDが小さいデータを検索
        $result = $this->BlogPostsService->getNextPost(BlogPostFactory::get(1));
        //戻り値を確認
        $this->assertEquals(2, $result->id);
        $this->assertEquals(3, $result->blog_content_id);
        $this->assertEquals("blog post 2", $result->title);

        //テスト投稿日が新しいデータを取得
        $result = $this->BlogPostsService->getNextPost(BlogPostFactory::get(3));
        //戻り値を確認
        $this->assertEquals(2, $result->id);
        $this->assertEquals(3, $result->blog_content_id);
        $this->assertEquals("blog post 2", $result->title);

        //テスト status=0, 結果はnullに戻る
        $result = $this->BlogPostsService->getNextPost(BlogPostFactory::get(2));
        //戻り値を確認
        $this->assertNull($result);
    }

    /**
     * test getUrl
     */
    public function test_getUrl()
    {
        $this->loadFixtureScenario(BlogPostsAdminServiceScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);

        //第３引き数が false の場合
        $rs = $this->BlogPostsService->getUrl(ContentFactory::get(100), BlogPostFactory::get(1), false);
        $this->assertEquals('/archives/1', $rs);

        //第３引き数が true の場合
        $rs = $this->BlogPostsService->getUrl(ContentFactory::get(100), BlogPostFactory::get(1), true);
        $this->assertEquals('https://localhost/archives/1', $rs);
    }

}
