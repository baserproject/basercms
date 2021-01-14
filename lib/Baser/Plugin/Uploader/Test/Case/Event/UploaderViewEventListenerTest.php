<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Test.Case.Event
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('UploaderViewEventListener', 'Uploader.Event');

/**
 * Class UploaderViewEventListenerTest
 *
 * @package Uploader.Test.Case.Event
 * @property  UploaderViewEventListener $UploaderViewEventListener
 */
class UploaderViewEventListenerTest extends BaserTestCase
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

	public function testPagesBeforeRender()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * afterLayout
	 */
	public function testAfterLayout()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * CKEditorのアップローダーを組み込む為のJavascriptを返す
	 */
	public function test__getCkeditorUploaderScript()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 画像タグをモバイル用に置き換える
	 */
	public function test__mobileImageReplace()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * アンカータグのリンク先が画像のものをモバイル用に置き換える
	 */
	public function test__mobileImageAnchorReplace()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
