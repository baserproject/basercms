<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Test.Case.View.Helper
 * @since			baserCMS v 4.0.5
 * @license			http://basercms.net/license/index.html
 */

/**
 * Include files
 */
App::uses('BcBaserHelper', 'View/Helper');

class MailBaserHelperTest extends BaserTestCase {
	
/**
 * Fixtures
 * @var array
 */
	public $fixtures = array(
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.User',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BcBaser = new BcBaserHelper(new View());
	}
	
/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BcBaser);
		Router::reload();
		parent::tearDown();
	}
	
/**
 * 現在のページがメールプラグインかどうかを判定する
 *
 * @param bool $expected 期待値
 * @param string $url リクエストURL
 * @return void
 * @dataProvider isMailDataProvider
 */
	public function testIsMail($expected, $url) {
		$MailBaser = $this->BcBaser->getPluginBaser('Mail');
		$MailBaser->request = $this->_getRequest($url);
		$this->assertEquals($expected, $this->BcBaser->isMail());
	}

	public function isMailDataProvider() {
		return array(
			//PC
			array(false, '/'),
			array(false, '/index'),
			array(false, '/news/index'),
			array(true, '/contact/index'),
			// モバイルページ
			array(false, '/m/'),
			array(false, '/m/index'),
			array(false, '/m/news/index'),
			array(true, '/m/contact/index'),
			// スマートフォンページ
			array(false, '/s/'),
			array(false, '/s/index'),
			array(false, '/s/news/index'),
			array(true, '/s/contact/index')
		);
	}
}