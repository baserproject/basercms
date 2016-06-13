<?php

/**
 * test for BlogComment
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS BlogComments Community <http://sites.google.com/site/baserBlogComments/>
 * @package         Feed.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS BlogComments Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.1.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('BlogComment', 'Blog.Model');

class BlogCommentTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.BlogComment',
	);

	public function setUp() {
		$this->BlogComment = ClassRegistry::init('Blog.BlogComment');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->BlogComment);
		parent::tearDown();
	}

/*
 * validate
 */
	public function test空チェック() {
		$this->BlogComment->create(array(
			'BlogComment' => array(
				'name' => '',
				'message' => '',
			)
		));

		$this->assertFalse($this->BlogComment->validates());

		$this->assertArrayHasKey('name', $this->BlogComment->validationErrors);
		$this->assertEquals('お名前を入力してください。', current($this->BlogComment->validationErrors['name']));

		$this->assertArrayHasKey('message', $this->BlogComment->validationErrors);
		$this->assertEquals('コメントを入力してください。', current($this->BlogComment->validationErrors['message']));
	}

	public function test桁数チェック異常系() {
		$this->BlogComment->create(array(
			'BlogComment' => array(
				'name' => '123456789012345678901234567890123456789012345678901',
				'email' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456@example.com',
				'url' => 'http://example.com/1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
			)
		));
		$this->assertFalse($this->BlogComment->validates());

		$this->assertArrayHasKey('name', $this->BlogComment->validationErrors);
		$this->assertEquals('お名前は50文字以内で入力してください。', current($this->BlogComment->validationErrors['name']));

		$this->assertArrayHasKey('email', $this->BlogComment->validationErrors);
		$this->assertEquals('Eメールは255文字以内で入力してください。', current($this->BlogComment->validationErrors['email']));


		$this->assertArrayHasKey('url', $this->BlogComment->validationErrors);
		$this->assertEquals('URLは255文字以内で入力してください。', current($this->BlogComment->validationErrors['url']));
	}

	public function test桁数チェック正常系() {
		$this->BlogComment->create(array(
			'BlogComment' => array(
				'name' => '12345678901234567890123456789012345678901234567890',
				'email' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678@example.com',
				'url' => 'http://example.com/123456789012345678901234567890123456789012345678901234567890123456789012345678901234567567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456'
			)
		));
		$this->assertTrue($this->BlogComment->validates());
	}

	public function testその他異常系() {
		// 形式チェック
		$this->BlogComment->create(array(
			'BlogComment' => array(
				'email' => 'hoge',
				'url' => 'hoge'
			)
		));

		$this->assertFalse($this->BlogComment->validates());

		$this->assertArrayHasKey('email', $this->BlogComment->validationErrors);
		$this->assertEquals('Eメールの形式が不正です。', current($this->BlogComment->validationErrors['email']));

		$this->assertArrayHasKey('url', $this->BlogComment->validationErrors);
		$this->assertEquals('URLの形式が不正です。', current($this->BlogComment->validationErrors['url']));
	}


/**
 * コメントを追加する
 */
	public function testAdd() {
		$data = array('BlogComment' => array(
			'name' => 'test_name<',
			'email' => '-@example.com',
			'url' => 'http://example.com/-',
			'message' => 'test_message<',
		));
		$this->BlogComment->add($data, 1, 1, false);

		$result = $this->BlogComment->find('first', array(
			'conditions' => array('id' => $this->BlogComment->getLastInsertID())
		));

		$message = 'コメントを正しく追加できません';
		$this->assertEquals($result['BlogComment']['name'], 'test_name&lt;', $message);
		$this->assertEquals($result['BlogComment']['email'], '-@example.com', $message);
		$this->assertEquals($result['BlogComment']['url'], 'http://example.com/-', $message);
		$this->assertEquals($result['BlogComment']['message'], 'test_message&lt;', $message);
		$this->assertEquals($result['BlogComment']['no'], 2, $message);
		$this->assertEquals($result['BlogComment']['status'], 1, $message);

	}

}
