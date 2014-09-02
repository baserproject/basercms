<?php
/**
 * BaserTestCase
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib.TestSuite
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

class BaserTestCase extends CakeTestCase {
	
/**
 * setUp method
 *
 * @return void
 */
	public function run(PHPUnit_Framework_TestResult $result = null) {
		if(!isset($this->fixtures) || !in_array('baser.PluginContent', $this->fixtures)) {
			$this->fixtures[] = 'baser.PluginContent';
		}
		parent::run($result);
	}
}