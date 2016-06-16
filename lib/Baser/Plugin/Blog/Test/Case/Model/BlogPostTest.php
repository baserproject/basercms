<?php

/**
 * test for BlogPost
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS BlogPosts Community <http://sites.google.com/site/baserBlogPosts/>
 * @package         Feed.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS BlogPosts Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.1.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('BlogPost', 'Blog.Model');

class BlogPostTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.User',
		'baser.Default.Content',
		'baser.Default.PluginContent',
		'baser.Default.SiteConfig',
		'baser.Default.BlogTag',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
		'plugin.blog.Model/BlogPostModel',
		'plugin.blog.Model/BlogCategoryModel',
	);

	public function setUp() {
		$this->BlogPost = ClassRegistry::init('Blog.BlogPost');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->BlogPost);
		parent::tearDown();
	}

/*
 * validate
 */
	public function test必須チェック() {

		$this->BlogPost->create(array(
			'BlogPost' => array()
		));

		$this->assertFalse($this->BlogPost->validates());

		$this->assertArrayHasKey('name', $this->BlogPost->validationErrors);
		$this->assertEquals('タイトルを入力してください。', current($this->BlogPost->validationErrors['name']));

		$this->assertArrayHasKey('posts_date', $this->BlogPost->validationErrors);
		$this->assertEquals('投稿日を入力してください。', current($this->BlogPost->validationErrors['posts_date']));
	}

	public function test空チェック() {
		$this->BlogPost->create(array(
			'BlogPost' => array(
				'user_id' => ''
			)
		));

		$this->assertFalse($this->BlogPost->validates());

		$this->assertArrayHasKey('user_id', $this->BlogPost->validationErrors);
		$this->assertEquals('投稿者を選択してください。', current($this->BlogPost->validationErrors['user_id']));
	}

	public function test桁数チェック異常系() {
		$this->BlogPost->create(array(
			'BlogPost' => array(
				'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
			)
		));

		$this->assertFalse($this->BlogPost->validates());

		$this->assertArrayHasKey('name', $this->BlogPost->validationErrors);
		$this->assertEquals('タイトルは255文字以内で入力してください。', current($this->BlogPost->validationErrors['name']));
	}

	public function test桁数チェック正常系() {
		$this->BlogPost->create(array(
			'BlogPost' => array(
				'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'posts_date' => '2020-01-27 12:57:59'
			)
		));
		$this->assertTrue($this->BlogPost->validates());
	}

	public function testその他異常系() {
		// 形式チェック
		$this->BlogPost->create(array(
			'BlogPost' => array(
				'publish_begin' => 'test',
				'publish_end' => 'test',
				'posts_date' => 'test',
			)
		));

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

		$this->BlogPost->create(array(
			'BlogPost' => array(
				'detail' => $bigData,
				'detail_draft' => $bigData,
			)
		));

		$this->assertFalse($this->BlogPost->validates());

		$this->assertArrayHasKey('detail', $this->BlogPost->validationErrors);
		$this->assertEquals('本稿欄に保存できるデータ量を超えています。', current($this->BlogPost->validationErrors['detail']));

		$this->assertArrayHasKey('detail_draft', $this->BlogPost->validationErrors);
		$this->assertEquals('草稿欄に保存できるデータ量を超えています。', current($this->BlogPost->validationErrors['detail_draft']));
	}

	public function testその他正常系() {
		// 形式チェック
		$this->BlogPost->create(array(
			'BlogPost' => array(
				'publish_begin' => '2020-01-27 12:57:59',
				'publish_end' => '2020-01-27 12:57:59',
				'posts_date' => '2020-01-27 12:57:59',
			)
		));

		$this->BlogPost->validates();
		$this->assertArrayNotHasKey('publish_begin', $this->BlogPost->validationErrors);
		$this->assertArrayNotHasKey('publish_end', $this->BlogPost->validationErrors);
		$this->assertArrayNotHasKey('posts_date', $this->BlogPost->validationErrors);

		// データ量チェック
		$this->BlogPost->create(array(
			'BlogPost' => array(
				'detail' => 'test',
				'detail_draft' => 'test',
			)
		));

		$this->BlogPost->validates();
		$this->assertArrayNotHasKey('detail', $this->BlogPost->validationErrors);
		$this->assertArrayNotHasKey('detail_draft', $this->BlogPost->validationErrors);
	}

