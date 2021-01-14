<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Controller.Component
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcGmapsComponent', 'Controller/Component');
App::uses('Controller', 'Controller');

/**
 * Class BcGmapsTestController
 *
 * @package Baser.Test.Case.Controller.Component
 */
class BcGmapsTestController extends Controller
{

	public $components = ['BcGmaps'];

}

/**
 * Class BcGmapsComponentTest
 *
 * @package Baser.Test.Case.Controller.Component
 * @property BcGmapsComponent $BcGmaps
 */
class BcGmapsComponentTest extends BaserTestCase
{

	public $fixtures = [];

	public $components = ['BcGmaps'];

	public function setUp()
	{
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

	public function tearDown()
	{
		parent::tearDown();
		unset($this->Controller);
		unset($this->BcGmaps);
	}

	public function test_connect()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	/**
	 * getInfoLocation
	 * 2018/05/15 ryuring TravisCI環境にて、タイミングにより、データを取得できず処理に失敗するので一旦コメントアウト
	 * @todo 処理内容を変える等の検討が必要
	 */
//	public function testGetInfoLocation() {
//		$this->BcGmaps->getInfoLocation('日本');
//		$lat = round($this->BcGmaps->getLatitude(), 1);
//		$lng = round($this->BcGmaps->getLongitude(), 1);
//		$this->assertEquals(36.2, $lat, '位置情報を正しく取得できません');
//		$this->assertEquals(138.3, $lng, '位置情報を正しく取得できません');
//	}
//
//	public function testGetLatitude() {
//		$this->markTestIncomplete('このテストは、まだ実装されていません。');
//	}
//
//	public function testGetLongitude(){
//		$this->markTestIncomplete('このテストは、まだ実装されていません。');
//	}

}
