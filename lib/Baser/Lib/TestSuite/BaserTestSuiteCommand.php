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
App::uses('CakeTestSuiteCommand', 'TestSuite');
App::uses('BaserTestLoader', 'TestSuite');

/**
 * @package Baser.TestSuite
 */
class BaserTestSuiteCommand extends CakeTestSuiteCommand {

/**
 * Handles output flag used to change printing on webrunner.
 *
 * @return void
 */
	public function handleReporter($reporter) {
		$object = null;

		$type = strtolower($reporter);
		$reporter = ucwords($reporter);
		$coreClass = 'Baser' . $reporter . 'Reporter';
		App::uses($coreClass, 'TestSuite/Reporter');

		$appClass = $reporter . 'Reporter';
		App::uses($appClass, 'TestSuite/Reporter');

		if (!class_exists($appClass)) {
			$object = new $coreClass(null, $this->_params);
		} else {
			$object = new $appClass(null, $this->_params);
		}
		return $this->arguments['printer'] = $object;
	}

}
