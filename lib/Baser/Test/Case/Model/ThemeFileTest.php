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
App::uses('ThemeFile', 'Model');

/**
 * ThemeFileTest class
 * 
 * class NonAssosiationThemeFile extends ThemeFile {
 *  public $name = 'ThemeFile';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class ThemeFileTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.ThemeConfig',
	);

	public function setUp() {
		parent::setUp();
		$this->ThemeFile = ClassRegistry::init('ThemeFile');
	}

	public function tearDown() {
		unset($this->ThemeFile);
		parent::tearDown();
	}

/**
 * validate
 */
	public function test必須チェック異常系() {
		$this->ThemeFile->create(array(
			'ThemeFile' => array(
				'name' => '',
			)
		));
		$this->assertFalse($this->ThemeFile->validates());
		$this->assertArrayHasKey('name', $this->ThemeFile->validationErrors);
		$this->assertEquals('テーマファイル名を入力してください。', current($this->ThemeFile->validationErrors['name']));
	}

	public function test必須チェック正常系() {
		$this->ThemeFile->create(array(
			'ThemeFile' => array(
				'name' => 'baser',
				'parent' => 'hoge'
			)
		));
		$this->assertTrue($this->ThemeFile->validates());
	}

	public function test重複チェック異常系() {
		$this->ThemeFile->create(array(
			'ThemeFile' => array(
				'name' => 'config.php',
				'parent' => WWW_ROOT . 'theme/nada-icons/',
			)
		));
		$this->assertFalse($this->ThemeFile->validates());
		$this->assertArrayHasKey('name', $this->ThemeFile->validationErrors);
		$this->assertEquals('入力されたテーマファイル名は、同一階層に既に存在します。', current($this->ThemeFile->validationErrors['name']));
	}
}
