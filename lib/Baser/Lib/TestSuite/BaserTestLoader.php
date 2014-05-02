<?php

/**
 * Custom Test Loader
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('CakeTestLoader', 'TestSuite');

/**
 * @package Baser.Lib.TestSuite
 */
class BaserTestLoader extends CakeTestLoader {

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
		} elseif ($params['baser']) {
			$result = BASER_TEST_CASES;
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

/**
 * Get the list of files for the test listing.
 *
 * @return void
 */
	public static function generateTestList($params) {
		$directory = self::_basePath($params);
		$fileList = self::_getRecursiveFileList($directory);

		$testCases = array();
		foreach ($fileList as $testCaseFile) {
			$case = str_replace($directory . DS, '', $testCaseFile);
			$case = str_replace('Test.php', '', $case);
			$testCases[$testCaseFile] = $case;
		}
		sort($testCases);
		return $testCases;
	}

}
