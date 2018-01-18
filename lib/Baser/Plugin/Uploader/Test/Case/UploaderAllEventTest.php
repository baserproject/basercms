<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.Test.Case
 * @since			baserCMS v 4.0.9
 * @license			http://basercms.net/license/index.html
 */

class UploaderAllEventTest extends CakeTestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Uploader Event tests');
		$suite->addTestDirectory(dirname(__FILE__) . DS . 'Event' . DS);
		return $suite;
	}

}
