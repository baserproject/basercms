<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('BcGooglemapsHelper', 'View/Helper');
App::uses('Component', 'Controller');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcTextHelper $Helper
 * @property BcGooglemapsHelper $BcGooglemaps
 */
class BcGooglemapsHelperTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Site',
		'baser.Default.SiteConfig'
	];

	public function setUp()
	{
		parent::setUp();
		$View = new View();
		$this->BcGooglemaps = new BcGooglemapsHelper($View);
	}

	public function tearDown()
	{
		unset($this->BcGooglemaps);
		parent::tearDown();
	}


	/**
	 * Google マップ を読み込む
	 *
	 * @param string $address
	 * @param int $width
	 * @param int $height
	 * @param string $expected 期待値
	 * @dataProvider loadDataProvider
	 */
	public function testLoad($address, $width, $height, $expected)
	{

		ob_start();
		$result = $this->BcGooglemaps->load($address, $width, $height);
		$output = ob_get_clean();

		if (!empty($address)) {
			if ($result) {
				$this->assertRegExp('/' . $expected . '/', $output, 'Google マップを正しく出力できません');
			} else {
				$this->markTestIncomplete('GoogleMapの情報の取得に失敗したため、テストをスキップします');
			}

		} else {
			$this->assertRegExp('/' . $expected . '/', $output, 'Google マップを正しく出力できません');
		}

	}

	public function loadDataProvider()
	{
		return [
			['福岡', null, null, '<div id="map">'],
			['福岡', 100, null, '<div id="map" style="width: 100px; height:px">'],
			['福岡', null, 100, '<div id="map" style="width: px; height:100px">'],
			['福岡', 100, 100, '<div id="map" style="width: 100px; height:100px">'],
			['', 100, 100, '^$'],
		];
	}

	/**
	 * 位置情報を読み込む
	 *
	 * @param string $address 位置情報を取得したい住所
	 * @param boolean $expected 期待値
	 * @dataProvider loadLocationDataProvider
	 */
	public function testLoadLocation($address, $expected)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません');

		$this->BcGooglemaps->address = $address;
		$result = $this->BcGooglemaps->loadLocation();
		$this->assertEquals($expected, $result);

	}

	public function loadLocationDataProvider()
	{
		return [
			['福岡', true],
			['', false]
		];
	}

	/**
	 * 位置情報を取得する
	 *
	 * @param string $address 位置情報を取得したい住所
	 * @param array/boolean $expected 期待値
	 * @dataProvider getLocationDataProvider
	 * 2018/05/15 ryuring TravisCI環境にて、タイミングにより、データを取得できず処理に失敗するので一旦コメントアウト
	 * @todo 処理内容を変える等の検討が必要
	 */
//	public function testGetLocation($address, $expected) {
//		$result = $this->BcGooglemaps->getLocation($address);
//
//		if (!empty($address)) {
//
//			if (isset($result['latitude']) && isset($result['longitude'])) {
//				$result['latitude'] = round($result['latitude'], 1);
//				$result['longitude'] = round($result['longitude'], 1);
//				$this->assertEquals($expected, $result, '位置情報を正しく取得できません');
//
//			} else {
//				$this->markTestIncomplete('GoogleMapの情報の取得に失敗したため、テストをスキップします');
//			}
//
//		} else {
//			$this->assertEquals($expected, $result, '位置情報を正しく取得できません');
//		}
//
//	}
//
//	public function getLocationDataProvider() {
//		return [
//			['博多駅', ['latitude' => '33.6', 'longitude' => '130.4']],
//			['fukuoka', ['latitude' => '33.6', 'longitude' => '130.4']],
//			[8100042, ['latitude' => '33.6', 'longitude' => '130.4']],
//			['', false]
//		];
//	}
}