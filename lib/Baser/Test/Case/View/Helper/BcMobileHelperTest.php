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
App::uses('BcMobileHelper', 'View/Helper');

/**
 * BcMobileHelper Test Case
 *
 */
class BcMobileHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.Default.Page',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.Content',
		'baser.Default.User'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcMobile = new BcMobileHelper($View);
		$this->BcMobile->request = $this->_getRequest('/m/');
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
 */
	public function testAfterLayout() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


/**
 * コンテンツタイプを出力
 * 
 * @return void
 */
	public function testHeader() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$this->BcMobile->header();
		$result = xdebug_get_headers();
		$expected = 'Content-type: application/xhtml+xml';
		$this->assertEquals($expected, $result[0]);

	}

}
