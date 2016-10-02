<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Routing.Filter
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcRequestFilter', 'Routing/Filter');

/**
 * BcRequestFilterTest class
 *
 * @package Baser.Test.Case.Routing.Filter
 */
class BcRequestFilterTest extends BaserTestCase {

/**
 * フィクスチャ
 * @var array
 */
	public $fixtures = array(
		'baser.Default.Page',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.User',
	);

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
	public function setUp() {
		parent::setUp();
		$this->requestFilter = new BcRequestFilter();
	}

/**
 * 管理画面のURLかどうかを判定
 *
 * @param bool $expect 期待値
 * @param string $url URL文字列
 * @return void
 * @dataProvider isAdminDataProvider
 */
	public function testIsAdmin($expect, $url) {
		$request = new CakeRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isAdmin($request));
	}

/**
 * isAdmin用データプロバイダ
 *
 * @return array
 */
	public function isAdminDataProvider() {
		return array(
			array(true, '/admin'),
			array(true, '/admin/'),
			array(true, '/admin/users/login'),
			array(false, '/'),
			array(false, '/s/'),
			array(false, '/news/index'),
			array(false, '/service')
		);
	}

/**
 * アセットのURLかどうかを判定
 *
 * @param bool $expect 期待値
 * @param string $url URL文字列
 * @return void
 * @dataProvider isAssetDataProvider
 */
	public function testIsAsset($expect, $url) {
		$request = new CakeRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isAsset($request));
	}

/**
 * isAsset用データプロバイダ
 *
 * @return array
 */
	public function isAssetDataProvider() {
		return array(
			array(false, '/'),
			array(false, '/about'),
			array(false, '/img/test.html' ),
			array(false, '/js/test.php'),
			array(false, '/css/file.cgi'),
			array(true, '/img/image.png'),
			array(true, '/js/startup.js'),
			array(true, '/css/main.css'),
			array(false, '/theme/example_theme/img/test.html'),
			array(false, '/theme/example_theme/js/test.php'),
			array(false, '/theme/example_theme/css/file.cgi'),
			array(true, '/theme/example_theme/img/image.png'),
			array(true, '/theme/example_theme/js/startup.js'),
			array(true, '/theme/example_theme/css/main.css')
		);
	}

/**
 * インストール用のURLかどうかを判定
 *
 * @param bool $expect 期待値
 * @param string $url URL文字列
 * @return void
 * @dataProvider isInstallDataProvider
 */
	public function testIsInstall($expect, $url) {
		Configure::write('BcRequest.isInstalled', false);
		$request = $this->_getRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isInstall($request));
	}

/**
 * isInstall用データプロバイダ
 *
 * @return array
 */
	public function isInstallDataProvider() {
		return array(
			array(true, '/install'),
			array(true, '/install/'),
			array(false, '/install/index'),
			array(true, '/installations/step2'),
			array(true, '/'),
			array(false, '/service')
		);
	}

/**
 * メンテナンス用のURLかどうかを判定
 *
 * @param bool $expect 期待値
 * @param string $url URL文字列
 * @return void
 * @dataProvider isMaintenanceDataProvider
 */
	public function testIsMaintenance($expect, $url) {
		$request = new CakeRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isMaintenance($request));
	}

/**
 * isMaintenance用データプロバイダ
 *
 * @return array
 */
	public function isMaintenanceDataProvider() {
		return array(
			array(true, '/maintenance'),
			array(true, '/maintenance/'),
			array(true, '/maintenance/index'),
			array(false, '/'),
			array(false, '/service'),
			array(false, '/admin/')
		);
	}

/**
 * アップデート用のURLかどうかを判定
 *
 * @param bool $expect 期待値
 * @param string $url URL文字列
 * @return void
 * @dataProvider isUpdateDataProvider
 */
	public function testIsUpdate($expect, $url) {
		$request = new CakeRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isUpdate($request));
	}

/**
 * isUpdate用データプロバイダ
 *
 * @return array
 */
	public function isUpdateDataProvider() {
		$slug = Configure::read('BcApp.updateKey');
		return array(
			array(true, "/{$slug}"),
			array(true, "/{$slug}/"),
			array(true, "/{$slug}/index"),
			array(false, '/'),
			array(false, '/service'),
			array(false, '/admin/')
		);
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
	public function testIsPage($expect, $url) {
		$request = $this->_getRequest($url);
		$this->assertEquals($expect, $this->requestFilter->isPage($request));
	}

/**
 * isPage用データプロバイダ
 *
 * @return array
 */
	public function isPageDataProvider() {
		return array(
			array(false, '/admin/'),
			array(false, '/news/index'),
			array(true, '/'),
			array(true, '/service'),
			array(true, '/about'),
			array(false, '/recruit')
		);
	}

} 