<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Test.Case.Model
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BlogContent', 'Blog.Model');

/**
 * Class BlogContentTest
 * 
 * @property BlogContent $BlogContent
 */
class BlogContentTest extends BaserTestCase {

	public $fixtures = [
		'baser.Default.SearchIndex',
		'baser.Default.SiteConfig',
		'baser.Default.BlogPost',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogCategory',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.User',
	];

	public function setUp() {
		$this->BlogContent = ClassRegistry::init('Blog.BlogContent');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->BlogContent);
		parent::tearDown();
	}

/*
 * validate
 */
	public function test空チェック() {
		$this->BlogContent->create([
			'BlogContent' => [
				'list_direction' => ''
			]
		]);
		$this->assertFalse($this->BlogContent->validates());
		$this->assertArrayHasKey('list_direction', $this->BlogContent->validationErrors);
		$this->assertEquals('一覧に表示する順番を指定してください。', current($this->BlogContent->validationErrors['list_direction']));
	}

	public function test桁数チェック異常系() {
		$this->BlogContent->create([
			'BlogContent' => [
				'layout' => '123456789012345678901',
				'template' => '123456789012345678901',
			]
		]);
		$this->assertFalse($this->BlogContent->validates());

		$this->assertArrayHasKey('layout', $this->BlogContent->validationErrors);
		$this->assertEquals('レイアウトテンプレート名は20文字以内で入力してください。', current($this->BlogContent->validationErrors['layout']));

		$this->assertArrayHasKey('template', $this->BlogContent->validationErrors);
		$this->assertEquals('レイアウトテンプレート名は20文字以内で入力してください。', current($this->BlogContent->validationErrors['template']));
	}

	public function test桁数チェック正常系() {
		$this->BlogContent->create([
			'BlogContent' => [
				'layout' => '12345678901234567890',
				'template' => '12345678901234567890',
			]
		]);

		$this->assertTrue($this->BlogContent->validates());
	}

	public function testその他異常系() {
		// 半角チェック
		$this->BlogContent->create([
			'BlogContent' => [
				'template' => 'テスト',
				'list_count' => 'テスト',
			]
		]);

		$this->assertFalse($this->BlogContent->validates());

		$this->assertArrayHasKey('template', $this->BlogContent->validationErrors);
		$this->assertEquals('コンテンツテンプレート名は半角で入力してください。', current($this->BlogContent->validationErrors['template']));

		$this->assertArrayHasKey('list_count', $this->BlogContent->validationErrors);
		$this->assertEquals('一覧表示件数は半角で入力してください。', current($this->BlogContent->validationErrors['list_count']));
		
		// eye_catch_sizeチェック
		$this->BlogContent->create([
			'BlogContent' => [
				'eye_catch_size' => '',
			]
		]);

		$this->assertFalse($this->BlogContent->validates());
		$this->assertArrayHasKey('eye_catch_size', $this->BlogContent->validationErrors);
		$this->assertEquals('アイキャッチ画像のサイズが不正です。', current($this->BlogContent->validationErrors['eye_catch_size']));
	}

	public function testその他正常系() {
		// eye_catch_sizeチェック
		$data = [
			'thumb_width' => 1,
			'thumb_height' => 1,
			'mobile_thumb_width' => 1,
			'mobile_thumb_height' => 1,
		];

		$this->BlogContent->create([
			'BlogContent' => [
				'eye_catch_size' => BcUtil::serialize($data)
			]
		]);

		$this->assertTrue($this->BlogContent->validates());
		$this->assertArrayNotHasKey('eye_catch_size', $this->BlogContent->validationErrors);
	}

/**
 * アイキャッチ画像サイズバリデーション
 */
	public function testCheckEyeCatchSize() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 英数チェック
 */
	public function testAlphaNumeric() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * コントロールソースを取得する
 */
	public function testGetControlSource() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * afterSave
 *
 * @param mixed $id 
 * @param int $exclude_search 
 * @dataProvider afterSaveDataProvider
 */
	public function testAfterSave($id, $exclude_search) {
		$this->BlogContent->create([
			'BlogContent' => [
				'id' => $id,	
				'description' => 'test-description',
			],
			'Content' => [
				'id' => $id,
				'name' => 'test-name',
				'parent_id' => 1,
				'title' => 'test-title',
				'exclude_search' => $exclude_search,
				'status' => 1,
				'site_id' => 0,
				'entity_id' => $id
			]
		]);

		$this->BlogContent->save();

		if (!$exclude_search) {
			$BlogContent = ClassRegistry::init('Blog.BlogContent');
			$result = $BlogContent->find('count', [
				'conditions' => ['Content.name' => 'test-name'],
			]);
			$this->assertEquals($result, 1, '検索用テーブルへ登録できません');
			unset($BlogContent);
		
		} else {
			$SearchIndex = ClassRegistry::init('SearchIndex');
			$result = $SearchIndex->find('count', [
				'conditions' => ['SearchIndex.model' => 'BlogContent'],
			]);
			$this->assertEquals($result, 0, '検索用テーブルから削除できません');
			unset($SearchIndex);

		}

	}

	public function afterSaveDataProvider() {
		return [
			['', 0],
			[1, 1],
		];
	}

/**
 * beforeDelete
 */
	public function testBeforeDelete() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 検索用データを生成する
 */
	public function testCreateSearchIndex() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$data = [
			'name' => 'test-name',
			'title' => 'test-title',
			'description' => 'test-description',
		];
		$result = $this->BlogContent->createContent($data);

		$expected = [
			'Content' => [
				'type' => 'ブログ',
				'model_id' => false,
				'category' => '',
				'title' => 'test-title',
				'detail' => 'test-description',
				'url' => '/test-name/index',
				'status' => true
			]
		];
		$this->assertEquals($expected, $result, '正しく検索用データを生成でません');
	}

/**
 * ブログコンテンツデータをコピーする
 */
	public function testCopy() {
		$this->BlogContent->copy(1, 1, 'hoge1', 1, 0);
		$result = $this->BlogContent->find('first', [
			'conditions' => ['BlogContent.id' => $this->BlogContent->getLastInsertID()]
		]);
		$this->assertEquals($result['Content']['title'], 'hoge1');
		$this->BlogContent->copy(1, 1, 'test-title', 1, 0);
		$result = $this->BlogContent->find('first', [
			'conditions' => ['BlogContent.id' => $this->BlogContent->getLastInsertID()]
		]);
		$this->assertEquals($result['Content']['title'], 'test-title');
	}

/**
 * フォームの初期値を取得する
 */
	public function testGetDefaultValue() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * アイキャッチサイズフィールドの値をDB用に変換する
 */
	public function testDeconstructEyeCatchSize() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * アイキャッチサイズフィールドの値をフォーム用に変換する
 */
	public function testConstructEyeCatchSize() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
