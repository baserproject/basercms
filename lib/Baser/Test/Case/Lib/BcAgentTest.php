<?php

/**
 * BcAgentクラスのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
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
		'baser.Page.Page'
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

/**
 * urlMatches用データプロバイダ
 *
 * @return array
 */
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

/**
 * userAgentMatches用データプロバイダ
 *
 * @return array
 */
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

/**
 * shouldRedirects用データプロバイダ
 *
 * @return array
 */
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

/**
 * makeRedirectUrl用データプロバイダ
 *
 * @return array
 */
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