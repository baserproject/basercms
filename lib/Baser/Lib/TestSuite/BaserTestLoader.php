<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib.TestSuite
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

App::uses('CakeTestLoader', 'TestSuite');

/**
 * TestLoader for CakePHP Test suite.
 *
 * Turns partial paths used on the testsuite console and web UI into full file paths.
 *
 * @package Baser.Lib.TestSuite
 */
class BaserTestLoader extends CakeTestLoader {

/**
 * Get the list of files for the test listing.
 *
 * @param string $params Path parameters
 * @return array
 */
	public static function generateTestList($params) {
		$directory = self::_basePath($params);
		$fileList = self::_getRecursiveFileList($directory);
		$testCases = [];
		foreach ($fileList as $testCaseFile) {
			$case = str_replace($directory . DS, '', $testCaseFile);
			$case = str_replace('Test.php', '', $case);
			$testCases[$testCaseFile] = $case;
		}
		sort($testCases);
		return $testCases;
	}
	
/**
 * Generates the base path to a set of tests based on the parameters.
 *
 * @param array $params
 * @return string The base path.
 */
	protected static function _basePath($params) {
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
