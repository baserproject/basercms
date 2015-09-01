<?php
/**
 * ThemeConfig
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS Users Community
 * @link      http://basercms.net baserCMS Project
 * @package     Baser.Test.Case.View.Helper
 * @since     baserCMS v 3.0.0
 * @license     http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('BcMobileHelper', 'View/Helper');

/**
 * BcMobileHelper Test Case
 *
 */
class BcMobileHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcMobile = new BcMobileHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BcMobile);

		parent::tearDown();
	}

/**
 * afterLayout
 *
 * @return void
 * @access public
 */
	public function testAfterLayout() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


/**
 * コンテンツタイプを出力
 * 
 * @return void
 * @access public
 */
	public function testHeader() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
