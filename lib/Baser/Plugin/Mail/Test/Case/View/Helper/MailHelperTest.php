<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Case.View.Helper
 * @since           baserCMS v 4.0.3
 * @license         https://basercms.net/license/index.html
 */

App::uses('MailHelper', 'Mail.View/Helper');
App::uses('BcBaserHelper', 'View/Helper');
App::uses('BcAppView', 'View');

/**
 * Class MailHelperTest
 *
 * @property MailHelper $Mail
 */
class MailHelperTest extends BaserTestCase
{

	/**
	 * Fixture
	 *
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.User',
		'baser.Default.SiteConfig',
		'plugin.Mail.Default/MailContent',
		'plugin.Mail.Default/MailField',
		'plugin.Mail.Default/MailMessage',
		'plugin.Mail.Default/MailConfig'
	];

	/**
	 * set up
	 */
	public function setUp()
	{
		parent::setUp();
		$this->View = new BcAppView(null);
		$this->View->request = $this->_getRequest('/');
		$this->Mail = new MailHelper($this->View);
	}

	/**
	 * tear down
	 */
	public function tearDown()
	{
		unset($this->Mail);
		parent::tearDown();
	}

	/**
	 * 説明文の取得結果
	 *
	 * public function testDescription() {
	 * $this->markTestIncomplete('このメソッドは、同一クラス内のメソッドをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * 説明文を取得する
	 */
	public function testGetDescription()
	{
		ClassRegistry::flush();
		$this->Mail->setMailContent(1);
		$expected = '<p><span style="color:#C30">*</span> 印の項目は必須となりますので、必ず入力してください。</p>';
		$result = $this->Mail->getDescription();
		$this->assertEquals($result, $expected, "説明文の取得結果が違います。");
	}

	/**
	 * 説明文の存在確認
	 */
	public function testDescriptionExists()
	{
		$this->Mail->setMailContent(1);
		$result = $this->Mail->descriptionExists();
		$this->assertTrue($result, "メールの説明文が指定されていません。");
	}

	/**
	 * メールフォームを取得
	 */
	public function testGetForm()
	{
		$MailMessage = ClassRegistry::init('Mail.MailMessage');
		$MailMessage->createTable(1);
		ClassRegistry::flush();
		$result = $this->Mail->getForm();
		$expected = '/.*<form.*<\/form>.*/s';
		$this->assertRegExp($expected, $result, "メールフォームが取得できません。");
	}

	/**
	 * メールフォームテンプレートを取得
	 */
	public function testGetFormTemplates()
	{
		$View = new View(null);
		$View->set('siteConfig', Configure::read('BcSite'));
		$this->Mail->BcBaser = new BcBaserHelper($View);
		$result = $this->Mail->getFormTemplates();
		$expected = [
			'default' => 'default',
			'smartphone' => 'smartphone'
		];
		$this->assertEquals($result, $expected, 'フォームテンプレートの取得結果が違います。');
	}

	/**
	 * メールテンプレートを取得
	 */
	public function testGetMailTemplates()
	{
		$View = new View(null);
		$View->set('siteConfig', Configure::read('BcSite'));
		$this->Mail->BcBaser = new BcBaserHelper($View);
		$result = $this->Mail->getMailTemplates();
		$expected = [
			'mail_default' => 'mail_default',
			'default' => 'default',
			'reset_password' => 'reset_password',
			'send_activate_url' => 'send_activate_url',
			'send_activate_urls' => 'send_activate_urls'
		];
		$this->assertEquals($result, $expected, 'メールテンプレートの取得結果が違います。');
	}

	/**
	 * ブラウザの戻るボタンを取得
	 */
	public function testGetToken()
	{
		$result = $this->Mail->getToken();
		$expected = '/<script.*<\/script>.*/s';
		$this->assertRegExp($expected, $result, 'スクリプトが取得できません。');
	}

	/**
	 * メールフォームへのリンクを取得
	 * @dataProvider linkProvider
	 */
	public function testLink($title, $contentsName, $expected)
	{
		$this->expectOutputString($expected);
		$this->Mail->link($title, $contentsName, $datas = [], $options = []);
	}

	public function linkProvider()
	{
		return [
			['タイトル', 'Members', '<a href="/Members">タイトル</a>'],
			[' ', 'a', '<a href="/a"> </a>'],
			[' ', ' ', '<a href="/ "> </a>'],
			[' ', '///', '<a href="/"> </a>'],
			[' ', '////a', '<a href="/a"> </a>'],
			[' ', '////a//a/aa', '<a href="/a/a/aa"> </a>'],
			[' ', '/../../../../a', '<a href="/../../../../a"> </a>'],
			['', 'javascript:void(0);', '<a href="/javascript:void(0);"></a>'],
			['<script>alert(1)</script>', '////a', '<a href="/a"><script>alert(1)</script></a>']
		];
	}

	/**
	 * メールコンテンツデータをセット
	 * @dataProvider setMailContentDataProvider
	 */
	public function testSetMailContent($id, $expect)
	{
		unset($this->Mail->mailContent);
		$this->Mail->setMailContent($id);
		$this->assertEquals((bool)($this->Mail->mailContent), $expect);
	}

	public function setMailContentDataProvider()
	{
		return [
			[1, true],
			[2, false]
		];
	}

	/**
	 * ブラウザの戻るボタンの生成結果取得
	 *
	 * public function testToken() {
	 * $this->markTestIncomplete('このメソッドは、同一クラス内のメソッドをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * beforeRender
	 */
	public function testBeforeRender()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
