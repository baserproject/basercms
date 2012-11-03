<?php
/**
 * run all baser tests
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */

/**
 * @package Baser.Test.Case
 */
class BcAllTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Baser All Tests');

		$path = BASER_TEST_CASES . DS;

		$suite->addTestFile($path . 'BcBasicsTest.php');
//		$suite->addTestFile($path . 'BcAllConsoleTest.php');
//		$suite->addTestFile($path . 'BcAllBehaviorsTest.php');
//		$suite->addTestFile($path . 'BcAllCacheTest.php');
//		$suite->addTestFile($path . 'BcAllComponentsTest.php');
//		$suite->addTestFile($path . 'BcAllConfigureTest.php');
//		$suite->addTestFile($path . 'BcAllCoreTest.php');
//		$suite->addTestFile($path . 'BcAllControllerTest.php');
//		$suite->addTestFile($path . 'BcAllDatabaseTest.php');
//		$suite->addTestFile($path . 'BcAllErrorTest.php');
//		$suite->addTestFile($path . 'BcAllEventTest.php');
		$suite->addTestFile($path . 'BcAllHelpersTest.php');
//		$suite->addTestFile($path . 'BcAllLogTest.php');
//		$suite->addTestFile($path . 'BcAllRoutingTest.php');
//		$suite->addTestFile($path . 'BcAllNetworkTest.php');
//		$suite->addTestFile($path . 'BcAllUtilityTest.php');
//		$suite->addTestFile($path . 'BcAllViewTest.php');
//		$suite->addTestFile($path . 'BcAllI18nTest.php');
		return $suite;
	}
}