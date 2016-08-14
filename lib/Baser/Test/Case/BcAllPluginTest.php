<?php
/**
 * run all baser core plugin tests
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */

/**
 * @package Baser.Test.Case
 */
class BcAllPluginTest extends CakeTestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('Baser All Tests');

		$plugins = Configure::read('BcApp.corePlugins');

		foreach ($plugins as $plugin) {
			$suite->addTestFile(BASER_PLUGINS . "{$plugin}/Test/Case/{$plugin}AllTest.php");
		}

		return $suite;
	}

}
