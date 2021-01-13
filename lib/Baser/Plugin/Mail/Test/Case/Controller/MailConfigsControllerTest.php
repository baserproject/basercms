<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Case.Controller
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('MailAppController', 'Mail.Controller');
App::uses('MailConfigsController', 'Mail.Controller');
App::uses('SessionComponent', 'Controller/Component');

class MailConfigsControllerTest extends BaserTestCase
{

	public $fixtures = [
		// 'baser.Default.SiteConfig',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Plugin',
		'baser.Default.Permission',
		'baser.Default.Favorite',
		'plugin.mail.Default/MailConfig',
	];

	public function setUp()
	{
		$this->MailConfigs = new MailConfigsController(new CakeRequest(null, false), new CakeResponse());

		$this->Case = $this->getMockForAbstractClass('ControllerTestCase');

		parent::setUp();
	}

	public function tearDown()
	{
		unset($this->MailConfigs);
		unset($this->Case);
		parent::tearDown();
	}

	/**
	 * [ADMIN] メールフォーム設定
	 *
	 * @param array $data requestのdata
	 * @dataProvider admin_formDataProvider
	 */
	public function testAdmin_form($data, $expected)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		session_id('baser');
		$this->Case->testAction("admin/mail/mail_configs/form", [
			'method' => 'post', 'data' => $data]);

		if (!empty($data)) {
			$flash = CakeSession::read('Message.flash');
			$this->assertEquals($flash['message'], 'メールフォーム設定を保存しました。');
		}

		$url = $this->Case->headers['Location'];
		$this->assertRegExp('/' . $expected . '/', $url);

	}

	public function admin_formDataProvider()
	{
		return [
			[[], '\/admin\/users\/login'],
			[["MailConfig" => [
				"site_name" => "test"]
			], '\/admin\/mail\/mail_configs\/form']
		];
	}

}
