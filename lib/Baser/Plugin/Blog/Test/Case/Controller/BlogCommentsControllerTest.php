<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case.Controller
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('BlogCommentsController', 'Blog.Controller');

/**
 * Class BlogCommentsControllerTest
 *
 * @package Blog.Test.Case.Controller
 * @property  BlogCommentsController $BlogCommentsController
 */
class BlogCommentsControllerTest extends BaserTestCase
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
	 * beforeRender
	 */
	public function testBeforeRender()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ブログを一覧表示する
	 */
	public function testAdmin_index()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 */
	public function testAdmin_ajax_delete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 無効状態にする（AJAX）
	 */
	public function testAdmin_ajax_unpublish()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 有効状態にする（AJAX）
	 */
	public function testAdmin_ajax_publish()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [AJAX] ブログコメントを登録する
	 */
	public function testAdd()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [AJAX] ブログコメントを登録する
	 */
	public function testSmartphone_add()
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

	/**
	 * コメント送信用にAjax経由でトークンを取得するアクション
	 */
	public function testGet_token()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
