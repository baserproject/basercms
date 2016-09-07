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
App::uses('Permission', 'Model');

/**
 * PermissionTest class
 * 
 * class NonAssosiationPermission extends Permission {
 *  public $name = 'Permission';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class PermissionTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Page',
		'baser.Model.Permission.PermissionPermissionModel',
		'baser.Default.UserGroup',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.Content',
		'baser.Default.User'
	);

	public function setUp() {
		parent::setUp();
		$this->Permission = ClassRegistry::init('Permission');
	}

	public function tearDown() {
		unset($this->Permission);
		parent::tearDown();
	}

/**
 * validate
 */
	public function test必須チェック() {
		$this->Permission->create(array(
			'Permission' => array(
				'name' => '',
				'url' => '',
			)
		));
		$this->assertFalse($this->Permission->validates());
		$this->assertArrayHasKey('name', $this->Permission->validationErrors);
		$this->assertEquals('設定名を入力してください。', current($this->Permission->validationErrors['name']));
		$this->assertArrayHasKey('user_group_id', $this->Permission->validationErrors);
		$this->assertEquals('ユーザーグループを選択してください。', current($this->Permission->validationErrors['user_group_id']));
		$this->assertArrayHasKey('url', $this->Permission->validationErrors);
		$this->assertEquals('設定URLを入力してください。', current($this->Permission->validationErrors['url']));
	}

	public function test桁数チェック正常系() {
		$this->Permission->create(array(
			'Permission' => array(
				'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'user_group_id' => '1',
				'url' => '/admin/12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
			)
		));
		$this->assertTrue($this->Permission->validates());
	}

	public function test桁数チェック異常系() {
		$this->Permission->create(array(
			'Permission' => array(
				'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'user_group_id' => '1',
				'url' => '/admin/1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
			)
		));
		$this->assertFalse($this->Permission->validates());
		$this->assertArrayHasKey('name', $this->Permission->validationErrors);
		$this->assertEquals('設定名は255文字以内で入力してください。', current($this->Permission->validationErrors['name']));
		$this->assertArrayHasKey('url', $this->Permission->validationErrors);
		$this->assertEquals('設定URLは255文字以内で入力してください。', current($this->Permission->validationErrors['url']));
	}

	public function testアクセス拒否チェック異常系() {
		$this->Permission->create(array(
			'Permission' => array(
				'user_group_id' => '1',
				'url' => '/index',
			)
		));
		$this->assertFalse($this->Permission->validates());
		$this->assertArrayHasKey('url', $this->Permission->validationErrors);
		$this->assertEquals('アクセス拒否として設定できるのは認証ページだけです。', current($this->Permission->validationErrors['url']));
	}
	public function testアクセス拒否チェック正常系() {
		$this->Permission->create(array(
			'Permission' => array(
				'user_group_id' => '1',
				'url' => '/admin/index',
			)
		));
		$this->assertTrue($this->Permission->validates());
	}


/**
 * 設定をチェックする
 *
 * @param array $check チェックするURL
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider checkUrlDataProvider
 */
	public function testCheckUrl($check, $expected, $message = null) {
		$result = $this->Permission->checkUrl($check);
		$this->assertEquals($expected, $result, $message);
	}

	public function checkUrlDataProvider() {
		return array(
			array(array(1), false, '適当なURLです'),
			array(array('hoge'), false, '適当なURLです'),
			array(array('/hoge'), false, '適当なURLです'),
			array(array('hoge/'), false, '適当なURLです'),
			array(array('/hoge/'), false, '適当なURLです'),
			array(array('/hoge/*'), false, '適当なURLです'),
			array(array('admin'), true, '権限の必要なURLです'),
			array(array('/admin'), true, '権限の必要なURLです'),
			array(array('admin/'), true, '権限の必要なURLです'),
			array(array('admin/*'), true, '権限の必要なURLです'),
			array(array('/admin/*'), true, '権限の必要なURLです'),
			array(array('/admin/dashboard/'), true, '権限の必要なURLです'),
			array(array('/admin/dashboard/*'), true, '権限の必要なURLです'),
		);
	}


