<?php
/**
 * test for routes.php
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright     Copyright 2008 - 2014, baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @package       Baser.Test.Case.Config
 * @since         baserCMS v 3.0.6
 * @license       http://basercms.net/license/index.html
 */

App::uses('Router', 'Routing');

/**
 * Test class for routes.php
 *
 * @package Baser.Test.Case.Config
 *
 */
class BaserRoutesTest extends BaserTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'baser.User',
		'baser.Page',
		'baser.PageCategory',
		'baser.PluginContent'
	);

/**
 * Set up
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * Tear down
 *
 * @return void
 */
	public function tearDown() {
		Router::reload();
		parent::tearDown();
	}

/**
 * URL文字列からルーターのパース結果の配列を得る
 *
 * @param string $url URL文字列
 * @return array
 */
	protected function _getParams($url) {
		$request = new CakeRequest($url);
		Router::setRequestInfo($request);
		$params = Router::parse($request->url);
		//Debugger::dump($params);
		return $params;
	}

/**
 * インストーラーのルーティングテスト
 *
 * @return void
 */
	public function testInstall() {
		$params = $this->_getParams('install');
		$expects = array(
			'named' => array(),
			'pass' => array(),
			'controller' => 'installations',
			'action' => 'index',
			'plugin' => null
		);
		$this->assertEquals($expects, $params);
	}

/**
 * アップデーターのルーティングテスト
 *
 * @param string $url URL
 * @return void
 *
 * @dataProvider updateDataProvider
 */
	public function testUpdate($url) {
		$params = $this->_getParams($url);
		$expects = array(
			'controller' => 'updaters',
			'action' => 'index',
			'plugin' => null,
			'named' => array(),
			'pass' => array(),
		);
		$this->assertEquals($expects, $params);
	}

/**
 * アップデーター用のデータ
 *
 * @return array
 */
	public function updateDataProvider() {
		$updateKey = Configure::read('BcApp.updateKey');

		return array(
			array("/{$updateKey}"),
			array("/{$updateKey}/index")
		);
	}

/**
 * 固定ページのルーティングテスト
 *
 * @param string $url URL
 * @param string $pass pass
 * @return void
 *
 * @dataProvider pageDisplayDataProvider
 */
	public function testPageDisplay($url, $pass) {
		$params = $this->_getParams($url);
		$expects = array(
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null,
			'named' => array(),
			'pass' => $pass,
		);
		$this->assertEquals($expects, $params);
	}

/**
 * 固定ページ用データプロバイダ
 *
 * @return array
 *
 * @todo ページカテゴリを含むテスト追加。PageCategoryのフィクスチャを変更してテスト専用のデータにするべきか
 * @todo モバイル・スマホ向けのテスト追加。bootstrap.phpのにおける処理が関わっているので後回し
 */
	public function pageDisplayDataProvider() {
		return array(
			array('/', array('index')),
			array('/company', array('company')),
			array('/service', array('service')),
			array('/recruit', array('recruit'))
		);
	}

/**
 * プラグインコンテンツのルーティングテスト
 *
 * @param array $pluginContent プラグインコンテンツのレコードの配列
 * @return void
 *
 * @dataProvider pluginContentDataProvider
 */
	public function testPluginContent(array $pluginContent) {
		$url = $pluginContent['name'];
		$params = $this->_getParams($url);
		$expects = array(
			'controller' => $pluginContent['plugin'],
			'action' => 'index',
			'plugin' => $pluginContent['plugin'],
			'named' => array(),
			'pass' => array()
		);

		$this->assertEquals($expects, $params);
	}

/**
 * プラグインコンテンツ用データプロバイダ
 *
 * @return array
 */
	public function pluginContentDataProvider() {
		return ClassRegistry::init('PluginContent')->find('all');
	}
}