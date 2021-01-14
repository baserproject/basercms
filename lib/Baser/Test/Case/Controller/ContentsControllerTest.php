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

App::uses('ContentsController', 'Controller');

/**
 * Class ContentsControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  ContentsController $ContentsController
 */
class ContentsControllerTest extends BaserTestCase
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
	 * コンテンツ一覧
	 */
	public function testAdmin_index()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ゴミ箱内のコンテンツ一覧を表示する
	 */
	public function testAdmin_trash_index()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ゴミ箱のコンテンツを戻す
	 */
	public function testAdmin_ajax_trash_return()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 新規コンテンツ登録（AJAX）
	 */
	public function testAdmin_add()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンテンツ編集
	 */
	public function testAdmin_edit()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * エイリアスを編集する
	 */
	public function testAdmin_edit_alias()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンテンツ削除（論理削除）
	 */
	public function testAdmin_ajax_delete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンテンツ削除（論理削除）
	 */
	public function testAdmin_delete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 公開状態を変更する
	 */
	public function testAdmin_ajax_change_status()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ゴミ箱を空にする
	 */
	public function testAdmin_ajax_trash_empty()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンテンツ表示
	 */
	public function testView()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * リネーム
	 *
	 * 新規登録時の初回リネーム時は、name にも保存する
	 */
	public function testAdmin_ajax_rename()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 並び順を移動する
	 */
	public function testAdmin_ajax_move()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
	 */
	public function testAdmin_exists_content_by_url()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 指定したIDのコンテンツが存在するか確認する
	 * ゴミ箱のものは無視
	 */
	public function testAdmin_ajax_exists()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * プラグイン等と関連付けられていない素のコンテンツをゴミ箱より消去する
	 */
	public function testAdmin_empty()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * サイトに紐付いたフォルダリストを取得
	 */
	public function testAdmin_ajax_get_content_folder_list()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンテンツ情報を取得する
	 */
	public function testAdmin_ajax_contents_info()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * admin_ajax_get_full_url
	 */
	public function testAdmin_ajax_get_full_url()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
