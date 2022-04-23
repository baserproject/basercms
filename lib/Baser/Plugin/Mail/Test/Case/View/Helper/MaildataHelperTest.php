<?php

App::uses('MaildataHelper', 'Mail.View/Helper');
App::uses('BcAppView', 'View');

class MaildataHelperTest extends BaserTestCase
{
	public $View = null;
	public $fixtures = [
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.User',
		'baser.Default.SiteConfig'
	];

	/**
	 * set up
	 */
	public function setUp()
	{
		parent::setUp();
		$this->View = new BcAppView(null);
		$this->View->request = $this->_getRequest('/');
		$this->Maildata = new MaildataHelper($this->View);
	}

	/**
	 * tear down
	 */
	public function tearDown()
	{
		unset($this->Maildata);
		parent::tearDown();
	}
	/**
	 * メール表示用のデータを出力する
	 *
	 * public function testControl() {
	 * $this->markTestIncomplete('このメソッドは、同一クラス内のメソッドをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * メール表示用のデータを出力する
	 * @dataProvider toDisplayStringProvider
	 */
	public function testToDisplayString($type, $value, $prefixSpace, $expected)
	{
		if ($type == 'file') {
			$this->View->set('mailContent', ['MailContent' => ['id' => 1]]);
		}

		$result = $this->Maildata->toDisplayString($type, $value, $prefixSpace);
		$this->assertEquals($result, $expected);
	}

	public function toDisplayStringProvider()
	{
		$options = [
			'資料請求' => '資料請求',
			'お問い合わせ' => 'お問い合わせ',
			'その他' => 'その他'
		];
		$get = [
			'hoge',
			'hello',
			'world'
		];
		return [
			['text', 'hoge', true, ' hoge'],
			['text', 'hoge', false, 'hoge'],
			['textarea', 'hoge', true, 'hoge'],
			['textarea', 'hoge', false, 'hoge'],
			['email', 'hoge', true, ' hoge'],
			['hidden', 'hoge', true, 'hoge'],
			['radio', '', true, ''],
			['radio', 'hoge', true, ' hoge'],
			['select', '', true, ''],
			['select', 'hoge', true, ' hoge'],
			['pref', '', true, ''],
			['pref', '福岡県', true, ' 福岡県'],
			['check', '', true, ''],
			['check', 'hoge', true, 'hoge'],
			['multi_check', '', true, ''],
			['multi_check', $options, true, " ・資料請求\n ・お問い合わせ\n ・その他\n"],
			['multi_check', $options, false, "・資料請求\n・お問い合わせ\n・その他\n"],
			['file', 'hoge', true, '<a href="/admin/mail_messages/attachment/1/hoge">hoge</a>'],
			['file', 'test/hoge.jpg', true, '<a href="/admin/mail_messages/attachment/1/test/hoge.jpg" target="_blank"><img src="/admin/mail_messages/attachment/1/test/hoge.jpg" width="400" alt=""/></a>'],
			['date_time_calender', 'hoge', true, ' 1970年 01月 01日'],
			['date_time_calender', '21000828', true, ' 2100年 08月 28日'],
			['date_time_calender', '2100/08/32', true, ' 1970年 01月 01日'],
			['date_time_calender', '', true, ''],
			['date_time_wareki', 'hoge', true, ''],
			['date_time_wareki', '20200828', true, ' 令和 2年 08月 28日'],
			['date_time_wareki', '19950828', true, ' 平成 7年 08月 28日'],
			['date_time_wareki', '19500828', true, ' 昭和 25年 08月 28日'],
			['date_time_wareki', '1950/08/28', true, ' 昭和 25年 08月 28日'],
			['autozip', '888-0000', true, ' 888-0000'],
			['autozip', '8880000', true, ' 888-0000'],
			['', 'hoge', true, 'hoge']
		];
	}
}
