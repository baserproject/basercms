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

App::uses('BcAppView', 'View');
App::uses('Helper', 'View');
App::uses('BcSmartphoneHelper', 'View/Helper');

/**
 * BcSmartphoneHelper Test Case
 *
 * @property BcSmartphoneHelper $BcSmartphone
 */
class BcSmartphoneHelperTest extends BaserTestCase {

	public $fixtures = [
		'baser.Default.Content',
		'baser.Default.SiteConfig',
		'baser.Default.User',
		'baser.Default.Site',
	];


/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->View = new BcAppView();
		$this->BcSmartphone = new BcSmartphoneHelper($this->View);
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

		//afterLayoutの条件分岐、$this->request->params['Site']をempty以外にしたい
		$this->_getRequest('/s/');
		$site = BcSite::findCurrent();
		pr($site->device);
		$this->BcSmartphone->afterLayout('');

	}

	public function testAfterRender() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
