<?php

/**
 * test for BcBaserHelper
 *
 * PHP versions 5
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('View', 'View');
App::uses('BcBaserHelper', 'View/Helper');
App::uses('BcHtmlHelper', 'View/Helper');

/**
 * Baser helper library.
 *
 *
 * @package       Baser.Test.Case
 * @property      BcBaseHelper $Helper
 */
class BcBaserHelperTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->Helper = new BcBaserHelper(new View(null));
	}

	public function tearDown() {
		unset($this->Helper);
		parent::tearDown();
	}

	public function testDocType() {
		$expect = '<!DOCTYPE html>';

		ob_start();
		$this->Helper->docType('html5');;
		$result = trim(ob_get_flush());

		$this->assertEquals($expect, $result);
	}
}
