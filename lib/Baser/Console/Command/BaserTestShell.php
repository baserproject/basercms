<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Console.Command
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('TestShell', 'Console/Command');
App::uses('BaserTestSuiteDispatcher', 'TestSuite');
App::uses('BaserTestSuiteCommand', 'TestSuite');
App::uses('BaserTestLoader', 'TestSuite');

/**
 * Class BaserTestShell
 *
 * @package Baser.Console.Command
 */
class BaserTestShell extends TestShell
{

	/**
	 * Initialization method installs PHPUnit and loads all plugins
	 *
	 * @return void
	 * @throws Exception
	 */
	public function initialize()
	{
		// CUSTOMIZE ADD 2016/08/28 ryuring
		// >>>
		$currentTheme = Configure::read('BcSite.theme');
		$testTheme = Configure::read('BcApp.testTheme');
		if ($_SERVER['argv'][2] === 'baser' && $currentTheme != $testTheme) {
			trigger_error(sprintf(__d('baser', 'CLIでのユニットテストは、%s テーマを利用する前提となっています。再インストール後にユニットテストを実行してください。'), $testTheme), E_USER_ERROR);
			exit();
		}
		// <<<
		$this->_dispatcher = new BaserTestSuiteDispatcher();
		$sucess = $this->_dispatcher->loadTestFramework();
		if (!$sucess) {
			throw new Exception(__d('cake_dev', 'Please install PHPUnit framework <info>(http://www.phpunit.de)</info>'));
		}
	}

	/**
	 * Parse the CLI options into an array CakeTestDispatcher can use.
	 *
	 * @return array Array of params for CakeTestDispatcher
	 */
	protected function _parseArgs()
	{
		if (empty($this->args)) {
			return;
		}
		$params = [
			'core' => false,
			'baser' => false,
			'app' => false,
			'plugin' => null,
			'output' => 'text',
		];

		if (strpos($this->args[0], '.php')) {
			$category = $this->_mapFileToCategory($this->args[0]);
			$params['case'] = $this->_mapFileToCase($this->args[0], $category);
		} else {
			$category = $this->args[0];
			if (isset($this->args[1])) {
				$params['case'] = $this->args[1];
			}
		}

		if ($category === 'core') {
			$params['core'] = true;
		} elseif ($category === 'baser') {
			$params['baser'] = true;
		} elseif ($category === 'app') {
			$params['app'] = true;
		} else {
			$params['plugin'] = $category;
		}

		return $params;
	}

	/**
	 * Runs the test case from $runnerArgs
	 *
	 * @param array $runnerArgs list of arguments as obtained from _parseArgs()
	 * @param array $options list of options as constructed by _runnerOptions()
	 * @return void
	 */
	protected function _run($runnerArgs, $options = [])
	{
		restore_error_handler();
		restore_error_handler();

		$testCli = new BaserTestSuiteCommand('BaserTestLoader', $runnerArgs);
		$testCli->run($options);
	}

	/**
	 * Shows a list of available test cases and gives the option to run one of them
	 *
	 * @return void
	 */
	public function available()
	{
		$params = $this->_parseArgs();
		$testCases = BaserTestLoader::generateTestList($params);
		$baser = $params['baser'];
		$app = $params['app'];
		$plugin = $params['plugin'];

		$title = "Core Test Cases:";
		$category = 'core';
		if ($baser) {
			$title = "Baser Test Cases:";
			$category = 'baser';
		} elseif ($app) {
			$title = "App Test Cases:";
			$category = 'app';
		} elseif ($plugin) {
			$title = Inflector::humanize($plugin) . " Test Cases:";
			$category = $plugin;
		}

		if (empty($testCases)) {
			$this->out(__d('baser', "有効なテストケースがありません。 \n\n"));
			return $this->out($this->OptionParser->help());
		}

		$this->out($title);
		$i = 1;
		$cases = [];
		foreach($testCases as $testCaseFile => $testCase) {
			$case = str_replace('Test.php', '', $testCase);
			$this->out("[$i] $case");
			$cases[$i] = $case;
			$i++;
		}

		while($choice = $this->in(__d('baser', '何のテストケースを実行したいですか？'), null, 'q')) {
			if (is_numeric($choice) && isset($cases[$choice])) {
				$this->args[0] = $category;
				$this->args[1] = $cases[$choice];
				$this->_run($this->_parseArgs(), $this->_runnerOptions());
				break;
			}

			if (is_string($choice) && in_array($choice, $cases)) {
				$this->args[0] = $category;
				$this->args[1] = $choice;
				$this->_run($this->_parseArgs(), $this->_runnerOptions());
				break;
			}

			if ($choice == 'q') {
				break;
			}
		}
	}

