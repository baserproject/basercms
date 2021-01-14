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

App::uses('BlogCategory', 'Blog.Model');

/**
 * Class BlogCategoryTest
 *
 * @property BlogCategory $BlogCategory
 */
class BlogCategoryTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.SearchIndex',
		'baser.Default.Permission',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.SiteConfig',
		'baser.Default.BlogPost',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
		'baser.Default.BlogTag',
		'baser.Default.Content',
		'baser.Default.Site',
		'plugin.blog.Model/BlogPost/BlogCategoryModel',
	];

	public function setUp()
	{
		$this->BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		parent::setUp();
	}

	public function tearDown()
	{
		unset($this->BlogCategory);
		parent::tearDown();
	}

	/*
	 * validate
	 */
	public function test必須チェック()
	{
		// blog_content_idを設定
		$this->BlogCategory->validationParams = [
			'blogContentId' => 1
		];

		$this->BlogCategory->create([
			'BlogCategory' => [
				'blog_content_id' => 1
			]
		]);

		$this->assertFalse($this->BlogCategory->validates());

		$this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
		$this->assertEquals('カテゴリ名を入力してください。', current($this->BlogCategory->validationErrors['name']));

		$this->assertArrayHasKey('title', $this->BlogCategory->validationErrors);
		$this->assertEquals('カテゴリタイトルを入力してください。', current($this->BlogCategory->validationErrors['title']));
	}

	public function test桁数チェック異常系()
	{
		// blog_content_idを設定
		$this->BlogCategory->validationParams = [
			'blogContentId' => 1
		];

		$this->BlogCategory->create([
			'BlogCategory' => [
				'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
			]
		]);
		$this->assertFalse($this->BlogCategory->validates());

		$this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
		$this->assertEquals('カテゴリ名は255文字以内で入力してください。', current($this->BlogCategory->validationErrors['name']));

		$this->assertArrayHasKey('title', $this->BlogCategory->validationErrors);
		$this->assertEquals('カテゴリタイトルは255文字以内で入力してください。', current($this->BlogCategory->validationErrors['title']));
	}

	public function test桁数チェック正常系()
	{
		// blog_content_idを設定
		$this->BlogCategory->validationParams = [
			'blogContentId' => 1
		];

		$this->BlogCategory->create([
			'BlogCategory' => [
				'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
			]
		]);

		$this->assertTrue($this->BlogCategory->validates());
	}

	public function testその他異常系()
	{
		// blog_content_idを設定
		$this->BlogCategory->validationParams = [
			'blogContentId' => 1
		];

		// 半角チェック
		$this->BlogCategory->create([
			'BlogCategory' => [
				'name' => 'テスト',
			]
		]);

		$this->assertFalse($this->BlogCategory->validates());

		$this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
		$this->assertEquals('カテゴリ名は半角のみで入力してください。', current($this->BlogCategory->validationErrors['name']));

		// 重複チェック
		$this->BlogCategory->create([
			'BlogCategory' => [
				'name' => 'release',
			]
		]);

		$this->assertFalse($this->BlogCategory->validates());

		$this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
		$this->assertEquals('入力されたカテゴリ名は既に登録されています。', current($this->BlogCategory->validationErrors['name']));
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @param array $option オプション
	 * @param array $expected 期待値
	 * @dataProvider getControlSourceDataProvider
	 */
	public function testGetControlSource($field, $options, $expected)
	{
		$result = $this->BlogCategory->getControlSource($field, $options);
		$this->assertEquals($expected, $result, 'コントロールソースを正しく取得できません');
	}

	public function getControlSourceDataProvider()
	{
		return [
			['parent_id', ['blogContentId' => 1], [
				1 => 'プレスリリース',
				2 => '　　　└子カテゴリ',
				3 => '親子関係なしカテゴリ']],
			['parent_id', ['blogContentId' => 0], []],
			['parent_id', ['blogContentId' => 1, 'excludeParentId' => true], [3 => '親子関係なしカテゴリ']],
			['parent_id', ['blogContentId' => 1, 'ownerId' => 2], []],
			['parent_id', ['blogContentId' => 1, 'ownerId' => 1], [
				1 => 'プレスリリース',
				2 => '　　　└子カテゴリ',
				3 => '親子関係なしカテゴリ']],
			['owner_id', [], [
				1 => 'システム管理',
				2 => 'サイト運営']],
		];
	}

	/**
	 * 同じニックネームのカテゴリがないかチェックする
	 * 同じブログコンテンツが条件
	 *
	 * @dataProvider duplicateBlogCategoryDataProvider
	 */
	public function testDuplicateBlogCategory($check, $expected)
	{
		$this->BlogCategory->validationParams['blogContentId'] = 1;
		$result = $this->BlogCategory->duplicateBlogCategory($check);
		$this->assertEquals($result, $expected);
	}

	public function duplicateBlogCategoryDataProvider()
	{
		return [
			[['id' => 0], true],
			[['id' => 1], false],
			[['name' => 'release'], false],
			[['title' => 'プレスリリース'], false],
			[['title' => '親子関係なしカテゴリ'], false],
			[['title' => 'hoge'], true],
		];
	}

	/**
	 * 関連する記事データをカテゴリ無所属に変更し保存する
	 */
	public function testBeforeDelete()
	{
		$this->BlogCategory->data = ['BlogCategory' => [
			'id' => '1'
		]];
		$this->BlogCategory->delete();

		$BlogPost = ClassRegistry::init('Blog.BlogPost');
		$result = $BlogPost->find('first', [
			'conditions' => ['blog_category_id' => 1]
		]);
		$this->assertEmpty($result);
	}

	/**
	 * カテゴリリストを取得する
	 */
	public function testGetCategoryList()
	{
		$message = '正しくカテゴリリストを取得できません';
		// 正常
		$result = $this->BlogCategory->getCategoryList(1, []);
		$this->assertNotEmpty($result, $message);
		$this->assertEquals($result[0]['BlogCategory']['id'], 1, $message);

		// 存在しないID
		$result = $this->BlogCategory->getCategoryList(0, []);
		$this->assertEmpty($result, $message);

		// option depth 2
		$result = $this->BlogCategory->getCategoryList(1, ['depth' => 2]);
		$this->assertNotEmpty($result[0]['BlogCategory']['children'], $message);

		// option type year
		$result = $this->BlogCategory->getCategoryList(1, ['type' => 'year']);
		$this->assertNotEmpty($result, $message);
		$this->assertEquals($result['2015'][0]['BlogCategory']['id'], 1, $message);

		// option viewCount true
		$result = $this->BlogCategory->getCategoryList(1, ['viewCount' => true]);
		$this->assertEquals($result[0]['BlogCategory']['count'], 2, $message);

		// option limit true
		$result = $this->BlogCategory->getCategoryList(1, ['type' => 'year', 'limit' => 1, 'viewCount' => true]);
		$this->assertEquals($result['2015'][0]['BlogCategory']['count'], 1, $message);
	}

	/**
	 * アクセス制限としてカテゴリの新規追加ができるか確認する
	 */
	public function testHasNewCategoryAddablePermission()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
//		$result = $this->BlogCategory->hasNewCategoryAddablePermission(2, 99);
	}

	/**
	 * 子カテゴリを持っているかどうか
	 */
	public function testHasChild()
	{
		$this->assertFalse($this->BlogCategory->hasChild(2));
		$this->assertTrue($this->BlogCategory->hasChild(1));
	}

	/**
	 * カテゴリ名よりカテゴリを取得する
	 * @dataProvider getByNameDataProvider
	 * @param int $blogCategoryId
	 * @param string $name
	 * @param bool $expects
	 */
	public function testGetByName($blogCategoryId, $name, $expects)
	{
		$result = $this->BlogCategory->getByName($blogCategoryId, $name);
		$this->assertEquals($expects, (bool)$result);
	}

	public function getByNameDataProvider()
	{
		return [
			[1, 'child', true],
			[1, 'hoge', false],
			[2, 'child', false]
		];
	}

}
