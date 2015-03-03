<?php
/**
 * ThemeConfig
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib.Test.Case.View.Helper
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('BcAdminHelper', 'View/Helper');

/**
 * BcAdminHelper Test Case
 *
 */
class BcAdminHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcAdmin = new BcAdminHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BcAdmin);

		parent::tearDown();
	}

/**
 * testIsAdminGlobalmenuUsed method
 *
 * @return void
 */
	public function testIsAdminGlobalmenuUsed() {
		$this->markTestIncomplete('testIsAdminGlobalmenuUsed not implemented.');
	}

/**
 * testIsSystemAdmin method
 *
 * @return void
 */
	public function testIsSystemAdmin() {
		$this->markTestIncomplete('testIsSystemAdmin not implemented.');
	}

}
