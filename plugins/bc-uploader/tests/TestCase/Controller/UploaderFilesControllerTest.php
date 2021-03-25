<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Test.Case.Controller
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('UploaderFilesController', 'Uploader.Controller');

/**
 * Class UploaderFilesControllerTest
 *
 * @package Uploader.Test.Case.Controller
 * @property  UploaderFilesController $UploaderFilesController
 */
class UploaderFilesControllerTest extends BaserTestCase
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

	public function testBeforeFilter()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ファイル一覧
	 */
	public function testAdmin_index()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] ファイル一覧を表示
	 */
	public function testAdmin_ajax_list()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] Ajaxファイルアップロード
	 */
	public function testAdmin_ajax_upload()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] サイズを指定して画像タグを取得する
	 */
	public function testAdmin_ajax_image()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 各サイズごとの画像の存在チェックを行う
	 */
	public function testAdmin_ajax_exists_images()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 編集処理
	 */
	public function testAdmin_edit()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * [ADMIN] 削除処理
	 */
	public function testAdmin_delete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 検索ボックスを取得する
	 */
	public function testAdmin_ajax_get_search_box()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 公開期間のチェックを行う
	 */
	public function testView_limited_file()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
