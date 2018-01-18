<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Network
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */

App::uses('CakeRequest', 'Network');

/**
 * CakeRequest class
 * 
 * @package Baser.Test.Case.Network
 */
class CakeRequestTest extends BaserTestCase {

	public $fixtures = [
		'baser.Default.Page',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.User',
		'baser.Default.SiteConfig',
	];

/**
 * Get the host that the request was handled on.
 */
public function testHost() {
	$this->markTestIncomplete('このテストは、まだ実装されていません。');
}

/**
 * normalizedHere
 *
 * @param string $url URL
 * @param string $expect 正規化されたURL
 * @return void
 * @dataProvider normalizedHereDataProvider
 */
	public function testNormalizedHere($url, $expect) {
		$request = $this->_getRequest($url);
		$this->assertEquals($expect, $request->normalizedHere());
	}

/**
 * normalizedHere用のデータプロバイダ
 *
 * @return array
 */
	public function normalizedHereDataProvider() {
		return [
			['/', '/index'],
			['/news/index', '/news/index'],
			['/news/', '/news/index'],
			['/news', '/news/index']
		];
	}
}