<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Case.Controller
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('MailController', 'Mail.Controller');
App::uses('MailMessage', 'Mail.Model');

class MailControllerTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.SiteConfig',
		'baser.Default.Page',
		'plugin.mail.Default/MailMessage',
	];

	public function setUp()
	{
		parent::setUp();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * beforeFilter.
	 */
	public function testBeforeFilter()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * beforeRender
	 */
	public function testBeforeRender()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [test_index description]
	 * @return [type] [description]
	 */
	public function test_index()
	{
		// $result = $this->testAction('/contact/index');
	}

	/**
	 * [PUBIC] フォームを表示する
	 *
	 * @return void
	 */
	public function testIndex()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [PUBIC] データの確認画面を表示
	 */
	public function testConfirm()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [PUBIC] データ送信
	 */
	public function testSubmit()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [private] 確認画面から戻る
	 */
	public function test_back()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 認証用のキャプチャ画像を表示する
	 */
	public function testCaptcha()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
