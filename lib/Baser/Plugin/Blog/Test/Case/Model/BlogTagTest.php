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

App::uses('BlogTag', 'Blog.Model');

/**
 * Class BlogTagTest
 *
 * @property BlogTag $BlogTag
 */
class BlogTagTest extends BaserTestCase
{

	public $fixtures = [
		'plugin.blog.Model/BlogTag/BlogPostBlogTagFindCustomPrams',
		'plugin.blog.Model/BlogTag/BlogPostsBlogTagBlogTagFindCustomPrams',
		'plugin.blog.Model/BlogTag/BlogTagBlogTagFindCustomPrams',
		'plugin.blog.Model/BlogTag/BlogContentBlogTagFindCustomPrams',
		'plugin.blog.Model/BlogTag/ContentBlogTagFindCustomPrams',
		'plugin.blog.Model/BlogTag/SiteBlogTagFindCustomPrams',
		'baser.Default.BlogCategory',
		'baser.Default.BlogComment',
		'baser.Default.User',
	];

	public function setUp()
	{
		$this->BlogTag = ClassRegistry::init('Blog.BlogTag');
		parent::setUp();
	}

	public function tearDown()
	{
		unset($this->BlogTag);
		parent::tearDown();
	}

	/*
	 * validate
	 */
	public function test空チェック()
	{

		$this->BlogTag->create([
			'BlogTag' => [
				'name' => ''
			]
		]);

		$this->assertFalse($this->BlogTag->validates());

		$this->assertArrayHasKey('name', $this->BlogTag->validationErrors);
		$this->assertEquals('ブログタグを入力してください。', current($this->BlogTag->validationErrors['name']));
	}

	public function test重複チェック()
	{
		$this->BlogTag->create([
			'BlogTag' => [
				'name' => 'タグ１'
			]
		]);

		$this->assertFalse($this->BlogTag->validates());

		$this->assertArrayHasKey('name', $this->BlogTag->validationErrors);
		$this->assertEquals('既に登録のあるタグです。', current($this->BlogTag->validationErrors['name']));
	}

	public function test正常チェック()
	{
		$this->BlogTag->create([
			'BlogTag' => [
				'name' => 'test'
			]
		]);

		$this->assertTrue($this->BlogTag->validates());
	}

	/**
	 * ブログタグリスト取得
	 *
	 * @param string $type
	 * @param mixed $expected
	 * @param array $options
	 * @dataProvider findCustomParamsDataProvider
	 */
	public function testFindCustomParams($type, $expected, $options = [])
	{
		$result = $this->BlogTag->find('customParams', $options);
		if ($type == 'count') {
			if ($result) {
				$result = count($result);
			}
		} elseif ($type == 'id') {
			if ($result) {
				$result = Hash::extract($result, '{n}.BlogTag.id');
			}
		}
		$this->assertEquals($expected, $result);
	}

	public function findCustomParamsDataProvider()
	{
		return [
			['count', 5, []],
			['count', 2, ['siteId' => 0]],                                        // サイト指定
			['count', 3, ['siteId' => [0, 2]]],                                    // サイト指定（復数）
			['count', 2, ['contentId' => 2]],                                    // ブログコンテンツID指定
			['count', 3, ['contentId' => [2, 3]]],                                // ブログコンテンツID指定（復数）
			['count', 2, ['contentUrl' => ['/blog1/', '/blog2/']]],                // コンテンツURL指定
			['count', 3, ['contentUrl' => ['/blog1/', '/blog2/', '/s/blog3/']]],// コンテンツURL指定（復数）
			['id', [5, 4, 3, 2, 1], ['sort' => 'id', 'direction' => 'DESC']],    // 並び替え指定
		];
	}

	/**
	 * 指定した名称のブログタグ情報を取得する
	 * @dataProvider getByNameDataProvider
	 * @param string $name
	 * @param bool $expects
	 */
	public function testGetByName($name, $expects)
	{
		$result = $this->BlogTag->getByName($name);
		$this->assertEquals($expects, (bool)$result);
	}

	public function getByNameDataProvider()
	{
		return [
			['タグ１', true],
			['タグ２', true],
			['新製品', false],
			['%90V%90%BB%95i', false], // 文字列 新製品 をURLエンコード化
			['hoge', false],
		];
	}

}
