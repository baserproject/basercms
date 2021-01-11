<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */
App::uses('ThemeFolder', 'Model');

/**
 * Class ThemeFolderTest
 *
 * class NonAssosiationThemeFolder extends ThemeFolder {
 *  public $name = 'ThemeFolder';
 *  public $belongsTo = [];
 *  public $hasMany = [];
 * }
 *
 * @package Baser.Test.Case.Model
 */
class ThemeFolderTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.ThemeConfig',
	];

	public function setUp()
	{
		parent::setUp();
		$this->ThemeFolder = ClassRegistry::init('ThemeFolder');
	}

	public function tearDown()
	{
		unset($this->ThemeFolder);
		parent::tearDown();
	}

	/**
	 * validate
	 */
	public function test必須チェック()
	{
		$this->ThemeFolder->create([
			'ThemeFolder' => [
				'name' => '',
			]
		]);
		$this->assertFalse($this->ThemeFolder->validates());
		$this->assertArrayHasKey('name', $this->ThemeFolder->validationErrors);
		$this->assertEquals('テーマフォルダ名を入力してください。', current($this->ThemeFolder->validationErrors['name']));
	}

	public function test半角英数チェック異常系()
	{
		$this->ThemeFolder->create([
			'ThemeFolder' => [
				'name' => '１２３ａｂｃ',
			]
		]);
		$this->assertFalse($this->ThemeFolder->validates());
		$this->assertArrayHasKey('name', $this->ThemeFolder->validationErrors);
		$this->assertEquals('テーマフォルダ名は半角のみで入力してください。', current($this->ThemeFolder->validationErrors['name']));
	}

	public function test重複チェック異常系()
	{
		$this->ThemeFolder->create([
			'ThemeFolder' => [
				'name' => 'nada-icons',
				'pastname' => 'test',
				'parent' => WWW_ROOT . 'theme/',
			]
		]);
		$this->assertFalse($this->ThemeFolder->validates());
		$this->assertArrayHasKey('name', $this->ThemeFolder->validationErrors);
		$this->assertEquals('入力されたテーマフォルダ名は、同一階層に既に存在します。', current($this->ThemeFolder->validationErrors['name']));
	}

	/**
	 * フォルダの重複チェック
	 */
	public function testDuplicateThemeFolder()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
