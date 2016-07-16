<?php

/**
 * test for BlogTag
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS BlogTags Community <http://sites.google.com/site/baserBlogTags/>
 * @package         Feed.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS BlogTags Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.1.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('BlogTag', 'Blog.Model');

class BlogTagTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.BlogTag',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogPost',
	);

	public function setUp() {
		$this->BlogTag = ClassRegistry::init('Blog.BlogTag');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->BlogTag);
		parent::tearDown();
	}

/*
 * validate
 */
	public function test空チェック() {

		$this->BlogTag->create(array(
			'BlogTag' => array(
				'name' => ''
			)
		));

		$this->assertFalse($this->BlogTag->validates());

		$this->assertArrayHasKey('name', $this->BlogTag->validationErrors);
		$this->assertEquals('ブログタグを入力してください。', current($this->BlogTag->validationErrors['name']));
	}

	public function test重複チェック() {
		$this->BlogTag->create(array(
			'BlogTag' => array(
				'name' => '新製品'
			)
		));

		$this->assertFalse($this->BlogTag->validates());

		$this->assertArrayHasKey('name', $this->BlogTag->validationErrors);
		$this->assertEquals('既に登録のあるタグです。', current($this->BlogTag->validationErrors['name']));
	}

	public function test正常チェック() {
		$this->BlogTag->create(array(
			'BlogTag' => array(
				'name' => 'test'
			)
		));

		$this->assertTrue($this->BlogTag->validates());
	}
}
