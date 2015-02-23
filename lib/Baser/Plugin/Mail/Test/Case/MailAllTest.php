<?php

/**
 * run all baser mail tests
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail.Test.Case
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */

/**
 * @package Mail.Test.Case
 */
class MailAllTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Baser All Tests');

		$path = __DIR__ . DS;

		$suite->addTestFile($path . 'MailAllControllerTest.php');
		$suite->addTestFile($path . 'MailAllModelTest.php');
		$suite->addTestFile($path . 'MailAllHelpersTest.php');
		return $suite;
	}

}
