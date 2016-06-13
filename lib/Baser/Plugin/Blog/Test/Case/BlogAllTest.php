<?php

/**
 * run all baser blog tests
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Blog.Test.Case
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */

/**
 * @package Blog.Test.Case
 */
class BlogAllTest extends CakeTestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('Baser Blog All Tests');

		$path = dirname(__FILE__) . DS;
		//$suite->addTestFile($path . 'BlogAllControllerTest.php');
		$suite->addTestFile($path . 'BlogAllModelTest.php');
		$suite->addTestFile($path . 'BlogAllHelpersTest.php');

		return $suite;
	}

}
