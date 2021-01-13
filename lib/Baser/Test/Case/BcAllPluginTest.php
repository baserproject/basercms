<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case
 * @since           baserCMS v 3.0.7
 * @license         https://basercms.net/license/index.html
 */

/**
 * run all baser core plugin tests
 *
 * @package Baser.Test.Case
 */
class BcAllPluginTest extends CakeTestSuite
{

	/**
	 * Suite define the tests for this suite
	 *
	 * @return CakeTestSuite
	 */
	public static function suite()
	{
		$suite = new CakeTestSuite('Baser All Tests');

		$plugins = Configure::read('BcApp.corePlugins');

		foreach($plugins as $plugin) {
			$suite->addTestFile(BASER_PLUGINS . "{$plugin}/Test/Case/{$plugin}AllTest.php");
		}

		return $suite;
	}

}
