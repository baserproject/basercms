<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Event
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcControllerEventListener', 'Event');

/**
 * Class BcControllerEventListenerTest
 *
 * @package Baser.Test.Case.Event
 * @property  BcControllerEventListener $BcControllerEventListener
 */
class BcControllerEventListenerTest extends BaserTestCase
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
	 * 管理システムの現在のサイトをセットする
	 */
	public function testSetAdminCurrentSite()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コントローラーにヘルパーを追加する
	 */
	public function testAddHelper()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
