<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Routing.Route
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcContentsRoute', 'Routing/Route');

/**
 * Class BcRequestFilterTest
 *
 * @package Baser.Test.Case.Routing.Route
 * @property BcContentsRoute $BcContentsRoute
 */
class BcContentsRouteTest extends BaserTestCase
{


	/**
	 * フィクスチャ
	 * @var array
	 */
	public $fixtures = [
		'baser.Routing.Route.BcContentsRoute.SiteBcContentsRoute',
		'baser.Routing.Route.BcContentsRoute.ContentBcContentsRoute',
		'baser.Default.SiteConfig',
		'baser.Default.User',
	];

	/**
	 * set up
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->BcContentsRoute = new BcContentsRoute(
			'/',
			[],
			[]
		);
		BcSite::flash();
	}

	/**
	 * Parses a string URL into an array. If a plugin key is found, it will be copied to the
	 * controller parameter
	 */
	public function testParse()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンテンツに関連するパラメーター情報を取得する
	 */
	public function testGetParams()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * Reverse route
	 */
	public function testMatch()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * リバースルーティング
	 *
	 * @param string $current 現在のURL
	 * @param string $params URLパラメーター
	 * @param string $expects 期待するURL
	 * @dataProvider reverseRoutingDataProvider
	 */
	public function testReverseRouting($current, $params, $expects)
	{
		Router::setRequestInfo($this->_getRequest($current));
		$this->assertEquals($expects, Router::url($params));
	}

