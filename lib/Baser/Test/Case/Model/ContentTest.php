<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
 * @since			baserCMS v 4.0.1
 * @license			http://basercms.net/license/index.html
 */

App::uses('Content', 'Model');

/**
 * ContentTest class
 *
 * @package Baser.Test.Case.Model
 * @property Content $Content
 */
class ContentTest extends BaserTestCase {

	public $fixtures = [
		'baser.Model.Content.ContentStatusCheck',
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
	public function setUp() {
		parent::setUp();
		$this->Content = ClassRegistry::init('Content');
		BcSite::flash();
	}

/**
 * testGetUrl
 *
 * $param string $host ホスト名
 * $param string $ua ユーザーエージェント名
 * @param string $url 変換前URL
 * @param boolean $full フルURLで出力するかどうか
 * @param boolean $useSubDomain サブドメインを利用するかどうか
 * @param string $expects 期待するURL
 * @dataProvider getUrlDataProvider
 */
	public function testGetUrl($host, $ua, $url, $full, $useSubDomain, $expects) {
		$siteUrl = Configure::read('BcEnv.siteUrl');
		Configure::write('BcEnv.siteUrl', 'http://main.com');
		if($ua) {
			$_SERVER['HTTP_USER_AGENT'] = $ua;
		}
		if($host) {
			Configure::write('BcEnv.host', $host);
		}
		Router::setRequestInfo($this->_getRequest('/m/'));
		$result = $this->Content->getUrl($url, $full, $useSubDomain);
		$this->assertEquals($result, $expects);
		Configure::write('BcEnv.siteUrl', $siteUrl);
	}

	public function getUrlDataProvider() {
		return [
			// ノーマルURL
			['main.com', '', '/', false, false, '/'],
			['main.com', '', '/index', false, false, '/'],
			['main.com', '', '/news/archives/1', false, false, '/news/archives/1'],
			['main.com', 'SoftBank', '/m/news/archives/1', false, false, '/m/news/archives/1'],
			['main.com', 'iPhone', '/news/archives/1', false, false, '/news/archives/1'],	// 同一URL
			['sub.main.com', '', '/sub/', false, true, '/'],
			['sub.main.com', '', '/sub/index', false, true, '/'],
			['sub.main.com', '', '/sub/news/archives/1', false, true, '/news/archives/1'],
			['another.com', '', '/another.com/', false, true, '/'],
			['another.com', '', '/another.com/index', false, true, '/'],
			['another.com', '', '/another.com/news/archives/1', false, true, '/news/archives/1'],
			['another.com', 'iPhone', '/another.com/s/news/archives/1', false, true, '/news/archives/1'],
			// フルURL
			['main.com', '', '/', true, false, 'http://main.com/'],
			['main.com', '', '/index', true, false, 'http://main.com/'],
			['main.com', '', '/news/archives/1', true, false, 'http://main.com/news/archives/1'],
			['main.com', 'SoftBank', '/m/news/archives/1', true, false, 'http://main.com/m/news/archives/1'],
			['main.com', 'iPhone', '/news/archives/1', true, false, 'http://main.com/news/archives/1'],	// 同一URL
			['sub.main.com', '', '/sub/', true, true, 'http://sub.main.com/'],
			['sub.main.com', '', '/sub/index', true, true, 'http://sub.main.com/'],
			['sub.main.com', '', '/sub/news/archives/1', true, true, 'http://sub.main.com/news/archives/1'],
			['another.com', '', '/another.com/', true, true, 'http://another.com/'],
			['another.com', '', '/another.com/index', true, true, 'http://another.com/'],
			['another.com', '', '/another.com/news/archives/1', true, true, 'http://another.com/news/archives/1'],
			['another.com', 'iPhone', '/another.com/s/news/archives/1', true, true, 'http://another.com/news/archives/1'],
		];
	}

/**
 * testGetUrl の base テスト
 * 
 * @param $url
 * @param $base
 * @param $expects
 * @dataProvider getUrlBaseDataProvider
 */
	public function testGetUrlBase($url, $base, $useBase, $expects) {
		Configure::write('app.baseUrl', $base);
		$request = $this->_getRequest('/');
		$request->base = $base;
		Router::setRequestInfo($request);
		$result = $this->Content->getUrl($url, false, false, $useBase);
		$this->assertEquals($result, $expects);
	}
	
	public function getUrlBaseDataProvider() {
		return [
			['/news/archives/1', '', true, '/news/archives/1'],
			['/news/archives/1', '', false, '/news/archives/1'],
			['/news/archives/1', '/sub', true, '/sub/news/archives/1'],
			['/news/archives/1', '/sub', false, '/news/archives/1'],
		];
	}

/**
 * testCreateUrl
 * 
 * @param int $id コンテンツID
 * @param string $expects 期待するURL
 * @dataProvider createUrlDataProvider
 */
	public function testCreateUrl($id, $expects) {
		$this->assertEquals($this->Content->createUrl($id), $expects);
	}
	
	public function createUrlDataProvider() {
		return [
			["hogehoge'/@<>1",''],
			[1, '/'],
			[2, '/m/'],
			[3, '/s/'],
			[4, '/index'],
			[5, '/service/'],
			[6, '/m/index'],
			[7, '/service/contact/'],
			[8, '/news/'],
			[9, '/service/service1'],
			[10, '/s/index'],
			[11, '/s/news/'],
			[12, '/s/service/'],
			[13, '/en/'],
			[14, '/sub/'],
			[15, '/another.com/'],
			[16, '/s/service/contact/'],
			[17, '/m/news/'],
			[18, '/en/news/'],
			[19, '/sub/news/'],
			[20, '/another.com/news/'],
			[21, '/en/service/'],
			[22, '/en/service/service1'],
			[23, '/sub/service/'],
			[24, '/sub/service/service1'],
			[25, '/another.com/service/'],
			[26, '/m/service/'],
			[27, '/m/service/contact/'],
			[28, '/en/service/contact/'],
			[29, '/sub/service/contact/'],
			[30, '/another.com/service/contact/'],
			[31, '/m/service/service1'],
			[32, '/s/service/service1'],
			[33, '/another.com/service/service1'],
			[34, '/en/index'],
			[35, '/sub/index'],
			[36, '/another.com/index'],
			[37, '/another.com/s/'],
			[38, '/another.com/s/index'],
			[39, '/another.com/s/news/'],
			[40, '/another.com/s/service/'],
			[41, '/another.com/s/service/service1'],
			[42, '/another.com/s/service/contact/'],
		];
	}

/**
 * URLからコンテンツを取得する
 *
 * TODO sameUrl / useSubDomain のテストが書けていない
 * Siteのデータを用意する必要がある
 * 
 * @param string $url
 * @param string $publish
 * @param bool $extend
 * @param bool $sameUrl
 * @param bool $useSubDomain
 * @param bool $expected
 * @dataProvider findByUrlDataProvider
 */
	public function testFindByUrl($expected, $url, $publish = true, $extend = false, $sameUrl = false, $useSubDomain = false) {
		$this->loadFixtures('ContentStatusCheck');
		$result = (bool) $this->Content->findByUrl($url, $publish, $extend, $sameUrl, $useSubDomain);
		$this->assertEquals($expected, $result);
	}

	public function findByUrlDataProvider() {
		return [
			[true, '/about', true],
			[false, '/service', true],
			[true, '/service', false],
			[false, '/hoge', false],
			[true, '/news/archives/1', true, true],
			[false, '/news/archives/1', true, false],
		];
	}

}