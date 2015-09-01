<?php

/**
 * ThemeFolderモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS ThemeFolders Community <http://sites.google.com/site/baserThemeFolders/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS ThemeFolders Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('ThemeFolder', 'Model');

/**
 * ThemeFolderTest class
 * 
 * class NonAssosiationThemeFolder extends ThemeFolder {
 *  public $name = 'ThemeFolder';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class ThemeFolderTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.ThemeConfig',
	);

	public function setUp() {
		parent::setUp();
		$this->ThemeFolder = ClassRegistry::init('ThemeFolder');
	}

	public function tearDown() {
		unset($this->ThemeFolder);
		parent::tearDown();
	}

/**
 * validate
 */
	public function test必須チェック() {
		$this->ThemeFolder->create(array(
			'ThemeFolder' => array(
				'name' => '',
			)
		));
		$this->assertFalse($this->ThemeFolder->validates());
		$this->assertArrayHasKey('name', $this->ThemeFolder->validationErrors);
		$this->assertEquals('テーマフォルダ名を入力してください。', current($this->ThemeFolder->validationErrors['name']));
	}

	public function test半角英数チェック異常系() {
		$this->ThemeFolder->create(array(
			'ThemeFolder' => array(
				'name' => '１２３ａｂｃ',
			)
		));
		$this->assertFalse($this->ThemeFolder->validates());
		$this->assertArrayHasKey('name', $this->ThemeFolder->validationErrors);
		$this->assertEquals('テーマフォルダ名は半角のみで入力してください。', current($this->ThemeFolder->validationErrors['name']));
	}

	public function test重複チェック異常系() {
		$this->ThemeFolder->create(array(
			'ThemeFolder' => array(
				'name' => 'm-single',
				'pastname' => 'test',
				'parent' => WWW_ROOT . 'theme/',
			)
		));
		$this->assertFalse($this->ThemeFolder->validates());
		$this->assertArrayHasKey('name', $this->ThemeFolder->validationErrors);
		$this->assertEquals('入力されたテーマフォルダ名は、同一階層に既に存在します。', current($this->ThemeFolder->validationErrors['name']));
	}
}
