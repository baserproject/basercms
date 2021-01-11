<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.Test.Case
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

class FeedAllHelpersTest extends CakeTestSuite
{

	/**
	 * Suite define the tests for this suite
	 *
	 * @return CakeTestSuite
	 */
	public static function suite()
	{
		$suite = new CakeTestSuite('All Feed Helpers tests');
		$suite->addTestDirectory(dirname(__FILE__) . DS . 'View' . DS . 'Helper' . DS);
		return $suite;
	}

}
