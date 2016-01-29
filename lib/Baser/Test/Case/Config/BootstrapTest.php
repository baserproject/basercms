<?php
/**
 * test for bootstrap.php
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright     Copyright 2008 - 2016, baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @package       Baser.Test.Case.Config
 * @since         baserCMS v 3.0.9
 * @license       http://basercms.net/license/index.html
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
	 * @param string $cacheName キャッシュ名
	 * @param string $expected 期待値
	 * @dataProvider getCacheSettingDataProvider
	 */
	public function testCacheSettings($cacheName, $expected) {
		$config = Cache::config($cacheName);
		$result = array(
			'prefix' => $config['settings']['prefix'],
			'engine' => $config['settings']['engine'],
			'path' => $config['settings']['path'],
			'duration' => $config['settings']['duration'],
			'probability' => $config['settings']['probability'],
		);
		$this->assertEquals($expected,$result);
	}

	public function getCacheSettingDataProvider() {
		return array(
			array('_cake_model_', array(
				'prefix' => 'myapp_cake_model_',
				'engine' => 'File',
				'path' => CACHE . 'models' . DS,
				'duration' => strtotime("+999 days") - time(),
				'probability' => 100,
			)),
			array('_cake_core_', array(
				'prefix' => 'myapp_cake_core_',
				'engine' => 'File',
				'path' => CACHE . 'persistent' . DS,
				'duration' => strtotime("+999 days") - time(),
				'probability' => 100,
			)),
			array('_cake_data_', array(
				'prefix' => 'myapp_cake_data_',
				'engine' => 'File',
				'path' => CACHE . 'datas' . DS,
				'probability' => 100,
				'duration' => strtotime("+999 days") - time(),
			)),
			array('_cake_env_', array(
				'prefix' => 'myapp_cake_env_',
				'engine' => 'File',
				'path' => CACHE . 'environment' . DS,
				'duration' => strtotime("+999 days") - time(),
				'probability' => 100,
			)),
		);
	}

}
