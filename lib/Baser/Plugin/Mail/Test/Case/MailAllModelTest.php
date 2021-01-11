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
 * @package Baser.Test.Case
 */
class MailAllModelTest extends CakeTestSuite
{

	/**
	 * Suite define the tests for this suite
	 *
	 * @return CakeTestSuite
	 */
	public static function suite()
	{
		$suite = new CakeTestSuite('All Helper tests');
		$path = dirname(__FILE__) . DS;
		$suite->addTestDirectory($path . 'Model' . DS);
		$suite->addTestDirectory($path . 'Model' . DS . 'Behavior' . DS);
		return $suite;
	}

}
