<?php

/**
 * ページモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */
App::uses('Page', 'Model');

/**
 * PageTest class
 * 
 * @package Baser.Test.Case.Model
 */
class UserTest extends BaserTestCase {
	
	public $fixtures = array(
		'baser.Page'
	);
	
/**
 * Page
 * 
 * @var Page
 */
	public $Page = null;

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->Page = ClassRegistry::init('Page');
	}

/**
 * tearDown
 */
	public function tearDown() {
		unset($this->Page);
		parent::tearDown();
	}
	
/**
 * 固定ページとして管理されているURLかチェックする
 * 
 * @param string $url URL
 * @param boolean $expects true Or false
 * @dataProvider isPageUrlDataProvider
 */
	public function testIsPageUrl($url, $expects) {
		$result = $this->Page->isPageUrl($url);
		$this->assertEqual($result, $expects);
	}
	
/**
 * testIsPageUrl 用データプロバイダ
 */
	public function isPageUrlDataProvider() {
		return array(
			array('/service', true),
			array('/service.html', true),
			array('/servce.css', false),
			array('/', true),
			array('/hoge', false)
		);
	}
	
}