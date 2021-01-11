<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */
App::uses('User', 'Model');

/**
 * Class UserTest
 *
 * class NonAssosiationUser extends User {
 *    public $name = 'User';
 *    public $belongsTo = [];
 *    public $hasMany = [];
 * }
 *
 * @package Baser.Test.Case.Model
 */
class UserTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
	];

	public function setUp()
	{
		parent::setUp();
		$this->User = ClassRegistry::init('User');
	}

	public function tearDown()
	{
		unset($this->User);
		parent::tearDown();
	}

	/**
	 * validate
	 */
	public function test必須チェック()
	{
		$this->User->create([
			'User' => [
				'name' => '',
				'real_name_1' => '',
				'real_name_2' => '',
				'password' => '',
				'email' => '',
				'user_group_id' => ''
			]
		]);
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

	public function test桁数チェック異常系()
	{
		$this->User->create([
			'User' => [
				'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'real_name_1' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０1',
				'real_name_2' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０1',
				'password' => 'abcde',
				'email' => '',
			]
		]);
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('name', $this->User->validationErrors);
		$this->assertEquals('アカウント名は255文字以内で入力してください。', current($this->User->validationErrors['name']));
		$this->assertArrayHasKey('real_name_1', $this->User->validationErrors);
		$this->assertEquals('名前[姓]は50文字以内で入力してください。', current($this->User->validationErrors['real_name_1']));
		$this->assertArrayHasKey('real_name_2', $this->User->validationErrors);
		$this->assertEquals('名前[名]は50文字以内で入力してください。', current($this->User->validationErrors['real_name_2']));
		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは6文字以上で入力してください。', current($this->User->validationErrors['password']));

		$this->User->create([
			'User' => [
				'password' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'email' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789@123.jp',
			]
		]);
		$this->assertFalse($this->User->validates());
		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは255文字以内で入力してください。', current($this->User->validationErrors['password']));
		$this->assertArrayHasKey('email', $this->User->validationErrors);
		$this->assertEquals('Eメールは255文字以内で入力してください。', current($this->User->validationErrors['email']));
	}

	public function test桁数チェック正常系()
	{
		$this->User->create([
			'User' => [
				'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'real_name_1' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０',
				'real_name_2' => '１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０１２３４５６７８９０',
				'password' => 'abcdef',
				'email' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789@12.jp',
			],
		]);

		$this->assertTrue($this->User->validates());
	}

	public function test半角英数チェック異常系()
	{
		$this->User->create([
			'User' => [
				'name' => '１２３ａｂｃ',
				'password' => '１２３ａｂｃ',
			]
		]);
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('name', $this->User->validationErrors);
		$this->assertEquals('アカウント名は半角英数字とハイフン、アンダースコアのみで入力してください。', current($this->User->validationErrors['name']));
		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。', current($this->User->validationErrors['password']));

	}

	public function test半角英数チェック正常系()
	{
		$this->User->create([
			'User' => [
				'name' => '123abc',
				'password' => '123abc',
			]
		]);
		$this->assertTrue($this->User->validates());
	}

	public function testパスワード記号正常系()
	{
		$this->User->create([
			'User' => [
				'password' => '. _-:/()#,@[]+=&;{}!$*',
			]
		]);
		$this->assertTrue($this->User->validates());
	}

	public function testパスワード記号異常系()
	{
		$this->User->create([
			'User' => [
				'password' => '. _-:/()#,@[]+=&;{}!$*^~"',
			]
		]);
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('password', $this->User->validationErrors);
		$this->assertEquals('パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。', current($this->User->validationErrors['password']));
	}

	public function test既存ユーザチェック異常系()
	{
		$this->User->create([
			'User' => [
				'name' => 'basertest',
			]
		]);
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('name', $this->User->validationErrors);
		$this->assertEquals('既に登録のあるアカウント名です。', current($this->User->validationErrors['name']));

	}

	public function test既存ユーザチェック正常系()
	{
		$this->User->create([
			'User' => [
				'name' => 'hoge',
			]
		]);
		$this->assertTrue($this->User->validates());
	}

	public function testメールアドレス形式チェック異常系()
	{
		$this->User->create([
			'User' => [
				'email' => 'abc.co.jp',
			]
		]);
		$this->assertFalse($this->User->validates());

		$this->assertArrayHasKey('email', $this->User->validationErrors);
		$this->assertEquals('Eメールの形式が不正です。', current($this->User->validationErrors['email']));
	}

	public function testメールアドレス形式チェック正常系()
	{
		$this->User->create([
			'User' => [
				'email' => 'abc@co.jp',
			]
		]);
		$this->assertTrue($this->User->validates());
	}

	/**
	 * validates
	 */
	public function testValidates()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getControlSourceDataProvider
	 */
	public function testGetControlSource($field, $expected, $message = null)
	{
		$result = $this->User->getControlSource($field);
		$this->assertEquals($expected, $result, $message);
	}

	public function getControlSourceDataProvider()
	{
		return [
			['user_group_id', [1 => 'システム管理', 2 => 'サイト運営'], 'コントロールソースを取得する取得できません'],
			['hoge', false, '存在しないフィールド名です'],
		];
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
	public function testGetUserList($conditions, $expected, $message = null)
	{
		$result = $this->User->getUserList($conditions);
		$this->assertEquals($expected, $result, $message);
	}

	public function getUserListDataProvider()
	{
		return [
			[[], [1 => 'basertest', 2 => 'basertest2'], 'コントロールソースを取得する取得できません'],
			[['User.id' => 1], [1 => 'basertest'], 'コントロールソースを取得する取得できません'],
		];
	}


	/**
	 * フォームの初期値を設定する
	 */
	public function testGetDefaultValue()
	{
		$result = $this->User->getDefaultValue();
		$expected = ['User' => ['user_group_id' => 1]];
		$this->assertEquals($expected, $result, 'フォームの初期値が正しくありません');
	}

	/**
	 * ユーザーが許可されている認証プレフィックスを取得する
	 *
	 * @param string $userName ユーザーの名前
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getAuthPrefixDataProvider
	 */
	public function testGetAuthPrefix($userName, $expected, $message = null)
	{
		$result = $this->User->getAuthPrefix($userName);
		$this->assertEquals($expected, $result, $message);
	}

	public function getAuthPrefixDataProvider()
	{
		return [
			['basertest', 'admin', 'ユーザーの認証プレフィックスを正しく取得できません'],
			['basertest2', 'operator', 'ユーザーの認証プレフィックスを正しく取得できません'],
		];
	}

	/**
	 * beforeSave
	 *
	 * @param type $options
	 * @return boolean
	 */
	public function testBeforeSave()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * afterSave
	 *
	 * @param boolean $created
	 */
	public function testAfterSave()
	{
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
	public function testApplyDefaultFavorites($userId, $userGroupId, $expected, $expectedLastData, $message = null)
	{
		$result = $this->User->applyDefaultFavorites($userId, $userGroupId);

		$LastId = $this->User->Favorite->getLastInsertID();
		$LastData = $this->User->Favorite->find('all', [
				'conditions' => ['Favorite.id' => $LastId],
				'fields' => ['Favorite.name'],
				'recursive' => 0,
			]
		);

		$this->assertEquals($expected, $result, $message);
		$this->assertEquals($expectedLastData, $LastData[0]['Favorite']['name'], $message);
	}

	public function applyDefaultFavoritesDataProvider()
	{
		return [
			[1, 1, true, 'クレジット', 'よく使う項目の初期データをユーザーに正しく適用できません'],
			[2, 1, true, 'クレジット', 'よく使う項目の初期データをユーザーに正しく適用できません'],
			[1, 2, true, 'コメント一覧', 'よく使う項目の初期データをユーザーに正しく適用できません'],
			[2, 2, true, 'コメント一覧', 'よく使う項目の初期データをユーザーに正しく適用できません'],
		];
	}

	/**
	 * ユーザーに関連するよく使う項目を削除する
	 */
	public function testDeleteFavorites()
	{
		$user = $this->User->find('first', ['conditions' => ['User.id' => 1]]);
		$this->assertTrue(isset($user['Favorite'][0]['id']), 'ユーザーに関連するよく使う項目の削除対象がありません。');
		$this->User->deleteFavorites(1);
		$user = $this->User->find('first', ['conditions' => ['User.id' => 1]]);
		$this->assertFalse(isset($user['Favorite'][0]['id']), 'ユーザーに関連するよく使う項目を削除できません。');
	}
}
