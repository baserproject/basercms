<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Config
 * @since			baserCMS v 3.0.9
 * @license			http://basercms.net/license/index.html
 */


/**
 * Test class for bootstrap.php
 *
 * @package Baser.Test.Case.Config
 *
 */
class BootstrapTest extends BaserTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
	);

/**
 * __construct
 * 
 * @param string $name
 * @param array $data
 * @param string $dataName
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
	}
	
/**
 * Set up
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * Tear down
 *
 * @return void
 */
	public function tearDown() {
		Router::reload();
		parent::tearDown();
	}


	/**
	 * キャッシュの初期設定をチェックする
	 *
	 * @param string $name 		キャッシュ名
	 * @param string $prefix 	接頭語
	 * @param string $path 		ディレクトリパス
	 * @param string $duration	キャッシュ時間
	 * @dataProvider getCacheSettingDataProvider
	 */
	public function testCacheSettings($name, $prefix, $path, $duration) {
		$config = Cache::config($name);
		$this->assertEquals($prefix, $config['settings']['prefix']);
		$this->assertEquals($duration,$config['settings']['duration']);
		$this->assertTrue(strpos($config['settings']['path'], $path) === 0);
	}

	public function getCacheSettingDataProvider() {
		return array(
			array('_cake_model_', 	'myapp_cake_model_', 	CACHE . 'models' . DS, 		strtotime("+999 days") - time()),
			array('_cake_core_', 	'myapp_cake_core_', 	CACHE . 'persistent' . DS, 	strtotime("+999 days") - time()),
			array('_cake_data_', 	'myapp_cake_data_', 	CACHE . 'datas' . DS, 		strtotime("+999 days") - time()),
			array('_cake_env_', 	'myapp_cake_env_', 		CACHE . 'environment' . DS, strtotime("+999 days") - time()),
		);
	}
}
