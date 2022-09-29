<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case.Model
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BlogPost', 'Blog.Model');
App::uses('Folder', 'Utility');

/**
 * Class BlogPostTest
 *
 * @property BlogPost $BlogPost
 */
class BlogPostTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.User',
		'baser.Default.SearchIndex',
		'baser.Default.SiteConfig',
		'baser.Default.BlogTag',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
		'baser.Default.Site',
		'plugin.blog.Model/BlogPost/ContentBlogPost',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'plugin.blog.Model/BlogPost/BlogPostModel',
		'plugin.blog.Model/BlogPost/BlogCategoryModel',
		'plugin.blog.Model/BlogPost/BlogPostsBlogTagModel',
	];

	public function setUp()
	{
		$this->BlogPost = ClassRegistry::init('Blog.BlogPost');
		parent::setUp();
	}

	public function tearDown()
	{
		unset($this->BlogPost);
		parent::tearDown();
	}

	/*
	 * validate
	 */
	public function test必須チェック()
	{

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
		for($i = 0; $i < 2; $i++) {
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
		$this->BlogPost->setupUpload(1);

		// protectedな値にアクセスするため配列にキャストする
		$behaviors = (array)$this->BlogPost->Behaviors;
		$result = $behaviors["\0*\0_loaded"]['BcUpload']->BcFileUploader['BlogPost']->settings;

		$imagecopy = $result['fields']['eye_catch']['imagecopy'];
		$expected = [
			'thumb' => [
				'suffix' => '__thumb',
				'width' => '300',
				'height' => '300'
			],
			'mobile_thumb' => [
				'suffix' => '__mobile_thumb',
				'width' => '100',
				'height' => '100'
			]
		];

		$this->assertEquals($result['saveDir'], 'blog/1/blog_posts');
		$this->assertEquals($imagecopy, $expected);
	}

	/**
	 * コントロールソースを取得する
	 */
	public function testGetDefaultValue()
	{
		$authUser['id'] = 1;
		$data = $this->BlogPost->getDefaultValue($authUser);
		$this->assertEquals($data['BlogPost']['user_id'], $authUser['id']);
		$this->assertRegExp('/' . '([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})' . '/', $data['BlogPost']['posts_date']);
		$this->assertEquals($data['BlogPost']['posts_date'], date('Y/m/d H:i:s'));
		$this->assertEquals($data['BlogPost']['status'], 0);
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
		$data['publish_begin'] = $publish_begin;
		$data['publish_end'] = $publish_end;
		$data['status'] = $status;
		$this->assertEquals($this->BlogPost->allowPublish($data), $expected);
	}

	public function allowPublishDataProvider()
	{
		return [
			['0000-00-00 00:00:00', '0000-00-00 00:00:00', false, false],
			['0000-00-00 00:00:00', '0000-00-00 00:00:00', true, true],
			['0000-00-00 00:00:00', date('Y-m-d H:i:s'), true, false],
			['0000-00-00 00:00:00', date('Y-m-d H:i:s', strtotime("+1 hour")), true, true],
			[date('Y-m-d H:i:s'), '0000-00-00 00:00:00', true, true],
			[date('Y-m-d H:i:s', strtotime("+1 hour")), '0000-00-00 00:00:00', true, false],
			[date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), true, false]
		];
	}

	/**
	 * 公開状態の記事を取得する
	 */
	public function testGetPublishes()
	{
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
			]];
		$expected = [
			'SearchIndex' => [
				'type' => 'ブログ',
				'model_id' => false,
				'site_id' => '0',
				'title' => 'test-name',
				'detail' => 'test-content test-detail',
				'url' => '/news/archives/1',
				'status' => true,
				'content_filter_id' => '',
				'content_id' => '4',
				'publish_begin' => null,
				'publish_end' => null
			]];
		$result = $this->BlogPost->createSearchIndex($data);
		$this->assertEquals($expected, $result, 'ブログ記事用の検索用データを正しく生成できません');
	}

	/**
	 * 検索用データ生成、ステータス設定
	 * @dataProvider createSearchIndexStatusDataProvider
	 */
	public function testCreateSearchIndexStatus($blogPostStatus, $contentStatus, $expected)
	{
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
			]];
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
			]];
		$data = $this->BlogPost->BlogContent->find('first', ['conditions' => ['BlogContent.id' => $blogContentId]]);
		$data['Content']['self_publish_begin'] = $contentPublish['begin'];
		$data['Content']['self_publish_end'] = $contentPublish['end'];
		$this->BlogPost->BlogContent->Content->save($data);
		$result = $this->BlogPost->createSearchIndex($blogPost);
		$this->assertEquals($expected, [
			'begin' => ($result['SearchIndex']['publish_begin'])? date('Y-m-d', strtotime($result['SearchIndex']['publish_begin'])) : '',
			'end' => ($result['SearchIndex']['publish_end'])? date('Y-m-d', strtotime($result['SearchIndex']['publish_end'])) : ''
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

	/**
	 * beforeDelete
	 */
	public function testBeforeDelete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
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
	 * カスタムファインダー　customParams
	 *
	 * @param array $options
	 * @param mixed $expected
	 * @dataProvider findCustomParamsDataProvider
	 */
	public function testFindCustomParams($type, $options, $expected)
	{
		set_error_handler(function($no, $str, $file, $line, $context) {
		});
		$result = $this->BlogPost->find('customParams', $options);
		if ($type == 'count') {
			$this->assertEquals($expected, count($result));
		} elseif ($type == 'name') {
			$this->assertEquals($expected, $result[0]['BlogPost']['name']);
		} elseif ($type == 'id') {
			$id = Hash::extract($result, '{n}.BlogPost.id');
			$this->assertEquals($expected, $id);
		}
	}

	public function findCustomParamsDataProvider()
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
	 * アイキャッチアップロード
	 */
	public function testUpload()
	{
		if (is_dir(WWW_ROOT . '/files/blog/999')) {
			throw new Exception('テスト用のフォルダが既に存在');
		}
		copy(__DIR__ . '/../../Fixture/File/test1.png', __DIR__ . '/../../Fixture/File/test1_.png');

		$this->BlogPost->setupUpload(999);

		$data = ['BlogPost' => [
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
		]];

		// 作成
		$this->BlogPost->create($data);
		$blogPost1 = $this->BlogPost->save();
		$blogPost1no = $blogPost1['BlogPost']['no'];
		$ym = date('Y/m');
		$fileDir = WWW_ROOT . '/files/blog/999/blog_posts/' . $ym;

		$this->assertEquals($ym . '/0000000' . $blogPost1no .  '_eye_catch.png', $blogPost1['BlogPost']['eye_catch']);
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch__thumb.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch__mobile_thumb.png'));

		$this->BlogPost = ClassRegistry::init('Blog.BlogPost');
		$this->BlogPost->setupUpload(999);

		// コピー
		$blogPost2 = $this->BlogPost->copy(null, $blogPost1);
		$blogPost2no = $blogPost2['BlogPost']['no'];

		// 複製元が影響を受けていないか
		$this->assertEquals($ym . '/0000000' . $blogPost1no .  '_eye_catch.png', $blogPost1['BlogPost']['eye_catch']);
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch__thumb.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost1no .  '_eye_catch__mobile_thumb.png'));

		// 複製できているか
		$this->assertEquals($ym . '/0000000' . $blogPost2no .  '_eye_catch.png', $blogPost2['BlogPost']['eye_catch']);
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch__thumb.png'));
		$this->assertTrue(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch__mobile_thumb.png'));

		// 削除
		$this->BlogPost->delete($blogPost2['BlogPost']['id']);
		$blogPost2 = $this->BlogPost->find('first', [
			'conditions' => [
				'BlogPost.id' => $blogPost2['BlogPost']['id'],
				// どこかでキャッシュされているのか、条件を変更しないと削除前の情報が取得できてしまう
				'BlogPost.id !=' => 9999,
			],
		]);
		$this->assertEmpty($blogPost2);
		$this->assertFalse(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch.png'));
		$this->assertFalse(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch__thumb.png'));
		$this->assertFalse(is_file($fileDir . '/0000000' . $blogPost2no .  '_eye_catch__mobile_thumb.png'));

		$dir = new Folder(WWW_ROOT . '/files/blog/999');
		$dir->delete();
	}

}
