<?php

/**
 * ユーザモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('User', 'Model');

/**
 * UserTest class
 * 
 * class NonAssosiationUser extends User {
 *	public $name = 'User';
 *	public $belongsTo = array();
 *	public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class UserTest extends BaserTestCase {

	public $fixtures = array(
		'baser.User.User',
		'baser.UserGroup.UserGroup',
		'baser.Favorite.Favorite',
	);

	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('User');
	}

	public function tearDown() {
		unset($this->User);
		parent::tearDown();
	}

	public function test必須チェック() {
		$this->User->create(array(
			'User' => array(
				'name' => '',
				'real_name_1' => '',
				'real_name_2' => '',
				'password' => '',
				'email' => '',
				'user_group_id' => ''
			)
		));
		$this->assertFalse($this->User->validates());
		$this->assertArrayHasKey('name', $this->User->validationErrors);
		$this->assertEquals('アカウント名を入力してください。', current($this->User->validationErrors['name']));
		$this->assertArrayHasKey('real_name_1', $this->User->validationErrors);
		$this->assertEquals('名前[姓]を入力してください。', current($this->User->validationErrors['real_name_1']));
		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは6文字以上で入力してください。', current($this->User->validationErrors['password']));
		$this->assertArrayHasKey('user_group_id', $this->User->validationErrors);
		$this->assertEquals('グループを選択してください。', current($this->User->validationErrors['user_group_id']));
	}

	public function test桁数チェック異常系() {
		$this->User->create(array(
			'User' => array(
				'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'real_name_1' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０1',
				'real_name_2' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０1',
				'password' => 'abcde',
				'email' => '',
			)
		));
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('name', $this->User->validationErrors);
		$this->assertEquals('アカウント名は255文字以内で入力してください。', current($this->User->validationErrors['name']));
		$this->assertArrayHasKey('real_name_1', $this->User->validationErrors);
		$this->assertEquals('名前[姓]は50文字以内で入力してください。', current($this->User->validationErrors['real_name_1']));
		$this->assertArrayHasKey('real_name_2', $this->User->validationErrors);
		$this->assertEquals('名前[名]は50文字以内で入力してください。', current($this->User->validationErrors['real_name_2']));
		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは6文字以上で入力してください。', current($this->User->validationErrors['password']));

		$this->User->create(array(
			'User' => array(
				'password' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'email' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789@123.jp',
			)
		));
		$this->assertFalse($this->User->validates());
		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは255文字以内で入力してください。', current($this->User->validationErrors['password']));
		$this->assertArrayHasKey('email', $this->User->validationErrors);
		$this->assertEquals('Eメールは255文字以内で入力してください。', current($this->User->validationErrors['email']));
	}

	public function test桁数チェック正常系() {
		$this->User->create(array(
			'User' => array(
				'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'real_name_1' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０',
				'real_name_2' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０',
				'password' => 'abcdef',
				'email' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789@12.jp',
			),
		));

		$this->assertTrue($this->User->validates());
	}

	public function test半角英数チェック() {
		
	}

	public function test既存ユーザチェック() {
		
	}

}
