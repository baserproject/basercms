<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Routing.Filter
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcCacheDispatcher', 'Routing/Filter');

/**
 * Class BcCacheDispatcherTest
 *
 * @package Baser.Test.Case.Routing.Filter
 * @property  BcCacheDispatcher $BcCacheDispatcher
 */
class BcCacheDispatcherTest extends BaserTestCase
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
	 * Checks whether the response was cached and set the body accordingly.
	 */
	public function testBeforeDispatch()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
