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

use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use BcBlog\Model\Table\BlogPostsTable;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\MultiSiteBlogPostScenario;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use ArrayObject;
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

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
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

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $validator = $this->BlogPostsTable->getValidator('default');
        //入力フィールドのデータが超えた場合、
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'title' => str_repeat('a', 256),
            'detail' => str_repeat('a', 16777219),
            'detail_draft' => str_repeat('a', 16777219),
        ]);
        //戻り値を確認
        $this->assertEquals('スラッグは255文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('タイトルは255文字以内で入力してください。', current($errors['title']));
        $this->assertEquals('本稿欄に保存できるデータ量を超えています。', current($errors['detail']));
        $this->assertEquals('草稿欄に保存できるデータ量を超えています。', current($errors['detail_draft']));
    }

    /*
	 * 必須チェック
	 */
    public function test_validationDefault_testIsNull()
    {
        $validator = $this->BlogPostsTable->getValidator('default');
        $errors = $validator->validate([
            'title' => null,
            'user_id' => null,
            'posted' => null,
        ]);

        //戻り値を確認
        $this->assertEquals('タイトルを入力してください。', current($errors['title']));
        $this->assertEquals('投稿日を入力してください。', current($errors['posted']));
        $this->assertEquals('投稿者を選択してください。', current($errors['user_id']));

        $errors = $validator->validate([]);

        //戻り値を確認
        $this->assertEquals('タイトルを入力してください。', current($errors['title']));
        $this->assertEquals('投稿日を入力してください。', current($errors['posted']));
        $this->assertEquals('投稿者を選択してください。', current($errors['user_id']));
    }

    /*
     * 重複をチェック
     */
    public function test_validationDefault_testDuplicate()
    {
        $validator = $this->BlogPostsTable->getValidator('default');
        BlogPostFactory::make(['name' => 'test'])->persist();
        $errors = $validator->validate([
            'name' => 'test'
        ]);

        //戻り値を確認
        $this->assertEquals('既に登録のあるスラッグです。', current($errors['name']));
    }

    /*
     * 許可されない値を指定した場合、
     */
    public function test_validationDefault_testNotAllow()
    {
        $validator = $this->BlogPostsTable->getValidator('default');
        BlogPostFactory::make(['name' => 'test'])->persist();
        $errors = $validator->validate([
            'name' => '1',                              //スラッグ
            'content' => '<?php echo $test; ?>',       //概要欄
            'detail' => '<?php echo $test; ?>',         //本稿欄
            'detail_draft' => '<?php echo $test; ?>',   //草稿欄
            'publish_begin' => '2022-02-29',            //公開開始日
            'publish_end' => '2022-02-29',              //公開終了日
            'posted' => '2022-02-29',                   //投稿日
            'eye_catch' => 'a.aa',                   //アイキャッチ画像
        ]);

        //戻り値を確認
        //スラッグ
        $this->assertEquals('数値だけのスラッグを登録することはできません。', current($errors['name']));
        //概要欄
        $this->assertEquals('概要欄でスクリプトの入力は許可されていません。', current($errors['content']));
        //本稿欄
        $this->assertEquals('本稿欄でスクリプトの入力は許可されていません。', current($errors['detail']));
        //草稿欄
        $this->assertEquals('草稿欄でスクリプトの入力は許可されていません。', current($errors['detail_draft']));
        //公開開始日
        $this->assertEquals('公開開始日の形式が不正です。', $errors['publish_begin']['dateTime']);
        $this->assertEquals('公開期間が不正です。', $errors['publish_begin']['checkDateRange']);
        //公開終了日
        $this->assertEquals('公開終了日の形式が不正です。', $errors['publish_end']['dateTime']);
        //投稿日
        $this->assertEquals('投稿日の形式が不正です。', $errors['posted']['dateTime']);
        //アイキャッチ画像
        $this->assertEquals('許可されていないファイルです。', $errors['eye_catch']['fileExt']);
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
        //データを生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        //対象メソッドをコール
        $result = $this->BlogPostsTable->getPostedDates($blogContentId, $options);

        //戻り値を確認
        if (isset($options['category']) && $options['category']) {
            $expected['201501-6']['category'] = BlogCategoryFactory::get(6);
        }
        $this->assertEquals($expected, $result);
    }

    public static function getPostedDatesDataProvider()
    {
        return [
            [6, [], [
                '201503' => ['year' => '2015', 'month' => '03', 'count' => null],
                '201502' => ['year' => '2015', 'month' => '02', 'count' => null],
                '201501' => ['year' => '2015', 'month' => '01', 'count' => null],
            ]],
            [7, [], ['201501' => ['year' => '2015', 'month' => '01', 'count' => null]]],
            [6, ['category' => true], [
                '201503' => ['year' => '2015', 'month' => '03', 'count' => null],
                '201502' => ['year' => '2015', 'month' => '02', 'count' => null],
                '201501-6' => ['year' => '2015', 'month' => '01', 'count' => null], // カテゴリはテスト中に挿入
            ]],
            [6, ['viewCount' => true, 'type' => 'year'], ['2015' => ['year' => '2015', 'month' => null, 'count' => 3]]],
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
        // データ生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);
        $result = $this->BlogPostsTable->getEntryDates($blogContentId, $year, $month);
        $this->assertEquals($expected, $result);
    }

    public static function getEntryDatesDataProvider()
    {
        return [
            [6, 2015, 1, ['2015-01-27']],
            [6, 2016, 1, []],
            [7, 2015, 1, ['2015-01-27']],
        ];
    }

    /**
     * 投稿者の一覧を取得する
     */
    public function testGetAuthors()
    {
        //データを生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);
        UserFactory::make([
            'id' => BlogPostFactory::get(1)->user_id,
            'name' => 'name_test',
            'real_name_1' => 'real_name_1_test',
            'real_name_2' => 'real_name_2_test',
            'nickname' => 'nickname_test',
        ])->persist();

        $result = $this->BlogPostsTable->getAuthors(6, []);
        $this->assertEquals($result[0]->name, 'name_test');
        $this->assertEquals($result[0]->real_name_1, 'real_name_1_test');
        $this->assertEquals($result[0]->real_name_2, 'real_name_2_test');
        $this->assertEquals($result[0]->nickname, 'nickname_test');

        $result = $this->BlogPostsTable->getAuthors(6, ['viewCount' => true]);
        $this->assertEquals($result[0]->count, 1);
    }

    /**
     * 指定した月の記事が存在するかチェックする
     */
    public function testExistsEntry()
    {
        // データ生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        $result = $this->BlogPostsTable->existsEntry(6, 2015, 1);
        $this->assertTrue($result);

        $result = $this->BlogPostsTable->existsEntry(6, 2016, 1);
        $this->assertFalse($result);

        $result = $this->BlogPostsTable->existsEntry(7, 2015, 2);
        $this->assertFalse($result);

        $result = $this->BlogPostsTable->existsEntry(7, 2015, 1);
        $this->assertTrue($result);
    }

    /**
     * test _getEntryDatesConditions
     * @param $blogContentId
     * @param $year
     * @param $month
     * @param $expectYear
     * @param $expertMonth
     * @dataProvider _getEntryDatesConditionsProvider
     */
    public function test_getEntryDatesConditions($blogContentId, $year, $month, $expectYear, $expertMonth)
    {
        $rs = $this->execPrivateMethod($this->BlogPostsTable, '_getEntryDatesConditions', [$blogContentId, $year, $month]);
        //戻る値を確認
        $this->assertEquals($rs["YEAR(`BlogPosts`.`posted`)"], $expectYear);
        $this->assertEquals($rs["MONTH(`BlogPosts`.`posted`)"], $expertMonth);
        $this->assertTrue($rs["BlogPosts.status"]);
        $this->assertEquals($rs["BlogPosts.blog_content_id"], 1);
    }

    public static function _getEntryDatesConditionsProvider()
    {
        return [
            [1, 2027, 1, 2027, 1],      //日付を設定する場合、
            [1, null, null, date('Y'), date('m')],   //日付を設定していない場合、
        ];
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

    public static function getControlSourceDataProvider()
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
    public function testAllowPublish($publishBegin, $publishEnd, $status, $expected)
    {
        $post = $this->BlogPostsTable->newEntity([
            'publish_begin' => $publishBegin,
            'publish_end' => $publishEnd,
            'status' => $status,
        ]);
        $this->assertEquals($this->BlogPostsTable->allowPublish($post), $expected);
    }

    public static function allowPublishDataProvider()
    {
        return [
            [null, null, false, false],
            [null, null, true, true],

            [null, new \Cake\I18n\DateTime('+1 hour'), true, true],
            [new \Cake\I18n\DateTime('-1 hour'), null, true, true],
            [null, new \Cake\I18n\DateTime('-1 hour'), true, false],
            [new \Cake\I18n\DateTime('+1 hour'), null, true, false],

            [new \Cake\I18n\DateTime('-1 hour'), new \Cake\I18n\DateTime('+1 hour'), true, true],
            [new \Cake\I18n\DateTime('-1 hour'), new \Cake\I18n\DateTime('+1 hour'), false, false],
            [new \Cake\I18n\DateTime('-1 hour'), new \Cake\I18n\DateTime('-1 hour'), true, false],
            [new \Cake\I18n\DateTime('+1 hour'), new \Cake\I18n\DateTime('-1 hour'), true, false],
            [new \Cake\I18n\DateTime('+1 hour'), new \Cake\I18n\DateTime('+2 hour'), true, false],
        ];
    }

    /**
     * 公開状態の記事を取得する
     */
    public function testGetPublishes()
    {
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        $result = $this->BlogPostsTable->getPublishes([]);
        $this->assertCount(8, $result);

        $options = ['conditions' => [
            'publish_begin' => '9000-01-27 12:00:00'
        ]];
        $result = $this->BlogPostsTable->getPublishes($options);
        $this->assertCount(0, $result);
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

    public static function createSearchIndexStatusDataProvider()
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

    public static function createSearchIndexPublishDataProvider()
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
     */
    public function testCopy()
    {
        //データを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest());
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 6,
            'no' => 3,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
        ])->persist();

        //コピーメソッドを呼ぶ
        $result = $this->BlogPostsTable->copy(1);
        //戻る値を確認
        $this->assertEquals($result->name, 'release_copy');
        $this->assertEquals($result->title, 'プレスリリース_copy');
        $this->assertFalse($result->status);
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
		if (is_dir(WWW_ROOT . '/files/blog/999')) {
			$folder = new BcFolder(WWW_ROOT . '/files/blog/999');
			$folder->delete();
		}
		copy(__DIR__ . '/../../Images/File/test1.png', __DIR__ . '/../../Images/File/test1_.png');
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
				'tmp_name' => __DIR__ . '/../../Images/File/test1_.png',
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

		$dir = new BcFolder(WWW_ROOT . '/files/blog/999');
		$dir->delete();
	}

    /**
     * test getPublishByNo
     */
    public function test_getPublishByNo()
    {
        //データを生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);
        UserFactory::make(['id' => BlogPostFactory::get(1)->user_id])->persist();
        //$no=数値＆$preview=True
        $rs = $this->BlogPostsTable->getPublishByNo(6, 3, true);

        //戻る値を確認
        $this->assertEquals($rs->no, 3);
        $this->assertEquals($rs->blog_content_id, 6);
        $this->assertEquals($rs->title, 'プレスリリース');
        $this->assertNotNull($rs->blog_comments);
        $this->assertNotNull($rs->blog_tags);
        $this->assertNotNull($rs->user);
        $this->assertNotNull($rs->blog_content);
        $this->assertNotNull($rs->blog_content->content);
        $this->assertNotNull($rs->blog_content->content->site);

        //$no=文字列＆$preview=false
        $rs = $this->BlogPostsTable->getPublishByNo(7, 'smartphone_release', false);
        //戻る値を確認
        $this->assertEquals($rs->no, 4);
        $this->assertEquals($rs->title, 'スマホサイトリリース');

        // 日本語スラッグ
        $rs = $this->BlogPostsTable->getPublishByNo(6, '日本語スラッグ', true);
        $this->assertEquals($rs->title, '日本語スラッグ記事タイトル');
        $rs = $this->BlogPostsTable->getPublishByNo(6, '日本語スラッグ', false);
        $this->assertEquals($rs->title, '日本語スラッグ記事タイトル');

        //サービスクラス
        $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
        //非公開を設定する
        $blogPostsService->unpublish(1);

        //preview が true の場合に取得できる
        $rs = $this->BlogPostsTable->getPublishByNo(6, 3, true);
        $this->assertEquals($rs->title, 'プレスリリース');

        //preview が false の場合に取得できない
        $rs = $this->BlogPostsTable->getPublishByNo(6, 3);
        $this->assertNull($rs);
    }

    /**
     * beforeSave
     * @return void
     */
    public function test_beforeSave()
    {
        //サービスクラス
        $PluginsService = $this->getService(PluginsServiceInterface::class);
        $BlogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $PluginsService->attach('BcSearchIndex');

        //データを生成
        $this->loadFixtureScenario(MultiSiteBlogPostScenario::class);

        $blogPost = $BlogPostsService->get(1);
        $blogPost->exclude_search = 1;
        $this->BlogPostsTable->beforeSave(new Event("beforeSave"), $blogPost, new ArrayObject());
        $this->assertTrue($this->BlogPostsTable->isExcluded());

        //set isExcluded true
        BlogContentFactory::make(['id' => 11])->persist();
        ContentFactory::make([
            'id' => 11,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 11,
            'exclude_search' => 1,
        ])->persist();
//        BlogPostFactory::make(['id' => 8, 'blog_content_id' => 11])->persist();

        $blogPost = $BlogPostsService->get(8);
        $this->BlogPostsTable->beforeSave(new Event("beforeSave"), $blogPost, new ArrayObject());
        $this->assertTrue($this->BlogPostsTable->isExcluded());
    }
}
