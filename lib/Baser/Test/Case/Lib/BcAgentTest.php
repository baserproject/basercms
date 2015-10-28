<?php

/**
 * BcAgentクラスのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.1.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('BcAgent', 'Lib');

/**
 * BcAgentTest class
 * 
 * @package Baser.Test.Case.Lib
 */
class BcAgentTest extends BaserTestCase {
/**
 * Fixtures
 * @var array
 */
	public $fixtures = array(
		'baser.Default.Page'
	);

/**
 * @var BcAgent
 */
	public $agent;

/**
 * set up
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->agent = new BcAgent('smartphone', array(
			'alias' => 's',
			'prefix' => 'smartphone',
			'autoRedirect' => true,
			'autoLink' => true,
			'agents' => array(
				'iPhone',			// Apple iPhone
				'iPod',				// Apple iPod touch
				'Android',			// 1.5+ Android
				'dream',			// Pre 1.5 Android
				'CUPCAKE',			// 1.5+ Android
				'blackberry9500',	// Storm
				'blackberry9530',	// Storm
				'blackberry9520',	// Storm v2
				'blackberry9550',	// Storm v2
				'blackberry9800',	// Torch
				'webOS',			// Palm Pre Experimental
				'incognito',		// Other iPhone browser
				'webmate'			// Other iPhone browser
			)
		));

		Configure::write("BcApp.smartphone", true);
	}


/**
 * 名前をキーとしてインスタンスを探す
 *
 * @param string $name 名前
 * @return BcAgent|null
 * @dataProvider findDataProvider
 */
	public function testFind($name, $expect) {
		$result = $this->agent->find($name);
		if (!is_null($result)) {
			$this->assertEquals($expect, $result->name, '設定を正しく読み込めません');
		} else {
			$this->assertNull($result, '存在しないユーザーエージェント名で設定が読み込まれています');
		}
	}

	public function findDataProvider() {
		return array(
			array('mobile', 'mobile'),
			array('smartphone', 'smartphone'),
			array('hoge', null),
		);
	}

/**
 * 設定ファイルに存在する全てのインスタンスを返す
 *
 * @return BcAgent[]
 */
	public function testFindAll() {
		$result = $this->agent->findAll();

		$mobile = new BcAgent('mobile', array(
			'alias' => 'm',
			'prefix' => 'mobile',
			'autoRedirect' => true,
			'autoLink' => true,
			'agents' => array(
				'Googlebot-Mobile',
				'Y!J-SRD',
				'Y!J-MBS',
				'DoCoMo',
				'SoftBank',
				'Vodafone',
				'J-PHONE',
				'UP.Browser'
			),
			'sessionId' => true
		));

		$expect = array(
			$mobile,
			$this->agent
		);

		$this->assertEquals($expect, $result, '設定ファイルに存在するすべてのインスタンスを正しく返すことができません');
	}

/**
 * URL用aliasをキーとしてインスタンスを返す
 *
 * @param string $alias URL用エイリアス
 * @return void
 * @dataProvider findByAliasDataProvider
 */
	public function testFindByAlias($alias, $expect) {
		$result = $this->agent->findByAlias($alias);

		if (!is_null($result)) {
			$this->assertEquals($expect, $result->name, '設定を正しく読み込めません');
		} else {
			$this->assertNull($result, '存在しないエイリアス名で設定が読み込まれています');
		}
	}

	public function findByAliasDataProvider() {
		return array(
			array('m', 'mobile'),
			array('s', 'smartphone'),
			array('hoge', null),
		);
	}

/**
 * HTTPリクエストのURLのプレフィックスに合致するインスタンスを返す
 *
 * @param CakeRequest $request URLをチェックするリクエスト
 * @return void
 * @dataProvider findByUrlDataProvider
 */
	public function testFindByUrl($alias, $expect) {
		$request = new CakeRequest($alias);
		$result = $this->agent->findByUrl($request);
		
		if (!is_null($result)) {
			$this->assertEquals($expect, $result->name, '設定を正しく読み込めません');
		} else {
			$this->assertNull($result, '存在しないエイリアス名で設定が読み込まれています');
		}
	}


	public function findByUrlDataProvider() {
		return array(
			array('m/', 'mobile'),
			array('s/', 'smartphone'),
			array('hoge/', null),
		);
	}

/**
 * 現在の環境のHTTP_USER_AGENTの値に合致するインスタンスを返す
 *
 * @return void
 * @dataProvider findCurrentDataProvider
 */
	public function testFindCurrent($agent, $expect) {
		$_SERVER["HTTP_USER_AGENT"] = $agent;
		$result = $this->agent->findCurrent();

		if (!is_null($result)) {
			$this->assertEquals($expect, $result->name, '設定を正しく読み込めません');
		} else {
			$this->assertNull($result, '存在しないユーザーエージェント名で設定が読み込まれています');
		}
	}

