<?php
/**
* baserCMS :  Based Website Development Project <http://basercms.net>
* Copyright (c) baserCMS Users Community <http://basercms.net/community/>
*
* @copyright		Copyright (c) baserCMS Users Community
* @link			http://basercms.net baserCMS Project
* @package			Baser.Test.Case.Lib
* @since			baserCMS v 4.0.0
* @license			http://basercms.net/license/index.html
*/
App::uses('BcSite', 'Lib');

/**
* BcSiteクラスのテスト
*
* @package Baser.Test.Case.Lib
*/
class BcSiteTest extends BaserTestCase {

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
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$request = new CakeRequest($url, false);
		$request->query = $query;
		$this->assertEquals($expect, $this->agent->shouldRedirects($request));
	}

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
	
}