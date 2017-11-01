<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Controller.Component
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('BcGmapsComponent', 'Controller/Component');
App::uses('Controller', 'Controller');


/**
 * 偽コントローラ
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class BcGmapsTestController extends Controller {

	public $components = ['BcGmaps'];

}

/**
 * BcGmapsComponentのテスト
 */
class BcGmapsComponentTest extends BaserTestCase {

	public $fixtures = [];

	public $components = ['BcGmaps'];

	public function setUp() {
		parent::setUp();

		// コンポーネントと偽のテストコントローラをセットアップする
		$request = new CakeRequest();
		$response = $this->getMock('CakeResponse');
		$this->Controller = new BcGmapsTestController($request, $response);

		$collection = new ComponentCollection();
		$collection->init($this->Controller);
		$this->BcGmaps = new BcGmapsComponent($collection);
		$this->BcGmaps->request = $request;
		$this->BcGmaps->response = $response;

		$this->Controller->Components->init($this->Controller);

		Router::reload();
		Router::connect('/:controller/:action/*');
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Controller);
		unset($this->BcGmaps);
	}


/**
 * Construct
 * 
 * @return void
 */
	public function test__construct() {
		$this->BcGmaps->__construct();
		$result = $this->BcGmaps->_baseUrl;
		$expected = "http://" . MAPS_HOST . "/maps/api/geocode/xml?";

		$this->assertEquals($expected, $result, 'APIのURLが正しくありません');
	}

	public function test_connect() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
/**
 * getInfoLocation
 *
 * @param string $address
 * @param string $city
 * @param string $state
 * @return boolean
 */
	public function testGetInfoLocation() {

		$result = $this->BcGmaps->getInfoLocation('日本');
		$this->assertTrue($result, 'getInfoLocationで情報を取得できません');

		$lat = round($this->BcGmaps->getLatitude(), 1);
		$lng = round($this->BcGmaps->getLongitude(), 1);

		$this->assertEquals(36.2, $lat, '位置情報を正しく取得できません');
		$this->assertEquals(138.3, $lng, '位置情報を正しく取得できません');

		$result = $this->BcGmaps->getInfoLocation('');
		$this->assertFalse($result, 'getInfoLocationに空のアドレスにtrueが返ってきます');

	}


	public function testGetLatitude() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function testGetLongitude(){
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
