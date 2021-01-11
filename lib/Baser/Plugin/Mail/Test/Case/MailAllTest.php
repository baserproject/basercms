<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Case
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @package Mail.Test.Case
 */
class MailAllTest extends CakeTestSuite
{

	/**
	 * Suite define the tests for this suite
	 *
	 * @return CakeTestSuite
	 */
	public static function suite()
	{
		$suite = new CakeTestSuite('Baser Mail All Tests');

		$path = dirname(__FILE__) . DS;

		$suite->addTestFile($path . 'MailAllControllerTest.php');
		$suite->addTestFile($path . 'MailAllModelTest.php');
		$suite->addTestFile($path . 'MailAllHelpersTest.php');
		return $suite;
	}

}
