<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.Test.Case
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @package Mail.Test.Case
 */
class FeedAllTest extends CakeTestSuite
{

	/**
	 * Suite define the tests for this suite
	 *
	 * @return CakeTestSuite
	 */
	public static function suite()
	{
		$suite = new CakeTestSuite('Feed Plugin All Tests');

		$path = dirname(__FILE__) . DS;

		$suite->addTestFile($path . 'FeedAllControllerTest.php');
		$suite->addTestFile($path . 'FeedAllHelpersTest.php');
		$suite->addTestFile($path . 'FeedAllModelTest.php');
		return $suite;
	}

}
