<?php

/**
 * Custom TestSuite Command
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
App::uses('CakeTextReporter', 'TestSuite/Reporter');

/**
 * @package Baser.TestSuite.Reporter
 */
class BaserTextReporter extends CakeTextReporter {

/**
 * Generate a test case list in plain text.
 * Creates as series of url's for tests that can be run.
 * One case per line.
 *
 * @return void
 */
	public function testCaseList() {
		$testCases = BaserTestLoader::generateTestList($this->params);
		$app = $this->params['app'];
		$baser = $this->params['baser'];
		$plugin = $this->params['plugin'];

		$buffer = "Core Test Cases:\n";
		$urlExtra = '';
		if ($baser) {
			$buffer = "Baser Test Cases:\n";
			$urlExtra = '&baser=true';
		} elseif ($app) {
			$buffer = "App Test Cases:\n";
			$urlExtra = '&app=true';
		} elseif ($plugin) {
			$buffer = Inflector::humanize($plugin) . " Test Cases:\n";
			$urlExtra = '&plugin=' . $plugin;
		}

		if (1 > count($testCases)) {
			$buffer .= "EMPTY";
			echo $buffer;
		}

		foreach ($testCases as $testCaseFile => $testCase) {
			$buffer .= $_SERVER['SERVER_NAME'] . $this->baseUrl() . "?case=" . $testCase . "&output=text\n";
		}

		$buffer .= "\n";
		echo $buffer;
	}

}
