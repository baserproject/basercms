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

namespace BcBlog\Test\TestCase\Model;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Table\BlogPostsTable;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use Cake\Filesystem\Folder;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogPostsTableTest
 *
 * @property BlogPostsTable $BlogPostsTable
 */
class BlogPostsTableTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    public $fixtures = [
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogContents',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
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
        $this->BlogPostsTable = $this->getTableLocator()->get('BcBlog.BlogPosts');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogPostsTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('blog_posts', $this->BlogPostsTable->getTable());
        $this->assertTrue($this->BlogPostsTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->BlogPostsTable->hasBehavior('BcUpload'));
        $this->assertTrue($this->BlogPostsTable->hasAssociation('BlogTags'));
        $this->assertTrue($this->BlogPostsTable->hasAssociation('BlogComments'));
        $this->assertTrue($this->BlogPostsTable->hasAssociation('BlogCategories'));
        $this->assertTrue($this->BlogPostsTable->hasAssociation('BlogContents'));
        $this->assertTrue($this->BlogPostsTable->hasAssociation('Users'));
        $this->assertTrue($this->BlogPostsTable->hasAssociation('Users'));
    }

    /*
	 * validate
	 */
    public function test必須チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogPost->create([
            'BlogPost' => ['blog_content_id' => 1]
        ]);

        $this->assertFalse($this->BlogPost->validates());

        $this->assertArrayHasKey('name', $this->BlogPost->validationErrors);
        $this->assertEquals('タイトルを入力してください。', current($this->BlogPost->validationErrors['name']));

        $this->assertArrayHasKey('posts_date', $this->BlogPost->validationErrors);
        $this->assertEquals('投稿日を入力してください。', current($this->BlogPost->validationErrors['posts_date']));
    }

    public function test空チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogPost->create([
            'BlogPost' => [
                'user_id' => '',
                'blog_content_id' => 1
            ]
        ]);

        $this->assertFalse($this->BlogPost->validates());

        $this->assertArrayHasKey('user_id', $this->BlogPost->validationErrors);
        $this->assertEquals('投稿者を選択してください。', current($this->BlogPost->validationErrors['user_id']));
    }

    public function test桁数チェック異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogPost->create([
            'BlogPost' => [
                'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
                'blog_content_id' => 1
            ]
        ]);

        $this->assertFalse($this->BlogPost->validates());

        $this->assertArrayHasKey('name', $this->BlogPost->validationErrors);
        $this->assertEquals('タイトルは255文字以内で入力してください。', current($this->BlogPost->validationErrors['name']));
    }

    public function test桁数チェック正常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogPost->create([
            'BlogPost' => [
                'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
                'posts_date' => '2020-01-27 12:57:59',
                'blog_content_id' => 1
            ]
        ]);
        $this->assertTrue($this->BlogPost->validates());
    }

    public function testその他異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // 形式チェック
        $this->BlogPost->create([
            'BlogPost' => [
                'publish_begin' => 'test',
                'publish_end' => 'test',
                'posts_date' => 'test',
                'blog_content_id' => 1
            ]
        ]);

        $this->assertFalse($this->BlogPost->validates());

        $this->assertArrayHasKey('publish_begin', $this->BlogPost->validationErrors);
        $this->assertEquals('公開開始日の形式が不正です。', current($this->BlogPost->validationErrors['publish_begin']));

        $this->assertArrayHasKey('publish_end', $this->BlogPost->validationErrors);
        $this->assertEquals('公開終了日の形式が不正です。', current($this->BlogPost->validationErrors['publish_end']));

        $this->assertArrayHasKey('posts_date', $this->BlogPost->validationErrors);
        $this->assertEquals('投稿日の形式が不正です。', current($this->BlogPost->validationErrors['posts_date']));

        // データ量チェック
        $bigData = 'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほ==100Byte.';

        // 64000Byte以上のデータを生成
        for ($i = 0; $i < 2; $i++) {
            $bigData .= $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData . $bigData;
        }

        $this->BlogPost->create([
            'BlogPost' => [
                'detail' => $bigData,
                'detail_draft' => $bigData,
                'blog_content_id' => 1
            ]
        ]);

        $this->assertFalse($this->BlogPost->validates());

        $this->assertArrayHasKey('detail', $this->BlogPost->validationErrors);
        $this->assertEquals('本稿欄に保存できるデータ量を超えています。', current($this->BlogPost->validationErrors['detail']));

        $this->assertArrayHasKey('detail_draft', $this->BlogPost->validationErrors);
        $this->assertEquals('草稿欄に保存できるデータ量を超えています。', current($this->BlogPost->validationErrors['detail_draft']));
    }

    public function testその他正常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // 形式チェック
        $this->BlogPost->create([
            'BlogPost' => [
                'publish_begin' => '2020-01-27 12:57:59',
                'publish_end' => '2020-01-29 12:57:59',
                'posts_date' => '2020-01-27 12:57:59',
                'blog_content_id' => 1
            ]
        ]);

        $this->BlogPost->validates();
        $this->assertArrayNotHasKey('publish_begin', $this->BlogPost->validationErrors);
        $this->assertArrayNotHasKey('publish_end', $this->BlogPost->validationErrors);
        $this->assertArrayNotHasKey('posts_date', $this->BlogPost->validationErrors);

        // データ量チェック
        $this->BlogPost->create([
            'BlogPost' => [
                'detail' => 'test',
                'detail_draft' => 'test',
                'blog_content_id' => 1
            ]
        ]);

        $this->BlogPost->validates();
        $this->assertArrayNotHasKey('detail', $this->BlogPost->validationErrors);
        $this->assertArrayNotHasKey('detail_draft', $this->BlogPost->validationErrors);
    }

    /**
     * アップロードビヘイビアの設定
     */
    public function testSetupUpload()
    {
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
            'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
            'use_content' => '1',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ])->persist();
        BlogPostFactory::make([
            'id' => '1',
            'blog_content_id' => '1',
            'no' => '1',
            'name' => 'name1',
            'content' => 'content1',
            'blog_category_id' => '1',
            'user_id' => '1',
            'status' => '1',
            'posts_date' => '2017-02-01 12:57:59',
            'content_draft' => '',
            'detail_draft' => '',
            'publish_begin' => null,
            'publish_end' => null,
            'exclude_search' => 0,
            'eye_catch' => '',
            'created' => '2017-02-01 12:57:59',
            'modified' => '2016-01-02 12:57:59'
        ])->persist();
        $this->BlogPostsTable->setupUpload(1);
        $result = $this->BlogPostsTable->getBehavior('BcUpload')->BcFileUploader["BlogPosts"];
        $this->assertEquals($result->settings["saveDir"], 'blog/1/blog_posts');
        $this->assertEquals($result->settings["fields"]["eye_catch"]["type"], 'image');
        $this->assertEquals($result->settings["fields"]["eye_catch"]["name"], 'eye_catch');
    }

    /**
     * ブログの月別一覧を取得する
     *
     * @param int $blogContentId ブログコンテンツID
     * @param array $options オプション
     * @param array $expected 期待値
     * @dataProvider getPostedDatesDataProvider
     */
    public function testGetPostedDates($blogContentId, $options, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->BlogPost->getPostedDates($blogContentId, $options);
        $this->assertEquals($expected, $result, '正しくブログの月別一覧を取得できません');
    }

    public function getPostedDatesDataProvider()
    {
        return [
            [1, [], [['year' => '2016', 'month' => '02'], ['year' => '2015', 'month' => '01']]],
            [2, [], [['year' => '2016', 'month' => '02']]],
            [1, ['category' => true], [
                ['year' => '2016', 'month' => '02', 'BlogCategory' => ['id' => null, 'name' => null, 'title' => null]],
                ['year' => '2016', 'month' => '02', 'BlogCategory' => ['id' => '2', 'name' => 'child', 'title' => '子カテゴリ']],
                ['year' => '2015', 'month' => '01', 'BlogCategory' => ['id' => '2', 'name' => 'child', 'title' => '子カテゴリ']],
                ['year' => '2015', 'month' => '01', 'BlogCategory' => ['id' => '1', 'name' => 'release', 'title' => 'プレスリリース']],
            ]],
            [1, ['viewCount' => true, 'type' => 'year'], [
                ['year' => '2016', 'count' => 2],
                ['year' => '2015', 'count' => 2]
            ]],
        ];
    }

    /**
     * カレンダー用に指定した月で記事の投稿がある日付のリストを取得する
     *
     * @param int $blogContentId ブログコンテンツID
     * @param int $year 年
     * @param int $month 月
     * @param array $expected 期待値
     * @dataProvider getEntryDatesDataProvider
     */
    public function testGetEntryDates($blogContentId, $year, $month, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $datasource = $datasource = $this->BlogPost->getDataSource()->config['datasource'];
        if ($datasource === 'Database/BcSqlite') {
            $this->markTestIncomplete('このテストは、まだ実装されていません。');
        }

        $result = $this->BlogPost->getEntryDates($blogContentId, $year, $month);
        $this->assertEquals($expected, $result, '正しく日付リストを取得できません');
    }

    public function getEntryDatesDataProvider()
    {
        return [
            [1, 2015, 1, ['2015-01-27', '2015-01-27']],
            [1, 2016, 1, []],
            [2, 2016, 2, ['2016-02-10', '2016-02-10']],
        ];
    }

    /**
     * 投稿者の一覧を取得する
     */
    public function testGetAuthors()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $message = '投稿者一覧を正しく取得できません';
        $result = $this->BlogPost->getAuthors(1, []);
        $this->assertEquals($result[0]['User']['name'], 'basertest', $message);
        $this->assertEquals($result[1]['User']['name'], 'basertest2', $message);

        $result = $this->BlogPost->getAuthors(2, []);
        $this->assertEquals($result[0]['User']['name'], 'basertest', $message);

        $result = $this->BlogPost->getAuthors(2, ['viewCount' => true]);
        $this->assertEquals($result[0]['count'], 2, $message);
    }

    /**
     * 指定した月の記事が存在するかチェックする
     */
    public function testExistsEntry()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $datasource = $datasource = $this->BlogPost->getDataSource()->config['datasource'];
        if ($datasource === 'Database/BcSqlite') {
            $this->markTestIncomplete('このテストは、まだ実装されていません。');
        }
        $result = $this->BlogPost->existsEntry(1, 2015, 1);
        $this->assertTrue($result);

        $result = $this->BlogPost->existsEntry(1, 2016, 1);
        $this->assertFalse($result);

        $result = $this->BlogPost->existsEntry(2, 2015, 1);
        $this->assertFalse($result);

        $result = $this->BlogPost->existsEntry(2, 2016, 2);
        $this->assertTrue($result);
    }

    /**
     * コントロールソースを取得する
     *
     * @param array $options オプション
     * @param array $expected 期待値
     * @dataProvider getControlSourceDataProvider
     */
    public function testGetControlSource($options, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->BlogPost->getControlSource('blog_category_id', $options);
        $this->assertEquals($expected, $result, '正しくコントロールソースを取得できません');
    }

    public function getControlSourceDataProvider()
    {
        return [
            [['blogContentId' => 1], [1 => 'プレスリリース', 2 => '　　　└子カテゴリ', 3 => '親子関係なしカテゴリ']],
            [['blogContentId' => 2], [4 => 'プレスリリース']]
        ];
    }

    /**
     * 公開状態を取得する
     *
     * @dataProvider allowPublishDataProvider
     */
    public function testAllowPublish($publish_begin, $publish_end, $status, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $data['publish_begin'] = $publish_begin;
        $data['publish_end'] = $publish_end;
        $data['status'] = $status;
        $this->assertEquals($this->BlogPost->allowPublish($data), $expected);
    }

    public function allowPublishDataProvider()
    {
        return [
            [null, null, false, false],
            [null, null, true, true],
            [null, date('Y-m-d H:i:s'), true, false],
            [null, date('Y-m-d H:i:s', strtotime("+1 hour")), true, true],
            [date('Y-m-d H:i:s'), null, true, true],
            [date('Y-m-d H:i:s', strtotime("+1 hour")), null, true, false],
            [date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), true, false]
        ];
    }

    /**
     * 公開状態の記事を取得する
     */
    public function testGetPublishes()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $message = '正しく公開状態の記事を取得できません';

        $result = count($this->BlogPost->getPublishes([]));
        $this->assertEquals($result, 6, $message);

        $options = ['conditions' => [
            'publish_begin' => '9000-01-27 12:00:00'
        ]];
        $result = $this->BlogPost->getPublishes($options);
        $this->assertEmpty($result);
    }

    /**
     * afterSave
     */
    public function testAfterSave()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $data = ['BlogPost' => [
            'id' => 99,
            'no' => 99,
            'exclude_search' => 0,
            'name' => 'test-name',
            'blog_content_id' => 1,
            'blog_category_id' => null,
            'posts_date' => '2020-01-27 12:57:59',
            'content' => 'test-content',
            'detail' => 'test-detail',
            'status' => 0,
            'publish_begin' => '2020-01-27 12:57:59',
            'publish_end' => '2020-01-28 12:57:59',
        ]];

        $SearchIndex = ClassRegistry::init('SearchIndex');

        // 登録
        $data['BlogPost']['exclude_search'] = false;
        $this->BlogPost->create($data);
        $this->BlogPost->save();

        $result = $SearchIndex->find('count', [
            'conditions' => ['SearchIndex.title' => 'test-name'],
        ]);
        $this->assertEquals($result, 1, '検索用テーブルへ登録できません');

        // 削除
        $data['BlogPost']['exclude_search'] = true;
        $this->BlogPost->create($data);
        $this->BlogPost->save();

        $result = $SearchIndex->find('count', [
            'conditions' => ['SearchIndex.title' => 'test-name'],
        ]);
        $this->assertEquals($result, 0, '検索用テーブルから削除できません');

        unset($SearchIndex);
    }


    /**
     * 検索用データを生成する
     */
    public function testCreateSearchIndex()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // 戻り値の型チェック
        $data = [
            'BlogPost' => [
                'blog_content_id' => 1,
                'no' => 1,
                'name' => 'test-name',
                'content' => 'test-content',
                'detail' => 'test-detail',
                'status' => true,
                'publish_begin' => '',
                'publish_end' => ''
            ]
        ];
        $expected = [
            'SearchIndex' => [
                'type' => 'ブログ',
                'model_id' => false,
                'site_id' => '1',
                'title' => 'test-name',
                'detail' => 'test-content test-detail',
                'url' => '/news/archives/1',
                'status' => true,
                'content_filter_id' => '',
                'content_id' => '4',
                'publish_begin' => null,
                'publish_end' => null
            ]
        ];
        $result = $this->BlogPost->createSearchIndex($data);
        $this->assertEquals($expected, $result, 'ブログ記事用の検索用データを正しく生成できません');
    }

    /**
     * 検索用データ生成、ステータス設定
     * @dataProvider createSearchIndexStatusDataProvider
     */
    public function testCreateSearchIndexStatus($blogPostStatus, $contentStatus, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $blogContentId = 1;
        $blogPost = [
            'BlogPost' => [
                'blog_content_id' => $blogContentId,
                'no' => 1,
                'name' => 'test-name',
                'content' => 'test-content',
                'detail' => 'test-detail',
                'publish_begin' => '',
                'publish_end' => '',
                'status' => $blogPostStatus
            ]
        ];
        $data = $this->BlogPost->BlogContent->find('first', ['conditions' => ['BlogContent.id' => $blogContentId]]);
        $data['Content']['self_status'] = $contentStatus;
        $this->BlogPost->BlogContent->Content->save($data);
        $result = $this->BlogPost->createSearchIndex($blogPost);
        $this->assertEquals($expected, $result['SearchIndex']['status'], 'ブログ記事用の検索用データを正しく生成できません');
    }

    public function createSearchIndexStatusDataProvider()
    {
        return [
            [true, true, true],
            [true, false, false],
            [false, true, false],
            [false, false, false],
        ];
    }

    /**
     * 検索用データ生成、公開期間設定
     * @dataProvider createSearchIndexPublishDataProvider
     */
    public function testCreateSearchIndexPublish($blogPostPublish, $contentPublish, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $blogContentId = 1;
        $blogPost = [
            'BlogPost' => [
                'blog_content_id' => $blogContentId,
                'no' => 1,
                'name' => 'test-name',
                'content' => 'test-content',
                'detail' => 'test-detail',
                'publish_begin' => $blogPostPublish['begin'],
                'publish_end' => $blogPostPublish['end'],
                'status' => true
            ]
        ];
        $data = $this->BlogPost->BlogContent->find('first', ['conditions' => ['BlogContent.id' => $blogContentId]]);
        $data['Content']['self_publish_begin'] = $contentPublish['begin'];
        $data['Content']['self_publish_end'] = $contentPublish['end'];
        $this->BlogPost->BlogContent->Content->save($data);
        $result = $this->BlogPost->createSearchIndex($blogPost);
        $this->assertEquals($expected, [
            'begin' => ($result['SearchIndex']['publish_begin']) ? date('Y-m-d', strtotime($result['SearchIndex']['publish_begin'])) : '',
            'end' => ($result['SearchIndex']['publish_end']) ? date('Y-m-d', strtotime($result['SearchIndex']['publish_end'])) : ''
        ], 'ブログ記事用の検索用データを正しく生成できません');
    }

    public function createSearchIndexPublishDataProvider()
    {
        return [
            [['begin' => '', 'end' => ''], ['begin' => '', 'end' => ''], ['begin' => '', 'end' => '']],
            [['begin' => '2020-09-01', 'end' => '2020-09-30'], ['begin' => '', 'end' => ''], ['begin' => '2020-09-01', 'end' => '2020-09-30']],    // 記事に設定
            [['begin' => '', 'end' => ''], ['begin' => '2020-09-01', 'end' => '2020-09-30'], ['begin' => '2020-09-01', 'end' => '2020-09-30']],    // コンテンツに設定
            [['begin' => '2020-08-01', 'end' => ''], ['begin' => '2020-09-01', 'end' => ''], ['begin' => '2020-09-01', 'end' => '']],    // 記事の開始日が早い
            [['begin' => '2020-10-01', 'end' => ''], ['begin' => '2020-09-01', 'end' => ''], ['begin' => '2020-10-01', 'end' => '']],    // 記事の開始日が遅い
            [['begin' => '', 'end' => '2020-08-30'], ['begin' => '', 'end' => '2020-09-30'], ['begin' => '', 'end' => '2020-08-30']],    // 記事の終了日が早い
            [['begin' => '', 'end' => '2020-10-30'], ['begin' => '', 'end' => '2020-09-30'], ['begin' => '', 'end' => '2020-09-30']],    // 記事の終了日が遅い
            [['begin' => '2020-10-30', 'end' => ''], ['begin' => '', 'end' => '2020-09-30'], ['begin' => '2020-09-30', 'end' => '2020-09-30']],    // 記事の開始日がコンテンツの終了日より遅い
            [['begin' => '', 'end' => '2020-8-30'], ['begin' => '2020-09-01', 'end' => ''], ['begin' => '2020-09-01', 'end' => '2020-09-01']],    // 記事の終了日がコンテンツの開始日より早い
        ];
    }

    /*
	 * beforeFind
	 */
    public function testBeforeFind()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コピーする
     *
     * @param int $id
     * @param array $data
     */
    public function testCopy()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogPost->copy(1);
        $result = $this->BlogPost->find('first', [
            'conditions' => ['BlogPost.id' => $this->BlogPost->getLastInsertID()]
        ]);
        $this->assertEquals($result['BlogPost']['name'], 'ホームページをオープンしました_copy');
        $this->assertEquals(date('Y/m/d', strtotime($result['BlogPost']['posts_date'])), date('Y/m/d'));
    }

    /**
     * プレビュー用のデータを生成する
     */
    public function testCreatePreviewData()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

	/**
	 * アイキャッチアップロード
	 */
	public function testCopyEyeCatch()
	{
		if (is_dir(WWW_ROOT . '/files/blog/999')) {
			$folder = new Folder();
			$folder->delete(WWW_ROOT . '/files/blog/999');
		}
		copy(__DIR__ . '/../../Fixture/File/test1.png', __DIR__ . '/../../Fixture/File/test1_.png');
		$this->loadFixtureScenario(InitAppScenario::class);
		BlogContentFactory::make()->forCopyEyeCatch()->persist();

		$this->loginAdmin($this->getRequest());

		$this->BlogPostsTable->setupUpload(999);

		$data = [
		    'no' => 1,
			'name' => 'test-name',
			'blog_content_id' => 999,
			'posts_date' => '2022-07-16 00:00:00',
			'content' => 'test-content',
			'detail' => 'test-detail',
			'status' => 0,
			'publish_begin' => null,
			'publish_end' => null,
			'user_id' => 1,
			'eye_catch' => [
				'name' => 'test.png',
				'type' => 'image/png',
				'tmp_name' => __DIR__ . '/../../Fixture/File/test1_.png',
				'error' => 0,
				'size' => 1,
			],
		];

		// 作成
		$blogPost1 = $this->BlogPostsTable->save($this->BlogPostsTable->newEntity($data));
		$blogPost1no = $blogPost1->no;
		$ym = date('Y/m');
		$fileDir = WWW_ROOT . '/files/blog/999/blog_posts/' . $ym;

		$this->assertEquals($ym . '/0000000' . $blogPost1no .  '_eye_catch.png', $blogPost1->eye_catch);
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch__thumb.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch__mobile_thumb.png'));

		// コピー
		$blogPost2 = $this->BlogPostsTable->copy(null, clone $blogPost1);
		$blogPost2no = $blogPost2->no;

		// 複製元が影響を受けていないか
		$this->assertEquals($ym . '/0000000' . $blogPost1no .  '_eye_catch.png', $blogPost1->eye_catch);
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch__thumb.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch__mobile_thumb.png'));

		// 複製できているか
		$this->assertEquals($ym . '/0000000' . $blogPost2no .  '_eye_catch.png', $blogPost2->eye_catch);
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch__thumb.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch__mobile_thumb.png'));

		// 削除
		$this->BlogPostsTable->delete($blogPost2);
		$blogPost2 = $this->BlogPostsTable->find()->where([
            'BlogPosts.id' => $blogPost2->id
		])->first();
		$this->assertEmpty($blogPost2);
		$this->assertFalse(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch.png'));
		$this->assertFalse(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch__thumb.png'));
		$this->assertFalse(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch__mobile_thumb.png'));

		$dir = new Folder(WWW_ROOT . '/files/blog/999');
		$dir->delete();
	}

}
