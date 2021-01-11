<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @package Blog.Test.Case
 */
class BlogAllTest extends CakeTestSuite
{

	/**
	 * Suite define the tests for this suite
	 *
	 * @return CakeTestSuite
	 */
	public static function suite()
	{
		$suite = new CakeTestSuite('Baser Blog All Tests');

		$path = dirname(__FILE__) . DS;
		$suite->addTestFile($path . 'BlogAllControllerTest.php');
		$suite->addTestFile($path . 'BlogAllModelTest.php');
		$suite->addTestFile($path . 'BlogAllHelpersTest.php');

		return $suite;
	}

}
