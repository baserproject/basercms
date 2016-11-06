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

	public $fixtures = array(
		'baser.Routing.Route.BcContentsRoute.SiteBcContentsRoute',
		'baser.Routing.Route.BcContentsRoute.ContentBcContentsRoute',
		'baser.Default.SiteConfig',
	);

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
			$_SERVER['HTTP_HOST'] = $host;
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
			['main.com', '', '/news/archives/1', false, false, '/news/archives/1'],
			['main.com', 'SoftBank', '/m/news/archives/1', false, false, '/m/news/archives/1'],
			['main.com', 'iPhone', '/news/archives/1', false, false, '/news/archives/1'],	// 同一URL
			['sub.main.com', '', '/sub/', false, true, '/'],
			['sub.main.com', '', '/sub/news/archives/1', false, true, '/news/archives/1'],
			['another.com', '', '/another.com/', false, true, '/'],
			['another.com', '', '/another.com/news/archives/1', false, true, '/news/archives/1'],
			['another.com', 'iPhone', '/another.com/s/news/archives/1', false, true, '/news/archives/1'],
			// フルURL
			['main.com', '', '/', true, false, 'http://main.com/'],
			['main.com', '', '/news/archives/1', true, false, 'http://main.com/news/archives/1'],
			['main.com', 'SoftBank', '/m/news/archives/1', true, false, 'http://main.com/m/news/archives/1'],
			['main.com', 'iPhone', '/news/archives/1', true, false, 'http://main.com/news/archives/1'],	// 同一URL
			['sub.main.com', '', '/sub/', true, true, 'http://sub.main.com/'],
			['sub.main.com', '', '/sub/news/archives/1', true, true, 'http://sub.main.com/news/archives/1'],
			['another.com', '', '/another.com/', true, true, 'http://another.com/'],
			['another.com', '', '/another.com/news/archives/1', true, true, 'http://another.com/news/archives/1'],
			['another.com', 'iPhone', '/another.com/s/news/archives/1', true, true, 'http://another.com/news/archives/1'],
		];
	}

}