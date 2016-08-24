<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('Dblog', 'Model');

/**
 * DblogTest class
 * 
 * class NonAssosiationDblog extends Dblog {
 *  public $name = 'Dblog';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class DblogTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Dblog',
	);

	public function setUp() {
		parent::setUp();
		$this->Dblog = ClassRegistry::init('Dblog');
	}

	public function tearDown() {
		unset($this->Dblog);
		parent::tearDown();
	}

	/**
 * validate
 */
	public function test空白チェック() {
		$this->Dblog->create(array(
			'Dblog' => array(
				'name' => '',
			)
		));
		$this->assertFalse($this->Dblog->validates());
		$this->assertArrayHasKey('name', $this->Dblog->validationErrors);
		$this->assertEquals('ログ内容を入力してください。', current($this->Dblog->validationErrors['name']));
	}

}
