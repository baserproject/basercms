<?php

/**
 * Custom TestSuite Command
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('CakeHtmlReporter', 'TestSuite/Reporter');

/**
 * @package Baser.Lib.TestSuite.Reporter
 */
class BaserHtmlReporter extends CakeHtmlReporter {

/**
 * Get the baseUrl if one is available.
 *
 * @return string The base URL for the request.
 */
	public function baseUrl() {
		return baseUrl() . 'test.php';
	}

/**
 * Paints the document start content contained in header.php
 *
 * @return void
 */
	public function paintDocumentStart() {
		ob_start();
		$baseDir = baseUrl();
		include BASER_LIBS . 'TestSuite' . DS . 'templates' . DS . 'header.php';
	}

/**
 * Paints the menu on the left side of the test suite interface.
 * Contains all of the various plugin, core, and app buttons.
 *
 * @return void
 */
	public function paintTestMenu() {
		$cases = $this->baseUrl() . '?show=cases';
		$plugins = App::objects('plugin', null, false);
		sort($plugins);
		include BASER_LIBS . 'TestSuite' . DS . 'templates' . DS . 'menu.php';
	}

/**
 * Retrieves and paints the list of tests cases in an HTML format.
 *
 * @return void
 */
	public function testCaseList() {
		$testCases = BaserTestLoader::generateTestList($this->params);
		$core = $this->params['core'];
		$baser = $this->params['baser'];
		$plugin = $this->params['plugin'];

		$buffer = "<h3>App Test Cases:</h3>\n<ul>";
		$urlExtra = null;
		if ($core) {
			$buffer = "<h3>Core Test Cases:</h3>\n<ul>";
			$urlExtra = '&core=true';
		} elseif ($baser) {
			$buffer = "<h3>Baser Test Cases:</h3>\n<ul>";
			$urlExtra = '&baser=true';
		} elseif ($plugin) {
			$buffer = "<h3>" . Inflector::humanize($plugin) . " Test Cases:</h3>\n<ul>";
			$urlExtra = '&plugin=' . $plugin;
		}

		if (1 > count($testCases)) {
			$buffer .= "<strong>EMPTY</strong>";
		}

		foreach ($testCases as $testCaseFile => $testCase) {
			$title = explode(DS, str_replace('.test.php', '', $testCase));
			$title[count($title) - 1] = Inflector::camelize($title[count($title) - 1]);
			$title = implode(' / ', $title);
			$buffer .= "<li><a href='" . $this->baseUrl() . "?case=" . urlencode($testCase) . $urlExtra . "'>" . $title . "</a></li>\n";
		}
		$buffer .= "</ul>\n";
		echo $buffer;
	}

/**
 * Renders the links that for accessing things in the test suite.
 *
 * @return void
 */
	protected function _paintLinks() {
		$show = $query = array();
		if (!empty($this->params['case'])) {
			$show['show'] = 'cases';
		}

		if (!empty($this->params['core'])) {
			$show['core'] = $query['core'] = 'true';
		}
		if (!empty($this->params['baser'])) {
			$show['baser'] = $query['baser'] = 'true';
		}
		if (!empty($this->params['plugin'])) {
			$show['plugin'] = $query['plugin'] = $this->params['plugin'];
		}
		if (!empty($this->params['case'])) {
			$query['case'] = $this->params['case'];
		}
		$show = $this->_queryString($show);
		$query = $this->_queryString($query);

		echo "<p><a href='" . $this->baseUrl() . $show . "'>Run more tests</a> | <a href='" . $this->baseUrl() . $query . "&amp;show_passes=1'>Show Passes</a> | \n";
		echo "<a href='" . $this->baseUrl() . $query . "&amp;debug=1'>Enable Debug Output</a> | \n";
		echo "<a href='" . $this->baseUrl() . $query . "&amp;code_coverage=true'>Analyze Code Coverage</a></p>\n";
	}

/**
 * Paints the end of the document html.
 *
 * @return void
 */
	public function paintDocumentEnd() {
		$baseDir = baseUrl();
		include BASER_LIBS . 'TestSuite' . DS . 'templates' . DS . 'footer.php';
		if (ob_get_length()) {
			ob_end_flush();
		}
	}

}
