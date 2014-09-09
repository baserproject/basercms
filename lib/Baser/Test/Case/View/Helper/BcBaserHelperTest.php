<?php
/**
 * test for BcBaserHelper
 *
 * PHP versions 5
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */
App::uses('View', 'View');
App::uses('BcBaserHelper', 'View/Helper');

/**
 * BcBaser helper library.
 *
 * @package       Baser.Test.Case
 * @property      BcBaserHelper $BcBaser
 */
class BcBaserHelperTest extends CakeTestCase {
	
/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.Menu'
	);
	
/**
 * setUp
 */
	public function setUp() {
		$this->BcBaser = new BcBaserHelper(new View());
	}
	
/**
 * tearDown
 */
	public function tearDown() {
		unset($this->BcBaser);
		parent::tearDown();
	}
	
/**
 * メニューを取得する
 */
	public function testGetMenus() {
		$result = $this->BcBaser->getMenus();
		$this->assertEqual(count($result), 7);
		$this->assertEqual(isset($result[0]['Menu']['id']), true);
	}
	
}