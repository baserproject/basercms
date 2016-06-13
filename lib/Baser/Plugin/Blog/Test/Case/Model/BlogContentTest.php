<?php

/**
 * test for BlogContent
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS BlogContents Community <http://sites.google.com/site/baserBlogContents/>
 * @package         Feed.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS BlogContents Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.1.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('BlogContent', 'Blog.Model');

class BlogContentTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Content',
		'baser.Default.PluginContent',
		'baser.Default.SiteConfig',
		'baser.Default.BlogPost',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogCategory',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
	);

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

		$this->BlogContent->create(array(
			'BlogContent' => array(
				'title' => '',
				'list_direction' => ''
			)
		));

		$this->assertFalse($this->BlogContent->validates());

		$this->assertArrayHasKey('title', $this->BlogContent->validationErrors);
		$this->assertEquals('ブログタイトルを入力してください。', current($this->BlogContent->validationErrors['title']));

		$this->assertArrayHasKey('list_direction', $this->BlogContent->validationErrors);
		$this->assertEquals('一覧に表示する順番を指定してください。', current($this->BlogContent->validationErrors['list_direction']));
	}

	public function test桁数チェック異常系() {

		$this->BlogContent->create(array(
			'BlogContent' => array(
				'name' => '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901',
				'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'layout' => '123456789012345678901',
				'template' => '123456789012345678901',
			)
		));

		$this->assertFalse($this->BlogContent->validates());

		$this->assertArrayHasKey('name', $this->BlogContent->validationErrors);
		$this->assertEquals('ブログアカウント名は100文字以内で入力してください。', current($this->BlogContent->validationErrors['name']));

		$this->assertArrayHasKey('title', $this->BlogContent->validationErrors);
		$this->assertEquals('ブログタイトルは255文字以内で入力してください。', current($this->BlogContent->validationErrors['title']));

		$this->assertArrayHasKey('layout', $this->BlogContent->validationErrors);
		$this->assertEquals('レイアウトテンプレート名は20文字以内で入力してください。', current($this->BlogContent->validationErrors['layout']));

		$this->assertArrayHasKey('template', $this->BlogContent->validationErrors);
		$this->assertEquals('レイアウトテンプレート名は20文字以内で入力してください。', current($this->BlogContent->validationErrors['template']));
	}

	public function test桁数チェック正常系() {
		$this->BlogContent->create(array(
			'BlogContent' => array(
				'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
				'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'layout' => '12345678901234567890',
				'template' => '12345678901234567890',
			)
		));

		$this->assertTrue($this->BlogContent->validates());
	}

	public function testその他異常系() {
		// 半角チェック
		$this->BlogContent->create(array(
			'BlogContent' => array(
				'name' => 'テスト',
				'layout' => 'テスト',
				'template' => 'テスト',
				'list_count' => 'テスト',
			)
		));

		$this->assertFalse($this->BlogContent->validates());

		$this->assertArrayHasKey('name', $this->BlogContent->validationErrors);
		$this->assertEquals('ブログアカウント名は半角のみ入力してください。', current($this->BlogContent->validationErrors['name']));

		$this->assertArrayHasKey('layout', $this->BlogContent->validationErrors);
		$this->assertEquals('レイアウトテンプレート名は半角で入力してください。', current($this->BlogContent->validationErrors['layout']));

		$this->assertArrayHasKey('template', $this->BlogContent->validationErrors);
		$this->assertEquals('コンテンツテンプレート名は半角で入力してください。', current($this->BlogContent->validationErrors['template']));

		$this->assertArrayHasKey('list_count', $this->BlogContent->validationErrors);
		$this->assertEquals('一覧表示件数は半角で入力してください。', current($this->BlogContent->validationErrors['list_count']));

		// 重複チェック
		$this->BlogContent->create(array(
			'BlogContent' => array(
				'name' => 'news',
			)
		));

		$this->assertFalse($this->BlogContent->validates());

		$this->assertArrayHasKey('name', $this->BlogContent->validationErrors);
		$this->assertEquals('入力されたブログアカウント名は既に使用されています。', current($this->BlogContent->validationErrors['name']));

		// notInListチェック
		$this->BlogContent->create(array(
			'BlogContent' => array(
				'name' => 'blog',
			)
		));

		$this->assertFalse($this->BlogContent->validates());

		$this->assertArrayHasKey('name', $this->BlogContent->validationErrors);
		$this->assertEquals('ブログアカウント名に「blog」は利用できません。', current($this->BlogContent->validationErrors['name']));

		// eye_catch_sizeチェック
		$this->BlogContent->create(array(
			'BlogContent' => array(
				'eye_catch_size' => '',
			)
		));

		$this->assertFalse($this->BlogContent->validates());
		$this->assertArrayHasKey('eye_catch_size', $this->BlogContent->validationErrors);
		$this->assertEquals('アイキャッチ画像のサイズが不正です。', current($this->BlogContent->validationErrors['eye_catch_size']));
	}

	public function testその他正常系() {
		// eye_catch_sizeチェック
		$data = array(
			'thumb_width' => 1,
			'thumb_height' => 1,
			'mobile_thumb_width' => 1,
			'mobile_thumb_height' => 1,
		);

		$this->BlogContent->create(array(
			'BlogContent' => array(
				'eye_catch_size' => BcUtil::serialize($data)
			)
		));

		$this->assertTrue($this->BlogContent->validates());
		$this->assertArrayNotHasKey('eye_catch_size', $this->BlogContent->validationErrors);
	}

/**
 * afterSave
 *
 * @param mixed $id 
 * @param int $exclude_search 
 * @dataProvider afterSaveDataProvider
 */
	public function testAfterSave($id, $exclude_search) {
		$this->BlogContent->create(array(
			'BlogContent' => array(
				'id' => $id,
				'name' => 'test-name',
				'title' => 'test-title',
				'description' => 'test-description',
				'exclude_search' => $exclude_search,
				'status' => 1
			)
		));

		$this->BlogContent->save();

		if (!$exclude_search) {
			$BlogPost = ClassRegistry::init('Blog.BlogContent');
			$result = $BlogPost->find('count', array(
				'conditions' => array('BlogContent.name' => 'test-name'),
			));
			$this->assertEquals($result, 1, '検索用テーブルへ登録できません');
			unset($BlogPost);
		
		} else {
			$Content = ClassRegistry::init('Content');
			$result = $Content->find('count', array(
				'conditions' => array('Content.model' => 'BlogContent'),
			));
			$this->assertEquals($result, 0, '検索用テーブルから削除できません');
			unset($Content);

		}

	}

	public function afterSaveDataProvider() {
		return array(
			array('', 0),
			array(1, 1),
		);
	}

