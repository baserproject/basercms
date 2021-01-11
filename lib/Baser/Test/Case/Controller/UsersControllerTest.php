<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Controller
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('UsersController', 'Controller');

/**
 * Class UsersControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  UsersController $UsersController
 */
class UsersControllerTest extends BaserTestCase
{

	/**
	 * set up
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * beforeFilter
	 */
	public function testBeforeFilter()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ログイン処理を行う
	 * ・リダイレクトは行わない
	 * ・requestActionから呼び出す
	 */
	public function testAdmin_login_exec()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 管理者ログイン画面
	 */
	public function testAdmin_login()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 代理ログイン
	 */
	public function testAdmin_ajax_agent_login()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 代理ログインをしている場合、元のユーザーに戻る
	 */
	public function testBack_agent()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 認証クッキーをセットする
	 */
	public function testSetAuthCookie()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 管理者ログアウト
	 */
	public function testAdmin_logout()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ユーザーリスト
	 */
	public function testAdmin_index()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ユーザー情報登録
	 */
	public function testAdmin_add()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ユーザー情報編集
	 */
	public function testAdmin_edit()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ユーザー情報削除　(ajax)
	 */
	public function testAdmin_ajax_delete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ユーザー情報削除
	 */
	public function testAdmin_delete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ログインパスワードをリセットする
	 * 新しいパスワードを生成し、指定したメールアドレス宛に送信する
	 */
	public function testAdmin_reset_password()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