	public function findCurrentDataProvider() {
		return array(
			array('Googlebot-Mobile', 'mobile'),
			array('DoCoMo', 'mobile'),
			array('iPhone', 'smartphone'),
			array('hoge', null),
		);
	}

/**
 * URL文字列からエイリアス文字列を取得
 *
 * @param string $url URL文字列
 * @return void
 * @dataProvider extractAliasDataProvider
 */
	public function testExtractAlias($url, $expect) {
		$result = $this->agent->extractAlias($url);
		
		if (!is_null($result)) {
			$this->assertEquals($expect, $result, 'エイリアス名を正しく読み込めません');
		} else {
			$this->assertNull($result, '存在しないエイリアス名で設定が読み込まれています');
		}

	}

	public function extractAliasDataProvider() {
		return array(
			array('m/', 'm'),
			array('m/hoge', 'm'),
			array('s/', 's'),
			array('hoge/', null),
		);
	}


/**
 * エージェント用の設定が有効かどうかを判定
 *
 * @return bool
 */
	public function testIsEnabled() {
		$result = $this->agent->isEnabled();
		$this->assertTrue($result, 'エージェント用の設定が有効か正しく判定できません');

		$agent_hoge = new BcAgent('hoge', array('agents' => array()));
		$result = $agent_hoge->isEnabled();
		$this->assertFalse($result, 'エージェント用の設定が有効か正しく判定できません');
	}

/**
 * URLがエージェント用かどうかを判定
 *
 * @param bool $expect 期待値
 * @param string $url URL文字列
 * @return void
 * @dataProvider urlMatchesDataProvider
 */
	public function testUrlMatches($expect, $url) {
		$this->assertEquals($expect, $this->agent->urlMatches(new CakeRequest($url)));
	}

	public function urlMatchesDataProvider() {
		return array(
			array(false, '/'),
			array(false, '/service'),
			array(true, '/s'),
			array(true, '/s/'),
			array(true, '/s/service'),
			array(false, '/m/' ),
			array(false, '/m/service')
		);
	}

/**
 * URLがエージェント用かどうかを判定
 *
 * @param bool $expect 期待値
 * @param string $userAgent ユーザーエージェントの文字列
 * @return void
 * @dataProvider userAgentMatchesDataProvider
 */
	public function testUserAgentMatches($expect, $userAgent) {
		$this->assertEquals($expect, $this->agent->userAgentMatches($userAgent));
	}

	public function userAgentMatchesDataProvider() {
		return array(
			array(true, 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4'),
			array(true, 'iPod'),
			array(true, 'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 5 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19'),
			array(true, 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; LT28at Build/6.1.C.1.111) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30'),
			array(false, 'DoCoMo')
		);
	}

/**
 * 与えられたリクエストに対して自動リダイレクトすべきかどうかを返す
 *
 * @param bool $expect 期待値
 * @param string $url URL文字列
 * @param array $query クエリパラメータの配列
 * @return void
 * @dataProvider shouldRedirectsDataProvider
 */
	public function testShouldRedirects($expect, $url, array $query = null) {
		$request = new CakeRequest($url, false);
		$request->query = $query;
		$this->assertEquals($expect, $this->agent->shouldRedirects($request));
	}

	public function shouldRedirectsDataProvider() {
		return array(
			array(false, '/s/'),
			array(false, '/s/news/index'),
			array(false, '/s/service', array('smartphone' => 'on')),
			array(true, '/'),
			array(true, '/news/index'),
			array(true, '/service'),
			array(false, '/news/index', array('smartphone' => 'off')),
			array(true, '/m/'),
			array(true, '/m/service/index'),
			array(false, '/m/service/index', array('smartphone' => 'off'))
		);
	}

/**
 * リクエストをリダイレクトするURLを生成
 *
 * @param string $expect 期待値
 * @param string $url URL文字列
 * @param array $query クエリパラメータの配列
 * @return void
 * @dataProvider makeRedirectUrlDataProvider
 */
	public function testMakeRedirectUrl($expect, $url, $query = null) {
		$request = new CakeRequest($url, false);
		$request->query = $query;
		$this->assertEquals($expect, $this->agent->makeRedirectUrl($request));
	}

	public function makeRedirectUrlDataProvider() {
		return array(
			array('s/', '/'),
			array('s/news/index', '/news/index'),
			array('s/service', '/service'),
			array('s/service?hoge=fuga', '/service', array('hoge' => 'fuga')),
			array('s/', '/m/'),
			array('s/news/index', '/m/news/index'),
			array('s/service', '/m/service'),
			array('s/service/?hoge=fuga', '/m/service/', array('hoge' => 'fuga'))
		);
	}
}