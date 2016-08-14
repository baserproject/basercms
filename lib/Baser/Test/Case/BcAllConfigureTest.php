<?php
/**
 * run all baser configure tests
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * @package Baser.Test.Case
 */
class BcAllConfigureTest extends CakeTestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Configure tests');
		$suite->addTestDirectory(BASER_TEST_CASES . DS . 'Configure' . DS);
		return $suite;
	}

}
