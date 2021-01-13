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

App::uses('Router', 'Routing');

/**
 * Class RoutesTest
 *
 * @package Baser.Test.Case.Config
 */
class RoutesTest extends BaserTestCase
{

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.User',
		'baser.Config.Routes.PageRoutes',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.User',
	];

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
		// スーパークラスで、自動的に fixtures に、baser.Default.Page を追加する為、
		// そちらのフィクスチャを読み込ませないようにアンセット
		unset($this->fixtures[array_search('baser.Default.Page', $this->fixtures)]);
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
		Router::reload();
		parent::tearDown();
	}

	/**
	 * URL文字列からルーターのパース結果の配列を得る
	 *
	 * @param string $url URL文字列
	 * @return array
	 */
	protected function _getParams($url)
	{
		$request = $this->_getRequest($url);
		return $request->params;
	}

	/**
	 * インストーラーのルーティングテスト
	 *
	 * @return void
	 */
	public function testInstall()
	{
		Configure::write('BcRequest.isInstalled', false);
		$params = $this->_getParams('install');
		Configure::write('BcRequest.isInstall', true);
		$expects = [
			'named' => [],
			'pass' => [],
			'controller' => 'installations',
			'action' => 'index',
			'plugin' => null
		];
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
	public function testUpdate($url)
	{
		Configure::write('BcRequest.isUpdater', true);
		$params = $this->_getParams($url);
		Configure::write('BcRequest.isUpdater', false);
		$expects = [
			'controller' => 'updaters',
			'action' => 'index',
			'plugin' => null,
			'named' => [],
			'pass' => [],
		];
		$this->assertEquals($expects, $params);
	}

	/**
	 * アップデーター用のデータ
	 *
	 * @return array
	 */
	public function updateDataProvider()
	{
		$updateKey = Configure::read('BcApp.updateKey');

		return [
			["/{$updateKey}"],
			["/{$updateKey}/index"]
		];
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
	public function testPageDisplay($url, $pass)
	{
		$params = $this->_getParams($url);
		$expects = [
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null,
			'named' => [],
			'pass' => $pass,
		];
		unset($params['Content']);
		unset($params['Site']);
		unset($params['entityId']);
		$this->assertEquals($expects, $params);
	}

	/**
	 * 固定ページ用データプロバイダ
	 *
	 * @return array
	 */
	public function pageDisplayDataProvider()
	{
		return [
			['/', ['index']],
			['/about', ['about']],
			['/service', ['service']],
			['/sitemap', ['sitemap']]
		];
	}

	/**
	 * [モバイル]固定ページのルーティングテスト
	 *
	 * @param string $url URL
	 * @param string $pass pass
	 * @return void
	 *
	 * @dataProvider mobilePageDisplayDataProvider
	 */
	public function testMobilePageDisplay($url, $pass)
	{
		$params = $this->_getParams($url);
		$expects = [
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null,
			'named' => [],
			'pass' => $pass,
		];
		unset($params['Content']);
		unset($params['Site']);
		unset($params['entityId']);
		$this->assertEquals($expects, $params);
	}

	/**
	 * [モバイル]固定ページ用データプロバイダ
	 *
	 * @return array
	 *
	 * @todo ページカテゴリを含むテスト及びエージェント対応・連動設定を考慮したテストを追加。
	 */
	public function mobilePageDisplayDataProvider()
	{
		return [
			['/m/', ['m', 'index']]
		];
	}

	/**
	 * [スマートフォン]固定ページのルーティングテスト
	 *
	 * @param string $url URL
	 * @param string $pass pass
	 * @return void
	 *
	 * @dataProvider smartphonePageDisplayDataProvider
	 */
	public function testSmartphonePageDisplay($url, $pass)
	{
		$params = $this->_getParams($url);
		$expects = [
			'controller' => 'pages',
			'action' => 'display',
			'plugin' => null,
			'named' => [],
			'pass' => $pass,
		];
		unset($params['Content']);
		unset($params['Site']);
		unset($params['entityId']);
		$this->assertEquals($expects, $params);
	}

	/**
	 * [スマートフォン]固定ページ用データプロバイダ
	 *
	 * @return array
	 *
	 * @todo ページカテゴリを含むテスト及びエージェント対応・連動設定を考慮したテストを追加。
	 */
	public function smartphonePageDisplayDataProvider()
	{
		return [
			['/s/', ['s', 'index']],
			['/s/service', ['s', 'service']]
		];
	}

}
