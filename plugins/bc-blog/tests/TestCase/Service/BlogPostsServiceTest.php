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

use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Test\Factory\BlogPostFactory;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * BlogPostsServiceTest
 *
 * @property BlogPostsService $BlogPostsService
 */
class BlogPostsServiceTest extends BcTestCase
{

    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BcBlog.Factory/BlogPosts',
    ];

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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $result = $this->BlogPost->find('all', $options);
        if ($type == 'count') {
            $this->assertEquals($expected, count($result));
        } elseif ($type == 'name') {
            $this->assertEquals($expected, $result[0]['BlogPost']['name']);
        } elseif ($type == 'id') {
            $id = Hash::extract($result, '{n}.BlogPost.id');
            $this->assertEquals($expected, $id);
        }
    }

    public function findIndexDataProvider()
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * タグ条件を生成する
     */
    public function testCreateTagCondition()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * キーワード条件を生成する
     */
    public function testCreateKeywordCondition()
    {
        $result = $this->BlogPostsService->createKeywordCondition([], "hello");
        //戻り値を確認
        $this->assertEquals("%hello%", $result['and'][0]['or'][0]['BlogPosts.name LIKE']);
        $this->assertEquals("%hello%", $result['and'][0]['or'][1]['BlogPosts.content LIKE']);
        $this->assertEquals("%hello%", $result['and'][0]['or'][2]['BlogPosts.detail LIKE']);

        //スペースを含むテストデータ
        $result = $this->BlogPostsService->createKeywordCondition([], "hello world");
        //戻り値を確認
        $this->assertEquals("%hello%", $result['and'][0]['or'][0]['BlogPosts.name LIKE']);
        $this->assertEquals("%hello%", $result['and'][0]['or'][1]['BlogPosts.content LIKE']);
        $this->assertEquals("%hello%", $result['and'][0]['or'][2]['BlogPosts.detail LIKE']);
        $this->assertEquals("%world%", $result['and'][1]['or'][0]['BlogPosts.name LIKE']);
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
     * 作成者の条件を作成する
     */
    public function testCreateAuthorCondition()
    {
        //データ　生成
        UserFactory::make(['id' => 1, 'name' => 'test name', 'email' => 'test_name@gmail.com'])->persist();
        //戻り値を確認
        $result = $this->BlogPostsService->createAuthorCondition([], "test name");
        $this->assertEquals($result["BlogPosts.user_id"], 1);
    }

    /**
     * 並び替え設定を生成する
     */
    public function testCreateOrder()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ページ一覧用の検索条件を生成する
     */
    public function testCreateIndexConditions()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 同じタグの関連投稿を取得する
     */
    public function testGetRelatedPosts()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // TODO ucmitz BlogHelperから移植した
        $post = [
            'BlogPost' => [
                'id' => 1,
                'blog_content_id' => 1,
            ],
            'BlogTag' => [
                ['name' => '新製品']
            ]
        ];
        $result = $this->Blog->getRelatedPosts($post);
        $this->assertEquals($result[0]['BlogPost']['id'], 3, '同じタグの関連投稿を正しく取得できません');
        $this->assertEquals($result[1]['BlogPost']['id'], 2, '同じタグの関連投稿を正しく取得できません');

        $post['BlogPost']['id'] = 2;
        $post['BlogPost']['blog_content_id'] = 1;
        $result = $this->Blog->getRelatedPosts($post);
        $this->assertEquals($result[0]['BlogPost']['id'], 3, '同じタグの関連投稿を正しく取得できません');

        $post['BlogPost']['id'] = 7;
        $post['BlogPost']['blog_content_id'] = 2;
        $result = $this->Blog->getRelatedPosts($post);
        $this->assertEmpty($result, '関連していない投稿を取得しています');

        $post['BlogPost']['id'] = 2;
        $post['BlogPost']['blog_content_id'] = 3;
        $result = $this->Blog->getRelatedPosts($post);
        $this->assertEmpty($result, '関連していない投稿を取得しています');
    }

    /**
     * 初期データ用のエンティティを取得
     */
    public function testGetNew()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 新規登録
     */
    public function testCreate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ブログ記事を更新する
     */
    public function testUpdate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 記事を公開状態に設定する
     */
    public function testPublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 記事を非公開状態に設定する
     */
    public function testUnpublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ブログ記事を削除する
     */
    public function testDelete()
    {
        //データ 生成
        BlogPostFactory::make(['id' => '1'])->persist();

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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 一括処理
     */
    public function testBatch()
    {
        // データを生成
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

        // 存在しない id を指定された場合は例外が発生すること
        // サービスメソッドを呼ぶ
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $result = $this->BlogPostsService->batch('delete', [1, 2, 3]);
    }

    /**
     * カテゴリ別記事一覧を取得
     */
    public function testGetIndexByCategory()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $result = $this->BlogPostsService->getIndexByAuthor('test author1', [
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
        $result = $this->BlogPostsService->getIndexByAuthor('test author3', []);

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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 日付別記事一覧を取得
     */
    public function testGetIndexByDate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 前の記事を取得する
     */
    public function testGetPrevPost()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 次の記事を取得する
     */
    public function testGetNextPost()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
