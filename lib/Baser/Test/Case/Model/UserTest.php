<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
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
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
	);

	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('User');
	}

	public function tearDown() {
		unset($this->User);
		parent::tearDown();
	}

/**
 * validate
 */
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

	public function test半角英数チェック異常系() {
		$this->User->create(array(
			'User' => array(
				'name' => '１２３ａｂｃ',
				'password' => '１２３ａｂｃ',
			)
		));
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('name', $this->User->validationErrors);
		$this->assertEquals('アカウント名は半角英数字とハイフン、アンダースコアのみで入力してください。', current($this->User->validationErrors['name']));
		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。', current($this->User->validationErrors['password']));

	}

	public function test半角英数チェック正常系() {
		$this->User->create(array(
			'User' => array(
				'name' => '123abc',
				'password' => '123abc',
			)
		));
		$this->assertTrue($this->User->validates());
	}

	public function testパスワード記号正常系() {
		$this->User->create(array(
			'User' => array(
				'password' => '. _-:/()#,@[]+=&;{}!$*',
			)
		));
		$this->assertTrue($this->User->validates());
	}

	public function testパスワード記号異常系() {
		$this->User->create(array(
			'User' => array(
				'password' => '. _-:/()#,@[]+=&;{}!$*^~"',
			)
		));
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。', current($this->User->validationErrors['password']));
	}

	public function test既存ユーザチェック異常系() {
		$this->User->create(array(
			'User' => array(
				'name' => 'basertest',
			)
		));
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('name', $this->User->validationErrors);
		$this->assertEquals('既に登録のあるアカウント名です。', current($this->User->validationErrors['name']));

	}

	public function test既存ユーザチェック正常系() {
		$this->User->create(array(
			'User' => array(
				'name' => 'hoge',
			)
		));
		$this->assertTrue($this->User->validates());
	}

	public function testメールアドレス形式チェック異常系() {
		$this->User->create(array(
			'User' => array(
				'email' => 'abc.co.jp',
			)
		));
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('email', $this->User->validationErrors);
		$this->assertEquals('Eメールの形式が不正です。', current($this->User->validationErrors['email']));
	}

	public function testメールアドレス形式チェック正常系() {
		$this->User->create(array(
			'User' => array(
				'email' => 'abc@co.jp',
			)
		));
		$this->assertTrue($this->User->validates());
	}


/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getControlSourceDataProvider
 */
	public function testGetControlSource($field, $expected, $message = null) {
		$result = $this->User->getControlSource($field);
		$this->assertEquals($expected, $result, $message);
	}

  public function getControlSourceDataProvider() {
    return array(
      array('user_group_id', array(1 => 'システム管理', 2 => 'サイト運営'), 'コントロールソースを取得する取得できません'),
      array('hoge', false, '存在しないフィールド名です'),
    );
  }

/**
 * ユーザーリストを取得する
 * 条件を指定する場合は引数を指定する
 * 
 * @param array $conditions 取得条件
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getUserListDataProvider
 */
	public function testGetUserList($conditions, $expected, $message = null) {
		$result = $this->User->getUserList($conditions);
		$this->assertEquals($expected, $result, $message);
	}

  public function getUserListDataProvider() {
    return array(
      array(array(), array(1 => 'basertest', 2 => 'basertest2'), 'コントロールソースを取得する取得できません'),
      array(array('User.id' => 1), array(1 => 'basertest'), 'コントロールソースを取得する取得できません'),
    );
  }


/**
 * フォームの初期値を設定する
 */
	public function testGetDefaultValue() {
		$result = $this->User->getDefaultValue();
		$expected = array('User' => array('user_group_id' => 1));
		$this->assertEquals($expected, $result, 'フォームの初期値が正しくありません');
	}


/**
 * afterFind
 *
 * @param array 結果セット
 * @param array $primary
 */
	public function testAfterFind() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		// $results = $this->User->find('all');
		// $result = $this->User->afterFind($results, true);
		// $this->assertEquals($expected, $result, $message);
	}

/**
 * 取得結果を変換する
 * HABTM対応
 *
 * @param array 結果セット
 */
	public function testConvertResults() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ユーザーが許可されている認証プレフィックスを取得する
 *
 * @param string $userName ユーザーの名前
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getAuthPrefixDataProvider
 */
	public function testGetAuthPrefix($userName, $expected, $message = null) {
		$result = $this->User->getAuthPrefix($userName);
		$this->assertEquals($expected, $result, $message);
	}

  public function getAuthPrefixDataProvider() {
    return array(
      array('basertest', 'admin', 'ユーザーの認証プレフィックスを正しく取得できません'),
      array('basertest2', 'operator', 'ユーザーの認証プレフィックスを正しく取得できません'),
    );
  }

/**
 * beforeSave
 * 
 * @param type $options
 * @return boolean
 */
	public function testBeforeSave() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * afterSave
 * 
 * @param boolean $created 
 */
	public function testAfterSave() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * よく使う項目の初期データをユーザーに適用する
 * 
 * @param type $userId ユーザーID
 * @param type $userGroupId ユーザーグループID
 * @param array $expected 期待値
 * @param array $expectedLastData Favoriteに最後に挿入されたデータ
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider applyDefaultFavoritesDataProvider
 */
	public function testApplyDefaultFavorites($userId, $userGroupId, $expected, $expectedLastData, $message = null) {
		$result = $this->User->applyDefaultFavorites($userId, $userGroupId);

		$LastId = $this->User->Favorite->getLastInsertID();
		$LastData = $this->User->Favorite->find('all',array(
			'conditions' => array('Favorite.id' => $LastId),
			'fields' => array('Favorite.name'),
			'recursive' => 0,
			)
		);

		$this->assertEquals($expected, $result, $message);
		$this->assertEquals($expectedLastData, $LastData[0]['Favorite']['name'], $message);
	}

  public function applyDefaultFavoritesDataProvider() {
    return array(
      array(1, 1, true, 'クレジット', 'よく使う項目の初期データをユーザーに正しく適用できません'),
      array(2, 1, true, 'クレジット', 'よく使う項目の初期データをユーザーに正しく適用できません'),
      array(1, 2, true, 'コメント一覧', 'よく使う項目の初期データをユーザーに正しく適用できません'),
      array(2, 2, true, 'コメント一覧', 'よく使う項目の初期データをユーザーに正しく適用できません'),
    );
  }

/**
 * ユーザーに関連するよく使う項目を削除する
 */
	public function testDeleteFavorites() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$this->User->Favorite->deleteAll(1);
		$result = $this->User->Favorite->find('all');
		$expected = array();
		$this->assertEquals($expected, $result, 'ユーザーに関連するよく使う項目を削除できません');
	}
}