/**
 * 認証プレフィックスを取得する
 *
 * @param int $id PermissionのID
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getAuthPrefixDataProvider
 */
	public function testGetAuthPrefix($id, $expected, $message = null) {
		$result = $this->Permission->getAuthPrefix($id);
		$this->assertEquals($expected, $result, $message);
	}

	public function getAuthPrefixDataProvider() {
		return array(
			array(1, 'operator', 'プレフィックスが一致しません'),
			array(16, 'admin', 'プレフィックスが一致しません'),
			array(99, false, '存在しないユーザーグループです'),
		);
	}

/**
 * 初期値を取得する
 */
	public function testGetDefaultValue() {
		$result = $this->Permission->getDefaultValue();
		$expected = array(
			'Permission' => array(
				'auth' => 0,
				'status' => 1
			)
		);
		$this->assertEquals($expected, $result, '初期値が正しくありません');
	}

/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getControlSourceDataProvider
 */
	public function testGetControlSource($field, $expected, $message = null) {
		$result = $this->Permission->getControlSource($field);
		$this->assertEquals($expected, $result, $message);
	}

	public function getControlSourceDataProvider() {
		return array(
			array('user_group_id', array(2 => 'サイト運営'), '$controlSources["user_group_id"]が取得できません'),
			array('auth', array(0 => '不可',1 => '可'), '$controlSources["auth"]が取得できません'),
			array('hoge', false, '存在しないフィールドです'),
		);
	}

/**
 * beforeSave
 * 
 * @param array $url saveするurl
 * @param array $expectedUrl 期待するurl
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider beforeSaveDataProvider
 */
	public function testBeforeSave($url, $expectedUrl, $message = null) {
		$this->Permission->data = array(
			'Permission' => array(
				'url' => $url,
			)
		);
		$this->Permission->beforeSave();
		$result = $this->Permission->data;

		$expected = array(
			'Permission' => array(
				'url' => $expectedUrl
			)
		);

		$this->assertEquals($expected, $result, $message);
	}

	public function beforeSaveDataProvider() {
		return array(
			array('hoge', '/hoge', 'urlが絶対パスになっていません'),
			array('/hoge', '/hoge', 'urlが絶対パスになっていません'),

		);
	}

/**
 * 権限チェックを行う
 * 
 * @param array $url
 * @param string $userGroupId
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider checkDataProvider
 */
	public function testCheck($url, $userGroupId, $expected, $message = null) {
		$result = $this->Permission->check($url, $userGroupId);
		$this->assertEquals($expected, $result, $message);
	}

	public function checkDataProvider() {
		return array(
			array('hoge', 1, true, 'システム管理者は権限をもっています'),
			array('hoge', 2, true, 'サイト運営者は権限をもっています'),
			array('/admin/*', 1, true, 'サイト運営者は権限をもっています'),
			array('/admin/*', 2, false, 'サイト運営者は権限をもっていません'),
			array('/admin/', 2, true, 'サイト運営者は権限をもっています'),
			array('/admin/dashboard', 2, false, 'サイト運営者は権限をもっていません'),
			array('/admin/dashboard/', 2, true, 'サイト運営者は権限をもっています'),
		);
	}

/**
 * アクセス制限データをコピーする
 * 
 * @param int $id
 * @param array $data
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider copyDataProvider
 */
	public function testCopy($id, $data, $expected, $message = null) {
		$result = $this->Permission->copy($id, $data);
		$this->assertEquals($expected, $result['Permission']['name'], $message);
	}

	public function copyDataProvider() {
		return array(
			array(1, array(), 'システム管理_copy', 'id指定でデータをコピーできません'),
			array(null,
				array('Permission' => array(
						'user_group_id' => '3',
						'name' => 'hoge',
					)
				),
				'hoge', 'data指定でデータをコピーできません'),
			array(99, array(), false, '存在しないIDです'),
			array(null, array('Permission'=>array('user_group_id' => '', 'name' => '')), false, 'コピーできないデータです'),
		);
	}

}
