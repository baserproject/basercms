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

App::uses('UploadsController', 'Controller');

/**
 * Class UploadsControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  UploadsController $UploadsController
 */
class UploadsControllerTest extends BaserTestCase
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
	 * セッションに保存した一時ファイルを出力する
	 */
	public function testTmp()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
