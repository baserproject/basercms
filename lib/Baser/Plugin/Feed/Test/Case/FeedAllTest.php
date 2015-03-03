<?php

/**
 * run all baser feed tests
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Feed.Test.Case
 * @copyright       Copyright 2008 - 2015, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.1.0-beta
 * @license         http://basercms.net/license/index.html
 */

/**
 * @package Mail.Test.Case
 */
class FeedAllTest extends CakeTestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('Feed Plugin All Tests');

		$path = dirname(__FILE__) . DS;

		$suite->addTestFile($path . 'FeedAllModelTest.php');
		return $suite;
	}

}
