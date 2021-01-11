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

App::uses('PluginsController', 'Controller');

/**
 * Class PluginsControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  PluginsController $PluginsController
 */
class PluginsControllerTest extends BaserTestCase
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
	 * プラグインをアップロードしてインストールする
	 */
	public function testAdmin_add()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * プラグインの一覧を表示する
	 */
	public function testAdmin_index()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * baserマーケットのプラグインデータを取得する
	 */
	public function testAdmin_ajax_get_market_plugins()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 並び替えを更新する [AJAX]
	 */
	public function testAdmin_ajax_update_sort()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ファイル削除
	 */
	public function testAdmin_ajax_delete_file()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 登録処理
	 */
	public function testAdmin_install()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * アクセス制限設定を追加する
	 */
	public function test_addPermission()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * データベースをリセットする
	 */
	public function testAdmin_reset_db()
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

}
