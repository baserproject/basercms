<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Config
 * @since           baserCMS v 3.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('Datasource/CakeSession', 'Model');

/**
 * Class SessionTest
 *
 * @package Baser.Test.Case.Config
 */
class SessionTest extends BaserTestCase
{

	/**
	 * __construct
	 *
	 * @param string $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = null, $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
	}

	/**
	 * Set up
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * モバイルagentによるSession設定テスト
	 * モバイルのUSER_AGENTの場合のみcookieの利用をoff/use_trans_sidを1としURLにSESSION_IDを付帯する
	 *
	 * @param int $expects 予測値
	 * @param string $agent USER_AGENT
	 * @return void
	 *
	 * @dataProvider sessionConfigureMobileDataProvider
	 * @todo PHP 7.1 よりセッションの仕様が変わり、破棄したセッションを session_id() で再開できなくなった
	 * session_start() を行う場合、コンソール出力が事前に発生する為、エラーとなってしまう。
	 * テスト方法の検討が必要
	 */
	public function testSessionConfigureMobile($expects, $agent)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		CakeSession::destroy();

		ini_set('session.use_cookies', 1);
		ini_set('session.use_trans_sid', 1);

		$_SERVER['HTTP_USER_AGENT'] = $agent;

		require APP . 'Config' . DS . 'session.php';
		CakeSession::start();

		$this->assertEquals($expects, [intval(ini_get('session.use_cookies')), intval(ini_get('session.use_trans_sid'))]);
	}

	/**
	 * Session設定用データプロバイダ
	 *
	 * @return array
	 */
	public function sessionConfigureMobileDataProvider()
	{
		return [
			/* iPhone / iOS6 */
			[[1, 1], 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25'],
			/* iPad / iOS6 */
			[[1, 1], 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25'],
			/* Android / Android OS 4.x */
			[[1, 1], 'Mozilla/5.0 (Linux; Android 4.1.1; Nexus 7 Build/JRO03S) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Safari/535.19'],

			/* IE11 */
			[[1, 1], 'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko'],
			/* Chrome */
			[[1, 1], 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.63 Safari/537.36'],


			/* Docomo */
			[[0, 1], 'P501i	DoCoMo/1.0/P501i'],
			[[0, 1], 'DoCoMo/2.0 F901iC(c100;TJ)'],
			/* KDDI */
			[[0, 1], 'KDDI-TS25 UP.Browser/6.0.8.3 (GUI) MMP/1.1'],

			/* SoftBank */
			[[0, 1], 'SoftBank/1.0/831SH/SHJ003/SN123456789012345'],
		];
	}


	/**
	 * HTTPSによるSession設定テスト
	 *
	 * @param int $expects 予測値
	 * @param string $siteUrl BcEnv.siteUrlの値
	 * @param string $sslUrl BcEnv.sslUrlの値
	 * @return void
	 *
	 * @dataProvider sessionConfigureUrlDataProvider
	 * @todo PHP 7.1 よりセッションの仕様が変わり、破棄したセッションを session_id() で再開できなくなった
	 * session_start() を行う場合、コンソール出力が事前に発生する為、エラーとなってしまう。
	 * テスト方法の検討が必要
	 */
	public function testSessionConfigureUrl($expects, $siteUrl, $sslUrl)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		CakeSession::destroy();
		ini_set('session.cookie_secure', 0);
		Configure::write('BcEnv.siteUrl', $siteUrl);
		Configure::write('BcEnv.sslUrl', $sslUrl);

		require APP . 'Config' . DS . 'session.php';
		CakeSession::start();

		$this->assertEquals($expects, intval(ini_get('session.cookie_secure')));
	}

	/**
	 * Session設定用データプロバイダ
	 *
	 * @return array
	 */
	public function sessionConfigureUrlDataProvider()
	{
		return [
			[0, 'https://basercms.net/', ''],
			[0, 'https://basercms.net/', 'https://basercms.net/'],
			[0, 'https://basercms.net/', 'https://basercms.net/'],
			[0, 'https://basercms.net/', ''],
			[1, 'https://basercms.net/', 'https://basercms.net/'],
			[0, 'https://basercms.net:10443/', 'https://basercms.net/'],
			[1, 'https://basercms.net:10443/', 'https://basercms.net:10443/'],
			[0, 'https://basercms.net:10080/', 'https://basercms.net:10443/'],
		];
	}

}