/**
 * 検索用データを生成する
 */
	public function testCreateContent() {
		$data = array(
			'name' => 'test-name',
			'title' => 'test-title',
			'description' => 'test-description',
		);
		$result = $this->BlogContent->createContent($data);

		$expected = array(
			'Content' => array(
				'type' => 'ブログ',
				'model_id' => false,
				'category' => '',
				'title' => 'test-title',
				'detail' => 'test-description',
				'url' => '/test-name/index',
				'status' => true
			)
		);
		$this->assertEquals($expected, $result, '正しく検索用データを生成でません');
	}

/**
 * ユーザーグループデータをコピーする
 */
	public function testCopy() {
		$this->BlogContent->copy(1);
		$result = $this->BlogContent->find('first', array(
			'conditions' => array('BlogContent.id' => $this->BlogContent->getLastInsertID())
		));
		$this->assertEquals($result['BlogContent']['name'], 'news_copy');

		$data = array(
			'BlogContent' => array(
				'name' => 'test-name',
				'title' => 'test-title',
				'description' => 'test-description',
				'exclude_search' => 0,
		));
		$this->BlogContent->copy(false, $data);
		$result = $this->BlogContent->find('first', array(
			'conditions' => array('BlogContent.id' => $this->BlogContent->getLastInsertID())
		));
		$this->assertEquals($result['BlogContent']['name'], 'test-name_copy');
	}

}
