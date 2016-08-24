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
			['/news/', ['/news/', '/news/index', '/news']],
			['/news/index', ['/news/index', '/news/', '/news']],
			['/news/archives/1', ['/news/archives/1']],
			['/news/archives/index', ['/news/archives/index', '/news/archives/', '/news/archives']]
		];
	}

}