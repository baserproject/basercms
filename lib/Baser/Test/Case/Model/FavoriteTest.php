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
App::uses('Favorite', 'Model');
App::uses('SessionComponent', 'Controller/Component');
App::uses('ComponentCollection', 'Controller/Component');
App::uses('CookieComponent', 'Controller/Component');

/**
 * Class FavoriteTest
 *
 * class NonAssosiationFavorite extends Favorite {
 *  public $name = 'Favorite';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 *
 * @package Baser.Test.Case.Model
 */
class FavoriteTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Permission',
	];

	public $components = ["Auth", "Cookie", "Session"];

	public function setUp()
	{
		parent::setUp();
		$this->Favorite = ClassRegistry::init('Favorite');
	}

	public function tearDown()
	{
		session_unset();
		unset($this->Favorite);
		parent::tearDown();
	}

	/**
	 * 偽装ログイン処理
	 *
	 * @param $id ユーザーIDとユーザーグループID
	 * - 1 システム管理者
	 * - 2 サイト運営
	 */
	public function login($id)
	{
		$this->Favorite->setSession(new SessionComponent(new ComponentCollection()));
		$prefix = BcUtil::authSessionKey('admin');
		$this->Favorite->_Session->write('Auth.' . $prefix . '.id', $id);
		$this->Favorite->_Session->write('Auth.' . $prefix . '.user_group_id', $id);
	}

	/**
	 * validate
	 */
	public function test権限チェック異常系()
	{
		$this->Favorite->create([
			'Favorite' => [
				'url' => '/admin/hoge',
			]
		]);

		$this->login(2);

		$this->assertFalse($this->Favorite->validates());
		$this->assertArrayHasKey('url', $this->Favorite->validationErrors);
		$this->assertEquals('このURLの登録は許可されていません。', current($this->Favorite->validationErrors['url']));
	}

	public function test権限チェックシステム管理者正常系()
	{
		$this->Favorite->create([
			'Favorite' => [
				'url' => '/admin/hoge',
			]
		]);

		$this->login(1);

		$this->assertTrue($this->Favorite->validates());
	}

	public function test権限チェックサイト運営者正常系()
	{
		$this->Favorite->create([
			'Favorite' => [
				'url' => '/hoge',
			]
		]);

		$this->login(2);

		$this->assertTrue($this->Favorite->validates());
	}

	/**
	 * セッションをセットする
	 */
	public function testSetSession()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * アクセス権があるかチェックする
	 */
	public function testIsPermitted()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
