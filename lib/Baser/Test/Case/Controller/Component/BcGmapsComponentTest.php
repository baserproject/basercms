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
 * @property BcGmapsComponent $BcGmaps
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

	public function test_connect() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
/**
 * getInfoLocation
 */
	public function testGetInfoLocation() {
		$this->BcGmaps->getInfoLocation('日本');
		$lat = round($this->BcGmaps->getLatitude(), 1);
		$lng = round($this->BcGmaps->getLongitude(), 1);
		$this->assertEquals(36.2, $lat, '位置情報を正しく取得できません');
		$this->assertEquals(138.3, $lng, '位置情報を正しく取得できません');
	}

	public function testGetLatitude() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function testGetLongitude(){
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
