<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Routing.Filter
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcRequestFilter', 'Routing/Filter');

/**
 * Class BcRequestFilterTest
 *
 * @package Baser.Test.Case.Routing.Filter
 */
class BcRequestFilterTest extends BaserTestCase
{

	/**
	 * フィクスチャ
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Page',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.User',
	];

	/**
	 * BcRequestFilter
	 * @var BcRequestFilter
	 */
	public $requestFilter;

	/**
	 * set up
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->requestFilter = new BcRequestFilter();
	}

	/**
	 * beforeDispatch Event
	 */
	public function testBeforeDispatch()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * リクエスト検出器の設定を取得
	 */
	public function testGetDetectorConfigs()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * リクエスト検出器を追加する
	 */
	public function testAddDetectors()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 管理画面のURLかどうかを判定
	 *
	 * @param bool $expect 期待値
	 * @param string $url URL文字列
	 * @return void
	 * @dataProvider isAdminDataProvider
	 */
	public function testIsAdmin($expect, $url)
	{
		$request = new CakeRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isAdmin($request));
	}

	/**
	 * isAdmin用データプロバイダ
	 *
	 * @return array
	 */
	public function isAdminDataProvider()
	{
		return [
			[true, '/admin'],
			[true, '/admin/'],
			[true, '/admin/users/login'],
			[false, '/'],
			[false, '/s/'],
			[false, '/news/index'],
			[false, '/service']
		];
	}

	/**
	 * アセットのURLかどうかを判定
	 *
	 * @param bool $expect 期待値
	 * @param string $url URL文字列
	 * @return void
	 * @dataProvider isAssetDataProvider
	 */
	public function testIsAsset($expect, $url)
	{
		$request = new CakeRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isAsset($request));
	}

	/**
	 * isAsset用データプロバイダ
	 *
	 * @return array
	 */
	public function isAssetDataProvider()
	{
		return [
			[false, '/'],
			[false, '/about'],
			[false, '/img/test.html'],
			[false, '/js/test.php'],
			[false, '/css/file.cgi'],
			[true, '/img/image.png'],
			[true, '/js/startup.js'],
			[true, '/css/main.css'],
			[false, '/theme/example_theme/img/test.html'],
			[false, '/theme/example_theme/js/test.php'],
			[false, '/theme/example_theme/css/file.cgi'],
			[true, '/theme/example_theme/img/image.png'],
			[true, '/theme/example_theme/js/startup.js'],
			[true, '/theme/example_theme/css/main.css']
		];
	}

	/**
	 * インストール用のURLかどうかを判定
	 *
	 * @param bool $expect 期待値
	 * @param string $url URL文字列
	 * @return void
	 * @dataProvider isInstallDataProvider
	 */
	public function testIsInstall($expect, $url)
	{
		Configure::write('BcRequest.isInstalled', false);
		$request = $this->_getRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isInstall($request));
	}

	/**
	 * isInstall用データプロバイダ
	 *
	 * @return array
	 */
	public function isInstallDataProvider()
	{
		return [
			[true, '/install'],
			[true, '/install/'],
			[false, '/install/index'],
			[true, '/installations/step2'],
			[true, '/'],
			[false, '/service']
		];
	}

	/**
	 * メンテナンス用のURLかどうかを判定
	 *
	 * @param bool $expect 期待値
	 * @param string $url URL文字列
	 * @return void
	 * @dataProvider isMaintenanceDataProvider
	 */
	public function testIsMaintenance($expect, $url)
	{
		$request = new CakeRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isMaintenance($request));
	}

	/**
	 * isMaintenance用データプロバイダ
	 *
	 * @return array
	 */
	public function isMaintenanceDataProvider()
	{
		return [
			[true, '/maintenance'],
			[true, '/maintenance/'],
			[true, '/maintenance/index'],
			[false, '/'],
			[false, '/service'],
			[false, '/admin/']
		];
	}

	/**
	 * アップデート用のURLかどうかを判定
	 *
	 * @param bool $expect 期待値
	 * @param string $url URL文字列
	 * @return void
	 * @dataProvider isUpdateDataProvider
	 */
	public function testIsUpdate($expect, $url)
	{
		$request = new CakeRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isUpdate($request));
	}

	/**
	 * isUpdate用データプロバイダ
	 *
	 * @return array
	 */
	public function isUpdateDataProvider()
	{
		$slug = Configure::read('BcApp.updateKey');
		return [
			[true, "/{$slug}"],
			[true, "/{$slug}/"],
			[true, "/{$slug}/index"],
			[false, '/'],
			[false, '/service'],
			[false, '/admin/']
		];
	}

	/**
	 * 固定ページ表示用のURLかどうかを判定
	 * [注]ルーターによるURLパース後のみ
	 *
	 * @param bool $expect 期待値
	 * @param string $url URL文字列
	 * @return void
	 * @dataProvider isPageDataProvider
	 */
	public function testIsPage($expect, $url)
	{
		$request = $this->_getRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isPage($request));
	}

	/**
	 * isPage用データプロバイダ
	 *
	 * @return array
	 */
	public function isPageDataProvider()
	{
		return [
			[false, '/admin/'],
			[false, '/news/index'],
			[true, '/'],
			[true, '/service'],
			[true, '/about'],
			[false, '/recruit']
		];
	}

	/**
	 * baserCMSの基本処理を必要とするかどうか
	 */
	public function testIsRequestView()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
