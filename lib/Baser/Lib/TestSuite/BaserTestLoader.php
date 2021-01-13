<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib.TestSuite
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

App::uses('CakeTestLoader', 'TestSuite');

/**
 * Class BaserTestLoader
 *
 * @package Baser.Lib.TestSuite
 */
class BaserTestLoader extends CakeTestLoader
{

	/**
	 * Generates the base path to a set of tests based on the parameters.
	 *
	 * @param array $params
	 * @return string The base path.
	 */
	protected static function _basePath($params)
	{
		$result = null;
		if (!empty($params['core'])) {
			$result = CORE_TEST_CASES;
			// CUSTOMIZE ADD 2014/07/02 ryuring
			// >>>
		} elseif ($params['baser']) {
			$result = BASER_TEST_CASES;
			// <<<
		} elseif (!empty($params['plugin'])) {
			if (!CakePlugin::loaded($params['plugin'])) {
				try {
					CakePlugin::load($params['plugin']);
					$result = CakePlugin::path($params['plugin']) . 'Test' . DS . 'Case';
				} catch (MissingPluginException $e) {
				}
			} else {
				$result = CakePlugin::path($params['plugin']) . 'Test' . DS . 'Case';
			}
		} elseif (!empty($params['app'])) {
			$result = APP_TEST_CASES;
		}
		return $result;
	}

}
