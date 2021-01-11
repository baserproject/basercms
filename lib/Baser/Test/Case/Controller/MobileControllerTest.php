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

App::uses('MobileController', 'Controller');

/**
 * Class MobileControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  MobileController $MobileController
 */
class MobileControllerTest extends BaserTestCase
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
	 * モバイル GoogleAnalytics 用 ライブラリを読み込む
	 */
	public function testGa()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
