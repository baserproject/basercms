<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib.TestSuite.Reporter
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('CakeTextReporter', 'TestSuite/Reporter');

/**
 * Class BaserTextReporter
 *
 * @package Baser.Lib.TestSuite.Reporter
 */
class BaserTextReporter extends CakeTextReporter
{

	/**
	 * Generate a test case list in plain text.
	 * Creates as series of url's for tests that can be run.
	 * One case per line.
	 *
	 * @return void
	 */
	public function testCaseList()
	{
		// CUSTOMIZE MODIFY 2014/07/02 ryuring
		// >>>
		//$testCases = parent::testCaseList();
		// ---
		$testCases = BaserTestLoader::generateTestList($this->params);
		$baser = $this->params['baser'];
		// <<<
		$app = $this->params['app'];
		$plugin = $this->params['plugin'];

		$buffer = "Core Test Cases:\n";
		// CUSTOMIZE MODIFY 2014/07/02 ryuring
		// >>>
		//if ($app) {
		// ---
		if ($baser) {
			$buffer = "Baser Test Cases:\n";
			$urlExtra = '&baser=true';
		} elseif ($app) {
			// <<<
			$buffer = "App Test Cases:\n";
		} elseif ($plugin) {
			$buffer = Inflector::humanize($plugin) . " Test Cases:\n";
		}

		if (count($testCases) < 1) {
			$buffer .= 'EMPTY';
			echo $buffer;
		}

		foreach($testCases as $testCase) {
			$buffer .= $_SERVER['SERVER_NAME'] . $this->baseUrl() . "?case=" . $testCase . "&output=text\n";
		}

		$buffer .= "\n";
		echo $buffer;
	}

}
