<?php

/**
 * test for BlogCategory
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS BlogCategorys Community <http://sites.google.com/site/baserBlogCategorys/>
 * @package         Feed.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS BlogCategorys Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.1.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('BlogCategory', 'Blog.Model');

class BlogCategoryTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Content',
		'baser.Default.PluginContent',
		'baser.Default.Permission',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.SiteConfig',
		'baser.Default.BlogPost',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
		'baser.Default.BlogTag',
		'plugin.blog.Model/BlogCategoryModel',
	);

	public function setUp() {
		$this->BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->BlogCategory);
		parent::tearDown();
	}

/*
 * validate
 */
	public function test必須チェック() {
		// blog_content_idを設定
		$this->BlogCategory->validationParams = array(
			'blogContentId' => 1
		);

		$this->BlogCategory->create(array(
			'BlogCategory' => array(
				'blog_content_id' => 1
			)
		));

		$this->assertFalse($this->BlogCategory->validates());

		$this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
		$this->assertEquals('ブログカテゴリ名を入力してください。', current($this->BlogCategory->validationErrors['name']));

		$this->assertArrayHasKey('title', $this->BlogCategory->validationErrors);
		$this->assertEquals('ブログカテゴリタイトルを入力してください。', current($this->BlogCategory->validationErrors['title']));
	}

	public function test桁数チェック異常系() {
		// blog_content_idを設定
		$this->BlogCategory->validationParams = array(
			'blogContentId' => 1
		);

		$this->BlogCategory->create(array(
			'BlogCategory' => array(
				'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
			)
		));
		$this->assertFalse($this->BlogCategory->validates());

		$this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
		$this->assertEquals('ブログカテゴリ名は255文字以内で入力してください。', current($this->BlogCategory->validationErrors['name']));

		$this->assertArrayHasKey('title', $this->BlogCategory->validationErrors);
		$this->assertEquals('ブログカテゴリ名は255文字以内で入力してください。', current($this->BlogCategory->validationErrors['title']));
	}

	public function test桁数チェック正常系() {
		// blog_content_idを設定
		$this->BlogCategory->validationParams = array(
			'blogContentId' => 1
		);

		$this->BlogCategory->create(array(
			'BlogCategory' => array(
				'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
			)
		));

		$this->assertTrue($this->BlogCategory->validates());
	}

	public function testその他異常系() {
		// blog_content_idを設定
		$this->BlogCategory->validationParams = array(
			'blogContentId' => 1
		);

		// 半角チェック
		$this->BlogCategory->create(array(
			'BlogCategory' => array(
				'name' => 'テスト',
			)
		));

		$this->assertFalse($this->BlogCategory->validates());

		$this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
		$this->assertEquals('ブログカテゴリ名は半角のみで入力してください。', current($this->BlogCategory->validationErrors['name']));

		// 重複チェック
		$this->BlogCategory->create(array(
			'BlogCategory' => array(
				'name' => 'release',
			)
		));

		$this->assertFalse($this->BlogCategory->validates());

		$this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
		$this->assertEquals('入力されたブログカテゴリは既に登録されています。', current($this->BlogCategory->validationErrors['name']));
	}

/**
 * コントロールソースを取得する
 * 
 * @param string $field フィールド名
 * @param array $option オプション
 * @param array $expected 期待値
 * @dataProvider getControlSourceDataProvider
 */
	public function testGetControlSource($field, $options, $expected) {
		$result = $this->BlogCategory->getControlSource($field, $options);
		$this->assertEquals($expected, $result, 'コントロールソースを正しく取得できません');
	}

	public function getControlSourceDataProvider() {
		return array(
			array('parent_id', array('blogContentId' => 1), array(
				1 => 'プレスリリース',
				2 => '&nbsp&nbsp&nbsp└子カテゴリ',
				3 => '親子関係なしカテゴリ')),
			array('parent_id', array('blogContentId' => 0), array()),
			array('parent_id', array('blogContentId' => 1, 'excludeParentId' => true), array(3 => '親子関係なしカテゴリ')),
			array('parent_id', array('blogContentId' => 1, 'ownerId' => 2), array()),
			array('parent_id', array('blogContentId' => 1, 'ownerId' => 1), array(
				1 => 'プレスリリース',
				2 => '&nbsp&nbsp&nbsp└子カテゴリ',
				3 => '親子関係なしカテゴリ')),
			array('owner_id', array(), array(
				1 => 'システム管理',
				2 => 'サイト運営')),
		);
	}

/**
 * 関連する記事データをカテゴリ無所属に変更し保存する
 */
	public function testBeforeDelete() {
		$this->BlogCategory->data = array('BlogCategory' => array(
			'id' => '1'
		));
		$this->BlogCategory->delete();

		$BlogPost = ClassRegistry::init('Blog.BlogPost');
		$result = $BlogPost->find('first', array(
        'conditions' => array('blog_category_id' => 1)
    ));
    $this->assertEmpty($result);
	}

/**
 * カテゴリリストを取得する
 */
	public function testGetCategoryList() {
		$message = '正しくカテゴリリストを取得できません';
		// 正常
		$result = $this->BlogCategory->getCategoryList(1, array());
		$this->assertNotEmpty($result, $message);
		$this->assertEquals($result[0]['BlogCategory']['id'], 1, $message);

		// 存在しないID
		$result = $this->BlogCategory->getCategoryList(0, array());
		$this->assertEmpty($result, $message);

		// option depth 2
		$result = $this->BlogCategory->getCategoryList(1, array('depth' => 2));
		$this->assertNotEmpty($result[0]['BlogCategory']['children'], $message);

		// option type year
		$result = $this->BlogCategory->getCategoryList(1, array('type' => 'year'));
		$this->assertNotEmpty($result, $message);
		$this->assertEquals($result['2015'][0]['BlogCategory']['id'], 1, $message);

		// option viewCount true
		$result = $this->BlogCategory->getCategoryList(1, array('viewCount' => true));
		$this->assertEquals($result[0]['BlogCategory']['count'], 2, $message);

		// option limit true
		$result = $this->BlogCategory->getCategoryList(1, array('type' => 'year', 'limit' => 1, 'viewCount' => true));
		$this->assertEquals($result['2015'][0]['BlogCategory']['count'], 1, $message);
	}


/**
 * カテゴリオーナーの基準において新しいカテゴリが追加できる状態かチェックする
 */
	public function testCheckNewCategoryAddable() {
		$message = '新しいカテゴリが追加できる状態チェックが正しくありません';
		$result = $this->BlogCategory->checkNewCategoryAddable(1, false);
		$this->assertTrue($result, $message);

		$result = $this->BlogCategory->checkNewCategoryAddable(99, false);
		$this->assertFalse($result, $message);

		$result = $this->BlogCategory->checkNewCategoryAddable(99, true);
		$this->assertTrue($result, $message);
	}
	
/**
 * アクセス制限としてカテゴリの新規追加ができるか確認する
 */
	public function testHasNewCategoryAddablePermission() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$result = $this->BlogCategory->hasNewCategoryAddablePermission(2, 99);
		
	}


}