	/**
	 * Find the test case for the passed file. The file could itself be a test.
	 *
	 * @param string $file
	 * @param string $category
	 * @param boolean $throwOnMissingFile
	 * @return array(type, case)
	 * @throws Exception
	 */
	protected function _mapFileToCase($file, $category, $throwOnMissingFile = true)
	{
		if (!$category || (substr($file, -4) !== '.php')) {
			return false;
		}

		$_file = realpath($file);
		if ($_file) {
			$file = $_file;
		}

		$testFile = $testCase = null;
		$testCaseFolder = str_replace(APP, '', APP_TEST_CASES);
		if (preg_match('@Test[\\\/]@', $file)) {
			if (substr($file, -8) === 'Test.php') {
				$testCase = substr($file, 0, -8);
				$testCase = str_replace(DS, '/', $testCase);
				$testCaseFolderEscaped = str_replace('/', '\/', $testCaseFolder);
				$testCase = preg_replace('@.*' . $testCaseFolderEscaped . '\/@', '', $testCase);
				if (!empty($testCase)) {
					if ($category === 'core') {
						$testCase = str_replace('lib/Cake', '', $testCase);
					}
					// CUSTOMIZE ADD
					// >>>
					if ($category === 'baser') {
						$testCase = str_replace('lib/Baser', '', $testCase);
					}
					// <<<

					return $testCase;
				}
				throw new Exception(__d('cake_dev', 'Test case %s cannot be run via this shell', $testFile));
			}
		}

		$file = substr($file, 0, -4);
		if ($category === 'core') {

			$testCase = str_replace(DS, '/', $file);
			$testCase = preg_replace('@.*lib/Cake/@', '', $file);
			$testCase[0] = strtoupper($testCase[0]);
			$testFile = CAKE . 'Test/Case/' . $testCase . 'Test.php';

			if (!file_exists($testFile) && $throwOnMissingFile) {
				throw new Exception(__d('cake_dev', 'Test case %s not found', $testFile));
			}

			return $testCase;
			// CUSTOMIZE ADD
		} elseif ($category === 'baser') {
			$testCase = str_replace(DS, '/', $file);
			$testCase = preg_replace('@.*lib/Baser/@', '', $file);
			$testCase[0] = strtoupper($testCase[0]);
			$testFile = BASER . 'Test/Case/' . $testCase . 'Test.php';
			if (!file_exists($testFile) && $throwOnMissingFile) {
				throw new Exception(__d('cake_dev', 'Test case %s not found', $testFile));
			}
			return $testCase;
			// <<<
		}


		if ($category === 'app') {
			$testFile = str_replace(APP, APP_TEST_CASES . '/', $file) . 'Test.php';
		} else {
			$testFile = preg_replace(
				"@((?:plugins|Plugin)[\\/]{$category}[\\/])(.*)$@",
				'\1' . $testCaseFolder . '/\2Test.php',
				$file
			);
		}

		if (!file_exists($testFile) && $throwOnMissingFile) {
			throw new Exception(__d('cake_dev', 'Test case %s not found', $testFile));
		}

		$testCase = substr($testFile, 0, -8);
		$testCase = str_replace(DS, '/', $testCase);
		$testCase = preg_replace('@.*' . $testCaseFolder . '/@', '', $testCase);
		return $testCase;
	}

	/**
	 * For the given file, what category of test is it? returns app, core or the name of the plugin
	 *
	 * @param string $file
	 * @return string
	 */
	protected function _mapFileToCategory($file)
	{
		$_file = realpath($file);
		if ($_file) {
			$file = $_file;
		}

		$file = str_replace(DS, '/', $file);
		if (strpos($file, 'lib/Cake/') !== false) {
			return 'core';
		} elseif (strpos($file, 'lib/Baser/') !== false) {
			return 'core';
		} elseif (preg_match('@(?:plugins|Plugin)/([^/]*)@', $file, $match)) {
			return $match[1];
		}
		return 'app';
	}

	/**
	 * Main entry point to this shell
	 *
	 * @return void
	 */
	public function main()
	{

		// CUSTOMIZE MODIFY 2016/08/06 ryuring
		// >>>
		//$this->out(__d('cake_console', 'CakePHP Test Shell'));
		// ---
		$this->out(__d('baser', 'baserCMS テストシェル'));
		// <<<

		$this->hr();
		$args = $this->_parseArgs();

		if (empty($args['case'])) {
			return $this->available();
		}

		$this->_run($args, $this->_runnerOptions());
	}

	/**
	 * Displays a header for the shell
	 *
	 * @return void
	 */
	protected function _welcome()
	{
		$this->out();

		// CUSTOMIZE MODIFY 2016/08/06 ryuring
		// >>>
		//$this->out(__d('cake_console', '<info>Welcome to CakePHP %s Console</info>', 'v' . Configure::version()));
		// ---
		$this->out(__d('baser', '<info>baserCMS %s コンソールへようこそ</info>', 'v' . getVersion()));
		// <<<

		$this->hr();
		$this->out(__d('cake_console', 'App : %s', APP_DIR));
		$this->out(__d('cake_console', 'Path: %s', APP));
		$this->hr();
	}

}
