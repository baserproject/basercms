<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

/**
 * run all baser tests
 *
 * @package Baser.Test.Case
 */
class BcAllTest extends CakeTestSuite
{

	/**
	 * Suite define the tests for this suite
	 *
	 * @return CakeTestSuite
	 */
	public static function suite()
	{
		$suite = new CakeTestSuite('Baser All Tests');

		$path = BASER_TEST_CASES . DS;

		$suite->addTestFile($path . 'BcBasicsTest.php');
		$suite->addTestFile($path . 'BcAllAuthTest.php');
		$suite->addTestFile($path . 'BcAllBehaviorsTest.php');
		$suite->addTestFile($path . 'BcAllComponentsTest.php');
		$suite->addTestFile($path . 'BcAllConfigureTest.php');
		$suite->addTestFile($path . 'BcAllConfigTest.php');
		$suite->addTestFile($path . 'BcAllControllerTest.php');
		$suite->addTestFile($path . 'BcAllEventTest.php');
		$suite->addTestFile($path . 'BcAllFilterTest.php');
		$suite->addTestFile($path . 'BcAllHelpersTest.php');
		$suite->addTestFile($path . 'BcAllLibTest.php');
		$suite->addTestFile($path . 'BcAllModelTest.php');
		$suite->addTestFile($path . 'BcAllRoutingTest.php');
		$suite->addTestFile($path . 'BcAllRouteTest.php');
		$suite->addTestFile($path . 'BcAllNetworkTest.php');
		$suite->addTestFile($path . 'BcAllPluginTest.php');
		$suite->addTestFile($path . 'BcAllViewTest.php');
//		$suite->addTestFile($path . 'BcAllConsoleTest.php');
//		$suite->addTestFile($path . 'BcAllCacheTest.php');
//		$suite->addTestFile($path . 'BcAllCoreTest.php');
//		$suite->addTestFile($path . 'BcAllDatabaseTest.php');
//		$suite->addTestFile($path . 'BcAllErrorTest.php');
//		$suite->addTestFile($path . 'BcAllLogTest.php');
//		$suite->addTestFile($path . 'BcAllUtilityTest.php');
//		$suite->addTestFile($path . 'BcAllI18nTest.php');
		return $suite;
	}

}
