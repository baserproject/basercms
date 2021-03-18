<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Test.Case
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * @package Uploader.Test.Case
 */
class UploaderAllTest extends CakeTestSuite
{

	/**
	 * Suite define the tests for this suite
	 *
	 * @return CakeTestSuite
	 */
	public static function suite()
	{
		$suite = new CakeTestSuite('Baser Uploader All Tests');
		$path = dirname(__FILE__) . DS;
		$suite->addTestFile($path . 'UploaderAllControllerTest.php');
		$suite->addTestFile($path . 'UploaderAllEventTest.php');
		$suite->addTestFile($path . 'UploaderAllHelpersTest.php');
		$suite->addTestFile($path . 'UploaderAllModelTest.php');
		return $suite;
	}

}
