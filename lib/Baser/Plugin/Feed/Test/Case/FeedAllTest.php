<?php

/**
 * run all baser feed tests
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail.Test.Case
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */

/**
 * @package Mail.Test.Case
 */
class FeedAllTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Feed Plugin All Tests');

		$path = __DIR__ . DS;

		$suite->addTestFile($path . 'FeedAllModelTest.php');
		return $suite;
	}

}
