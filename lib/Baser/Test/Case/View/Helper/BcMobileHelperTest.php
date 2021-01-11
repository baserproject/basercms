<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.View.Helper
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcAppView', 'View');
App::uses('Helper', 'View');
App::uses('BcMobileHelper', 'View/Helper');

/**
 * BcMobileHelper Test Case
 *
 * @property BcMobileHelper $BcMobile
 * @property BcAppView $View
 */
class BcMobileHelperTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Page',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.Content',
		'baser.Default.User'
	];

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->View = new BcAppView();
		$this->BcMobile = new BcMobileHelper($this->View);
		$this->BcMobile->request = $this->_getRequest('/m/');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown()
	{
		unset($this->BcMobile);
		parent::tearDown();
	}

	/**
	 * afterLayout
	 *
	 * @return void
	 */
	public function testAfterLayout()
	{
		$_SERVER['HTTP_USER_AGENT'] = 'SoftBank';
		$site = BcSite::findCurrent(true);
		$this->View->output = '＞＜＆＆1２＠＠';
		$expected = '&gt;&lt;&amp;&amp;12@@';

		$this->BcMobile->afterLayout('');
		$result = $this->View->output;

		$this->assertEquals($expected, $result);
	}

	/**
	 * コンテンツタイプを出力
	 *
	 * header()が実行できないためテスト不可
	 * 原因:このメソッド実行前にechoやprintなどのアウトプット or 既にheaderを送信 or UTF-8BOM?
	 * headers_sent() headers_list()で確認可
	 *
	 * @return void
	 */
	public function testHeader()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$this->BcMobile->request->params['Site']['device'] = 'mobile';
		$this->BcMobile->header();

	}

	public function testAfterRender()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
