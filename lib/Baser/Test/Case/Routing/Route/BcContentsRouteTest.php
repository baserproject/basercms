<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Routing.Route
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcContentsRoute', 'Routing/Route');

/**
 * BcRequestFilterTest class
 *
 * @package Baser.Test.Case.Routing.Route
 * @property BcContentsRoute $BcContentsRoute
 */
class BcContentsRouteTest extends BaserTestCase {


/**
 * フィクスチャ
 * @var array
 */
	public $fixtures = [
		'baser.Default.Site',
		'baser.Default.Content',
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
		$this->BcContentsRoute = new BcContentsRoute(
			'/',
			[],
			[]
		);;
	}

/**
 * リバースルーティング
 * 
 * @param string $current 現在のURL
 * @param string $params URLパラメーター
 * @param string $expects 期待するURL
 * @dataProvider reverseRoutingDataProvider
 */
	public function testReverseRouting($current, $params, $expects) {
		Router::setRequestInfo($this->_getRequest($current));
		$this->assertEquals($expects, Router::url($params));
	}

	public function reverseRoutingDataProvider() {
		return [
			[	// ContentFolder
				'/',
				['plugin' => null, 'controller' => 'content_folders', 'action' => 'view', 'entityId' => 1],
				'/'
			],
			[	// Page
				'/',
				['plugin' => null, 'controller' => 'pages', 'action' => 'display', 'entityId' => 1],
				'/index'
			],
			[
				'/',
				['plugin' => null, 'controller' => 'pages', 'action' => 'display', 'entityId' => 2],
				'/about'
			],
			[	// Blog
				'/', 
				['plugin' => 'blog', 'controller' => 'blog', 'action' => 'index', 'entityId' => 1], 
				'/news/'
			],
			[
				'/',
				['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 2], 
				'/news/archives/2'
			],
			[
				'/',
				['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 'page' => 2, 2],
				'/news/archives/2/page:2'
			],
			[
				'/',
				['plugin' => 'blog', 'controller' => 'blog', 'action' => 'archives', 'entityId' => 1, 'category', 'release'],
				'/news/archives/category/release'
			],
			[	// named page
				'/', 
				['page' => 2],
				'/index/page:2'
			],
			[
				'/news/index',
				['page' => 2],
				'/news/index/page:2'
			],
		];
	}
	
/**
 * 管理画面のURLかどうかを判定
 *
 * @param bool $expect 期待値
 * @param string $url URL文字列
 * @return void
 * @dataProvider getUrlPatternDataProvider
 */
	public function testGetUrlPattern($url, $expects) {
		$this->assertEquals($expects, $this->BcContentsRoute->getUrlPattern($url));
	}

/**
 * getUrlPattern 用データプロバイダ
 *
 * @return array
 */
	public function getUrlPatternDataProvider() {
		return [
			['/news', ['/news']],
			['/news/', ['/news/', '/news/index']],
			['/news/index', ['/news/index', '/news/']],
			['/news/archives/1', ['/news/archives/1']],
			['/news/archives/index', ['/news/archives/index', '/news/archives/']]
		];
	}

}