<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('BcSmartphoneHelper', 'View/Helper');

/**
 * BcSmartphoneHelper Test Case
 *
 */
class BcSmartphoneHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcSmartphone = new BcSmartphoneHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BcSmartphone);

		parent::tearDown();
	}

/**
 * afterLayout
 *
 * @return void
 */
	public function testAfterLayout() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}




}
