<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Event
 * @since			baserCMS v 4.0.9
 * @license			http://basercms.net/license/index.html
 */

App::uses('CakeEventManager', 'Event');

/**
 * CakeEventManagerTest class
 *
 * @package Baser.Test.Case.Event
 * @property  CakeEventManager $CakeEventManager
 */
class CakeEventManagerTest extends BaserTestCase {

/**
 * set up
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * Dispatches a new event to all configured listeners
 */
	public function testDispatch() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}