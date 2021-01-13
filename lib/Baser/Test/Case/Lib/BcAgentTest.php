<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Lib
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */
App::uses('BcAgent', 'Lib');

/**
 * Class BcAgentTest
 *
 * @package Baser.Test.Case.Lib
 */
class BcAgentTest extends BaserTestCase
{
	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Page',
		'baser.Default.Site',
	];

	/**
	 * @var BcAgent
	 */
	public $agent;

	/**
	 * set up
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->agent = new BcAgent('smartphone', [
			'alias' => 's',
			'prefix' => 'smartphone',
			'autoRedirect' => true,
			'autoLink' => true,
			'agents' => [
				'iPhone',            // Apple iPhone
				'iPod',                // Apple iPod touch
				'Android',            // 1.5+ Android
				'dream',            // Pre 1.5 Android
				'CUPCAKE',            // 1.5+ Android
				'blackberry9500',    // Storm
				'blackberry9530',    // Storm
				'blackberry9520',    // Storm v2
				'blackberry9550',    // Storm v2
				'blackberry9800',    // Torch
				'webOS',            // Palm Pre Experimental
				'incognito',        // Other iPhone browser
				'webmate'            // Other iPhone browser
			]
		]);

		Configure::write("BcApp.smartphone", true);
	}


	/**
	 * 名前をキーとしてインスタンスを探す
	 *
	 * @param string $name 名前
	 * @return BcAgent|null
	 * @dataProvider findDataProvider
	 */
	public function testFind($name)
	{
		$result = $this->agent->find($name);
		if (!is_null($result)) {
			$this->assertEquals($name, $result->name, '設定を正しく読み込めません');
		} else {
			$this->assertNull($result, '存在しないユーザーエージェント名で設定が読み込まれています');
		}
	}

	public function findDataProvider()
	{
		return [
			['mobile'],
			['smartphone'],
			['hoge'],
		];
	}

	/**
	 * 設定ファイルに存在する全てのインスタンスを返す
	 *
	 * @return BcAgent[]
	 */
	public function testFindAll()
	{
		$result = $this->agent->findAll();

		$mobile = new BcAgent('mobile', [
			'alias' => 'm',
			'prefix' => 'mobile',
			'autoRedirect' => true,
			'autoLink' => true,
			'agents' => [
				'Googlebot-Mobile',
				'Y!J-SRD',
				'Y!J-MBS',
				'DoCoMo',
				'SoftBank',
				'Vodafone',
				'J-PHONE',
				'UP.Browser'
			],
			'sessionId' => true
		]);

		$expect = [
			$mobile,
			$this->agent
		];

		$this->assertEquals($expect, $result, '設定ファイルに存在するすべてのインスタンスを正しく返すことができません');
	}

	/**
	 * 現在の環境のHTTP_USER_AGENTの値に合致するインスタンスを返す
	 *
	 * @param string $agent ユーザーエージェント名
	 * @param string $expect 期待値
	 * @return void
	 * @dataProvider findCurrentDataProvider
	 */
	public function testFindCurrent($agent, $expect)
	{
		$_SERVER["HTTP_USER_AGENT"] = $agent;
		$result = $this->agent->findCurrent();

		if (!is_null($result)) {
			$this->assertEquals($expect, $result->name, '設定を正しく読み込めません');
		} else {
			$this->assertNull($result, '存在しないユーザーエージェント名で設定が読み込まれています');
		}
	}

	public function findCurrentDataProvider()
	{
		return [
			['Googlebot-Mobile', 'mobile'],
			['DoCoMo', 'mobile'],
			['iPhone', 'smartphone'],
			['hoge', null],
		];
	}

	/**
	 * ユーザーエージェントの判定用正規表現を取得
	 */
	public function testGetDetectorRegex()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * URLがエージェント用かどうかを判定
	 *
	 * @param bool $expect 期待値
	 * @param string $userAgent ユーザーエージェントの文字列
	 * @return void
	 * @dataProvider isMatchDecisionKeyDataProvider
	 */
	public function testIsMatchDecisionKey($expect, $userAgent)
	{
		$_SERVER['HTTP_USER_AGENT'] = $userAgent;
		$this->assertEquals($expect, $this->agent->isMatchDecisionKey());
	}

	public function isMatchDecisionKeyDataProvider()
	{
		return [
			[true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4'],
			[true, 'iPod'],
			[true, 'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 5 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19'],
			[true, 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; LT28at Build/6.1.C.1.111) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30'],
			[false, 'DoCoMo']
		];
	}

}
