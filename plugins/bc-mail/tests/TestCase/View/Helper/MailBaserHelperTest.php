<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Case.View.Helper
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * Include files
 */
App::uses('BcBaserHelper', 'View/Helper');

class MailBaserHelperTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.User',
	];

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->BcBaser = new BcBaserHelper(new View());
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown()
	{
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
	public function testIsMail($expected, $url)
	{
		$MailBaser = $this->BcBaser->getPluginBaser('Mail');
		$MailBaser->request = $this->_getRequest($url);
		$this->assertEquals($expected, $this->BcBaser->isMail());
	}

	public function isMailDataProvider()
	{
		return [
			//PC
			[false, '/'],
			[false, '/index'],
			[false, '/news/index'],
			[true, '/contact/index'],
			// モバイルページ
			[false, '/m/'],
			[false, '/m/index'],
			[false, '/m/news/index'],
			[true, '/m/contact/index'],
			// スマートフォンページ
			[false, '/s/'],
			[false, '/s/index'],
			[false, '/s/news/index'],
			[true, '/s/contact/index']
		];
	}
}