/**
 * アップロードビヘイビアの設定
 */
	public function testSetupUpload() {
		$this->BlogPost->setupUpload(1);

		// protectedな値にアクセスするため配列にキャストする
		$behaviors = (array) $this->BlogPost->Behaviors;
		$result = $behaviors["\0*\0_loaded"]['BcUpload']->settings['BlogPost'];

		$imagecopy = $result['fields']['eye_catch']['imagecopy'];
		$expected = array(
			'thumb' => array(
				'suffix' => '__thumb',
				'width' => '300',
				'height' => '300'
			),
			'mobile_thumb' => array(
				'suffix' => '__mobile_thumb',
				'width' => '100',
				'height' => '100'
			)
		);

		$this->assertEquals($result['saveDir'], 'blog/news/blog_posts');
		$this->assertEquals($imagecopy, $expected);
	}

/**
 * ブログの月別一覧を取得する
 *
 * @param int $blogContentId ブログコンテンツID
 * @param array $options オプション
 * @param array $expected 期待値
 * @dataProvider getPostedDatesDataProvider
 */
	public function testGetPostedDates($blogContentId, $options, $expected) {
		$result = $this->BlogPost->getPostedDates($blogContentId, $options);
		$this->assertEquals($expected, $result, '正しくブログの月別一覧を取得できません');
	}

	public function getPostedDatesDataProvider() {
		return array(
			array(1, array(), array(
					array('year' => '2015', 'month' => '01')
				)),
			array(2, array(), array(
					array('year' => '2016', 'month' => '02'),
					array('year' => '2015', 'month' => '01')
				)),
			array(1, array('category' => true), array(
					array(
						'year' => '2015', 'month' => '01',
						'BlogCategory' => array('id' => '1', 'name' => 'release', 'title' => 'プレスリリース'))
				)),
			array(1, array('viewCount' => true, 'type' => 'year'), array(
					array('year' => '2015', 'count' => 1)
				)),
		);
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
	public function testGetEntryDates($blogContentId, $year, $month, $expected) {
		$dbConfig = new DATABASE_CONFIG();
		$datasource = $dbConfig->plugin['datasource'];
		if ($datasource === 'Database/BcSqlite') {
			$this->markTestIncomplete('このテストは、まだ実装されていません。');
		}

		$result = $this->BlogPost->getEntryDates($blogContentId, $year, $month);
		$this->assertEquals($expected, $result, '正しく日付リストを取得できません');
	}

	public function getEntryDatesDataProvider() {
		return array(
			array(1, 2015, 1, array('2015-01-27')),
			array(1, 2016, 1, array()),
			array(2, 2016, 2, array('2016-02-10')),
		);
	}

/**
 * 投稿者の一覧を取得する
 */
	public function testGetAuthors() {
		$message = '投稿者一覧を正しく取得できません';
		$result = $this->BlogPost->getAuthors(1, array());
		$this->assertEquals($result[0]['User']['name'], 'basertest', $message);

		$result = $this->BlogPost->getAuthors(2, array());
		$this->assertEquals($result[0]['User']['name'], 'basertest', $message);
		$this->assertEquals($result[1]['User']['name'], 'basertest2', $message);

		$result = $this->BlogPost->getAuthors(2, array('viewCount' => true));
		$this->assertEquals($result[0]['count'], 1, $message);
	}

/**
 * 指定した月の記事が存在するかチェックする
 */
	public function testExistsEntry() {
		$dbConfig = new DATABASE_CONFIG();
		$datasource = $dbConfig->plugin['datasource'];
		if ($datasource === 'Database/BcSqlite') {
			$this->markTestIncomplete('このテストは、まだ実装されていません。');
		}

		$message = '指定した月の記事が存在するか正しくチェックできません';
		$result = $this->BlogPost->existsEntry(1, 2015, 1);
		$this->assertTrue($result);

		$result = $this->BlogPost->existsEntry(1, 2016, 1);
		$this->assertFalse($result);

		$result = $this->BlogPost->existsEntry(2, 2015, 1);
		$this->assertTrue($result);

		$result = $this->BlogPost->existsEntry(2, 2016, 1);
		$this->assertFalse($result);
	}

/**
 * コントロールソースを取得する
 *
 * @param array $options オプション
 * @param array $expected 期待値
 * @dataProvider getControlSourceDataProvider
 */
	public function testGetControlSource($options, $expected) {
		$result = $this->BlogPost->getControlSource('blog_category_id', $options);
		$this->assertEquals($expected, $result, '正しくコントロールソースを取得できません');
	}

	public function getControlSourceDataProvider() {
		return array(
			array(array('blogContentId' => 1), array(1 => 'プレスリリース', 2 => '&nbsp&nbsp&nbsp└子カテゴリ', 3 => '親子関係なしカテゴリ')),
			array(array('blogContentId' => 2), array()),
			array(array('userGroupId' => 1, 'blogContentId' => 1, 'postEditable' => false, 'blogCategoryId' => 2), array(2 => '子カテゴリ')),
		);
	}

/**
 * 公開状態の記事を取得する
 */
	public function testGetPublishes() {
		$message = '正しく公開状態の記事を取得できません';
		
		$result = count($this->BlogPost->getPublishes(array()));
		$this->assertEquals($result, 3, $message);

		$options = array('conditions' => array(
			'publish_begin' => '9000-01-27 12:00:00'
		));
		$result = $this->BlogPost->getPublishes($options);
		$this->assertEmpty($result);
	}

/**
 * afterSave
 */
	public function testAfterSave() {
		$data = array('BlogPost' => array(
			'id' => 99,
			'exclude_search' => 0,
			'name' => 'test-name',
			'blog_content_id' => 1,
			'posts_date' => '2020-01-27 12:57:59',
			'content' => 'test-content',
			'detail' => 'test-detail',
			'no' => 4,
			'status' => 0,
			'publish_begin' => '2020-01-27 12:57:59',
			'publish_end' => '2020-01-27 12:57:59',
		));

		$Content = ClassRegistry::init('Content');
		
		// 登録
		$data['BlogPost']['exclude_search'] = false;
		$this->BlogPost->create($data);
		$this->BlogPost->save();

		$result = $Content->find('count', array(
			'conditions' => array('Content.title' => 'test-name'),
		));
		$this->assertEquals($result, 1, '検索用テーブルへ登録できません');
		
		// 削除
		$data['BlogPost']['exclude_search'] = true;
		$this->BlogPost->create($data);
		$this->BlogPost->save();

		$result = $Content->find('count', array(
			'conditions' => array('Content.title' => 'test-name'),
		));
		$this->assertEquals($result, 0, '検索用テーブルから削除できません');

		unset($Content);
	}


/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 * @access public
 */
	public function testCreateContent() {
		$data = array(
			'name' => 'test-name',
			'content' => 'test-content',
			'detail' => 'test-detail',
			'blog_content_id' => 1,
			'no' => 1,
			'status' => true,
			'publish_begin' => '2020-01-27 12:57:59',
			'publish_end' => '2020-01-27 12:57:59',
		);
		$expected = array(
		'Content' => array(
			'type' => 'ブログ',
			'model_id' => false,
			'category' => '',
			'title' => 'test-name',
			'detail' => 'test-content test-detail',
			'url' => '/news/archives/1',
			'status' => false
		));

		$result = $this->BlogPost->createContent($data);
		$this->assertEquals($expected, $result, '正しく検索用データを生成できません');

		// blog_category_idを指定
		$data['blog_category_id'] = 1;
		$expected['Content']['category'] = 'プレスリリース';
		$result = $this->BlogPost->createContent($data);

		$this->assertEquals($expected, $result, '正しく検索用データを生成できません');

	}

/**
 * コピーする
 * 
 * @param int $id
 * @param array $data
 * @return mixed page Or false
 */
	public function testCopy() {
		$this->BlogPost->copy(1);
		$result = $this->BlogPost->find('first', array(
			'conditions' => array('BlogPost.id' => $this->BlogPost->getLastInsertID())
		));
		$this->assertEquals($result['BlogPost']['name'], 'ホームページをオープンしました_copy');
	}

}
