<?php

/**
 * run all models baser mail tests
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail.Test.Case
 * @copyright       Copyright 2008 - 2015, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */

/**
 * @package Baser.Test.Case
 */
class MailAllModelTest extends CakeTestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Helper tests');
		$path = dirname(__FILE__) . DS;
		$suite->addTestDirectory($path . 'Model' . DS);
		$suite->addTestDirectory($path . 'Model' . DS . 'Behavior' . DS);
		return $suite;
	}

}
