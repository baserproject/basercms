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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Test\Factory\BlogPostFactory;

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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 年月日条件を生成する
     */
    public function testCreateYearMonthDayCondition()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 作成者の条件を作成する
     */
    public function testCreateAuthorCondition()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
