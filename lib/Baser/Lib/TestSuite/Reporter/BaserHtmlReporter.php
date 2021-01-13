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

App::uses('CakeHtmlReporter', 'TestSuite/Reporter');

/**
 * Class BaserHtmlReporter
 *
 * @package Baser.Lib.TestSuite.Reporter
 */
class BaserHtmlReporter extends CakeHtmlReporter
{
// CUSTOMIZE ADD 2014/07/02 ryuring
// >>>
	/**
	 * Get the baseUrl if one is available.
	 *
	 * @return string The base URL for the request.
	 */
	public function baseUrl()
	{
		return baseUrl() . 'test.php';
	}
// <<<

	/**
	 * Paints the document start content contained in header.php
	 *
	 * @return void
	 */
	public function paintDocumentStart()
	{
		ob_start();
		// CUSTOMIZE MODIFY 2014/07/02 ryuring
		// >>>
		/*$baseDir = $this->params['baseDir'];
		include CAKE . 'TestSuite' . DS . 'templates' . DS . 'header.php';*/
		// ---
		$baseDir = baseUrl();
		include BASER_LIBS . 'TestSuite' . DS . 'templates' . DS . 'header.php';
		// <<<
	}

	/**
	 * Paints the menu on the left side of the test suite interface.
	 * Contains all of the various plugin, core, and app buttons.
	 *
	 * @return void
	 */
	public function paintTestMenu()
	{
		$cases = $this->baseUrl() . '?show=cases';
		$plugins = App::objects('plugin', null, false);
		sort($plugins);
		// CUSTOMIZE MODIFY 2014/07/02 ryuring
		//include CAKE . 'TestSuite' . DS . 'templates' . DS . 'menu.php';
		// ---
		include BASER_LIBS . 'TestSuite' . DS . 'templates' . DS . 'menu.php';
		// <<<
	}

	/**
	 * Retrieves and paints the list of tests cases in an HTML format.
	 *
	 * @return void
	 */
	public function testCaseList()
	{
		// CUSTOMIZE MODIFY 2014/07/02
		// >>>
		//$testCases = parent::testCaseList();
		// ---
		$testCases = BaserTestLoader::generateTestList($this->params);
		$baser = $this->params['baser'];
		// <<<
		$core = $this->params['core'];
		$plugin = $this->params['plugin'];

		$buffer = "<h3>App Test Cases:</h3>\n<ul>";
		$urlExtra = null;
		if ($core) {
			$buffer = "<h3>Core Test Cases:</h3>\n<ul>";
			$urlExtra = '&core=true';
			// CUSTOMIZE ADD 2014/07/02 ryuring
			// >>>
		} elseif ($baser) {
			$buffer = "<h3>Baser Test Cases:</h3>\n<ul>";
			$urlExtra = '&baser=true';
			// <<<
		} elseif ($plugin) {
			$buffer = "<h3>" . Inflector::humanize($plugin) . " Test Cases:</h3>\n<ul>";
			$urlExtra = '&plugin=' . $plugin;
		}

		if (count($testCases) < 1) {
			$buffer .= "<strong>EMPTY</strong>";
		}

		foreach($testCases as $testCase) {
			$title = explode(DS, str_replace('.test.php', '', $testCase));
			$title[count($title) - 1] = Inflector::camelize($title[count($title) - 1]);
			$title = implode(' / ', $title);
			$buffer .= "<li><a href='" . $this->baseUrl() . "?case=" . urlencode($testCase) . $urlExtra . "'>" . $title . "</a></li>\n";
		}
		$buffer .= "</ul>\n";
		echo $buffer;
	}

	/**
	 * Paints the end of the document html.
	 *
	 * @return void
	 */
	public function paintDocumentEnd()
	{
		// CUSTOMIZE MODIFY 2014/07/02 ryuring
		// >>>
		/*$baseDir = $this->params['baseDir'];
		include CAKE . 'TestSuite' . DS . 'templates' . DS . 'footer.php';*/
		// ---
		$baseDir = baseUrl();
		include BASER_LIBS . 'TestSuite' . DS . 'templates' . DS . 'footer.php';
		// <<<

		if (ob_get_length()) {
			ob_end_flush();
		}
	}

	/**
	 * Returns the query string formatted for ouput in links
	 *
	 * @return array
	 */
	protected function _getQueryLink()
	{
		$show = $query = [];
		if (!empty($this->params['case'])) {
			$show['show'] = 'cases';
		}

		if (!empty($this->params['core'])) {
			$show['core'] = $query['core'] = 'true';
		}
		if (!empty($this->params['plugin'])) {
			$show['plugin'] = $query['plugin'] = $this->params['plugin'];
		}
		if (!empty($this->params['case'])) {
			$query['case'] = $this->params['case'];
		}
		if (!empty($this->params['filter'])) {
			$query['filter'] = $this->params['filter'];
		}
		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>
		if (!empty($this->params['baser'])) {
			$show['baser'] = $query['baser'] = 'true';
		}
		// <<<
		$show = $this->_queryString($show);
		$query = $this->_queryString($query);
		return [$show, $query];
	}

}
