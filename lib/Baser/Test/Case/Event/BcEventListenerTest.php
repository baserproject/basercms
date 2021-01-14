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

App::uses('BcEventListener', 'Event');

/**
 * Class BcEventListenerTest
 *
 * @package Baser.Test.Case.Event
 * @property  BcEventListener $BcEventListener
 */
class BcEventListenerTest extends BaserTestCase
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
	 * implementedEvents
	 */
	public function testImplementedEvents()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 指定した文字列が現在のアクションとしてみなされるかどうか判定する
	 */
	public function testIsAction()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