	public function reverseRoutingDataProvider()
	{
		return [
			// ContentFolder
			['/', ['plugin' => null, 'controller' => 'content_folders', 'action' => 'view', 'entityId' => 1], '/'],
			// Page
			['/', ['plugin' => null, 'controller' => 'pages', 'action' => 'display', 'index'], '/index'],
			['/', ['plugin' => null, 'controller' => 'pages', 'action' => 'display', 'service', 'service1'], '/service/service1'],
			// Blog
			['/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => 1], '/news/'],
			['/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 2], '/news/archives/2'],
			['/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 'page' => 2, 2], '/news/archives/2/page:2'],
			['/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 'category', 'release'], '/news/archives/category/release'],
			['/', ['page' => 2], '/page:2'],
			['/news/index', ['page' => 2], '/news/index/page:2'],
		];
	}

	/**
	 * Router::parse
	 *
	 * @param string $url URL文字列
	 * @param string $expect 期待値
	 * @return void
	 * @dataProvider routerParseDataProvider
	 */
	public function testRouterParse($useSiteDeviceSetting, $host, $ua, $url, $expects)
	{
		$siteUrl = Configure::read('BcEnv.siteUrl');
		Configure::write('BcSite.use_site_device_setting', $useSiteDeviceSetting);
		Configure::write('BcEnv.siteUrl', 'http://main.com');
		if ($ua) {
			$_SERVER['HTTP_USER_AGENT'] = $ua;
		}
		if ($host) {
			Configure::write('BcEnv.host', $host);
		}
		Router::setRequestInfo($this->_getRequest($url));
		$this->assertEquals($expects, Router::parse($url));
		Configure::write('BcEnv.siteUrl', $siteUrl);
	}

	public function routerParseDataProvider()
	{
		return [
			// PC（ノーマル : デバイス設定無）
			[0, '', '', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['index'], 'named' => []]],
			[0, '', '', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['index'], 'named' => []]],
			[0, '', '', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			[0, '', '', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			[0, '', '', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			[0, '', '', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '1', 'pass' => [1], 'named' => []]],
			[0, '', '', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => ['page' => 1]]],
			[0, '', '', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '4', 'pass' => [], 'named' => []]],
			[0, '', '', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '3', 'pass' => ['service', 'service1'], 'named' => []]],
			[0, '', '', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// モバイル（別URL : デバイス設定有）
			[1, '', 'SoftBank', '/m/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '4', 'pass' => ['m', 'index'], 'named' => []]],
			[1, '', 'SoftBank', '/m/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '4', 'pass' => ['m', 'index'], 'named' => []]],
			[1, '', 'SoftBank', '/m/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '2', 'pass' => [], 'named' => []]],
			[1, '', 'SoftBank', '/m/news', ['plugin' => '', 'controller' => 'm', 'action' => 'news', 'pass' => [], 'named' => []]],
			[1, '', 'SoftBank', '/m/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '2', 'pass' => [], 'named' => []]],
			[1, '', 'SoftBank', '/m/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '2', 'pass' => [1], 'named' => []]],
			[1, '', 'SoftBank', '/m/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '2', 'pass' => [], 'named' => ['page' => 1]]],
			[1, '', 'SoftBank', '/m/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '11', 'pass' => [], 'named' => []]],
			[1, '', 'SoftBank', '/m/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '10', 'pass' => ['m', 'service', 'service1'], 'named' => []]],
			[1, '', 'SoftBank', '/m/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// スマホ（同一URL / エイリアス : デバイス設定有）
			[1, '', 'iPhone', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['s', 'index'], 'named' => []]],
			[1, '', 'iPhone', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['s', 'index'], 'named' => []]],
			[1, '', 'iPhone', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			[1, '', 'iPhone', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			[1, '', 'iPhone', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			[1, '', 'iPhone', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '1', 'pass' => [1], 'named' => []]],
			[1, '', 'iPhone', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => ['page' => 1]]],
			[1, '', 'iPhone', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '4', 'pass' => [], 'named' => []]],
			[1, '', 'iPhone', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '3', 'pass' => ['s', 'service', 'service1'], 'named' => []]],
			[1, '', 'iPhone', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// スマホ（ノーマル : デバイス設定無）
			[0, '', 'iPhone', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['index'], 'named' => []]],
			[0, '', 'iPhone', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '1', 'pass' => ['index'], 'named' => []]],
			[0, '', 'iPhone', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			[0, '', 'iPhone', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			[0, '', 'iPhone', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			[0, '', 'iPhone', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '1', 'pass' => [1], 'named' => []]],
			[0, '', 'iPhone', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => ['page' => 1]]],
			[0, '', 'iPhone', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '4', 'pass' => [], 'named' => []]],
			[0, '', 'iPhone', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '3', 'pass' => ['service', 'service1'], 'named' => []]],
			[0, '', 'iPhone', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// PC（英語 : デバイス設定無）
			[0, '', '', '/en/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '12', 'pass' => ['en', 'index'], 'named' => []]],
			[0, '', '', '/en/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '12', 'pass' => ['en', 'index'], 'named' => []]],
			[0, '', '', '/en/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '3', 'pass' => [], 'named' => []]],
			[0, '', '', '/en/news', ['plugin' => '', 'controller' => 'en', 'action' => 'news', 'pass' => [], 'named' => []]],
			[0, '', '', '/en/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '3', 'pass' => [], 'named' => []]],
			[0, '', '', '/en/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '3', 'pass' => [1], 'named' => []]],
			[0, '', '', '/en/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '3', 'pass' => [], 'named' => ['page' => 1]]],
			[0, '', '', '/en/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '8', 'pass' => [], 'named' => []]],
			[0, '', '', '/en/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '8', 'pass' => ['en', 'service', 'service1'], 'named' => []]],
			[0, '', '', '/en/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// PC（サブドメイン : デバイス設定無）
			[0, 'sub.main.com', '', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '13', 'pass' => ['sub', 'index'], 'named' => []]],
			[0, 'sub.main.com', '', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '13', 'pass' => ['sub', 'index'], 'named' => []]],
			[0, 'sub.main.com', '', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '4', 'pass' => [], 'named' => []]],
			[0, 'sub.main.com', '', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			[0, 'sub.main.com', '', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '4', 'pass' => [], 'named' => []]],
			[0, 'sub.main.com', '', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '4', 'pass' => [1], 'named' => []]],
			[0, 'sub.main.com', '', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '4', 'pass' => [], 'named' => ['page' => 1]]],
			[0, 'sub.main.com', '', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '9', 'pass' => [], 'named' => []]],
			[0, 'sub.main.com', '', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '9', 'pass' => ['sub', 'service', 'service1'], 'named' => []]],
			[0, 'sub.main.com', '', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// PC（別ドメイン : デバイス設定無）
			[0, 'another.com', '', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '14', 'pass' => ['another.com', 'index'], 'named' => []]],
			[0, 'another.com', '', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '14', 'pass' => ['another.com', 'index'], 'named' => []]],
			[0, 'another.com', '', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '5', 'pass' => [], 'named' => []]],
			[0, 'another.com', '', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			[0, 'another.com', '', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '5', 'pass' => [], 'named' => []]],
			[0, 'another.com', '', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '5', 'pass' => [1], 'named' => []]],
			[0, 'another.com', '', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '5', 'pass' => [], 'named' => ['page' => 1]]],
			[0, 'another.com', '', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '10', 'pass' => [], 'named' => []]],
			[0, 'another.com', '', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '11', 'pass' => ['another.com', 'service', 'service1'], 'named' => []]],
			[0, 'another.com', '', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '1', 'pass' => [], 'named' => []]],
			// スマホ（別ドメイン / 同一URL / 別コンテンツ : デバイス設定有）
			[1, 'another.com', 'iPhone', '/', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '15', 'pass' => ['another.com', 's', 'index'], 'named' => []]],
			[1, 'another.com', 'iPhone', '/index', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '15', 'pass' => ['another.com', 's', 'index'], 'named' => []]],
			[1, 'another.com', 'iPhone', '/news/', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '6', 'pass' => [], 'named' => []]],
			[1, 'another.com', 'iPhone', '/news', ['plugin' => '', 'controller' => 'news', 'action' => 'index', 'pass' => [], 'named' => []]],
			[1, 'another.com', 'iPhone', '/news/index', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '6', 'pass' => [], 'named' => []]],
			[1, 'another.com', 'iPhone', '/news/archives/1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => '6', 'pass' => [1], 'named' => []]],
			[1, 'another.com', 'iPhone', '/news/index/page:1', ['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => '6', 'pass' => [], 'named' => ['page' => 1]]],
			[1, 'another.com', 'iPhone', '/service/', ['plugin' => '', 'controller' => 'content_folders', 'action' => 'view', 'entityId' => '13', 'pass' => [], 'named' => []]],
			[1, 'another.com', 'iPhone', '/service/service1', ['plugin' => '', 'controller' => 'pages', 'action' => 'display', 'entityId' => '16', 'pass' => ['another.com', 's', 'service', 'service1'], 'named' => []]],
			[1, 'another.com', 'iPhone', '/service/contact/', ['plugin' => 'mail', 'controller' => 'mail', 'action' => 'index', 'entityId' => '2', 'pass' => [], 'named' => []]],
		];
	}

}
