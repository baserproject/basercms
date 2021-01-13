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
App::uses('ThemeFile', 'Model');
App::uses('File', 'Utility');

/**
 * Class ThemeFileTest
 *
 * class NonAssosiationThemeFile extends ThemeFile {
 *  public $name = 'ThemeFile';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 *
 * @package Baser.Test.Case.Model
 */
class ThemeFileTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.ThemeConfig',
	];

	public function setUp()
	{
		parent::setUp();
		$this->ThemeFile = ClassRegistry::init('ThemeFile');
	}

	public function tearDown()
	{
		unset($this->ThemeFile);
		parent::tearDown();
	}

	/**
	 * validate
	 */
	public function test必須チェック異常系()
	{
		$this->ThemeFile->create([
			'ThemeFile' => [
				'name' => '',
				'parent' => '',
				'ext' => 'php',
				'contents' => ''
			]
		]);
		$this->assertFalse($this->ThemeFile->validates());
		$this->assertArrayHasKey('name', $this->ThemeFile->validationErrors);
		$this->assertEquals('テーマファイル名を入力してください。', current($this->ThemeFile->validationErrors['name']));
	}

	public function test必須チェック正常系()
	{
		$this->ThemeFile->create([
			'ThemeFile' => [
				'name' => 'baser',
				'parent' => 'hoge',
				'ext' => 'php',
				'contents' => ''
			]
		]);
		$this->assertTrue($this->ThemeFile->validates());
	}

	/**
	 * ファイルの重複チェック
	 */
	public function testDuplicateThemeFile()
	{
		$themeFile = new File(TMP . 'test/theme-file.php', true);
		$this->ThemeFile->create([
			'ThemeFile' => [
				'name' => 'another-theme-file',
				'parent' => TMP . 'test/',
				'ext' => 'php',
				'contents' => ''
			]
		]);
		$this->assertTrue($this->ThemeFile->validates(), 'テーマファイルが重複していないにも関わらずバリデーションに失敗しています。');

		$this->ThemeFile->create([
			'ThemeFile' => [
				'name' => 'theme-file',
				'parent' => TMP . 'test/',
				'ext' => 'php',
				'contents' => ''
			]
		]);
		$this->assertFalse($this->ThemeFile->validates(), 'テーマファイルが重複しているにも関わらずバリデーションに成功しています。');

		$themeFile->delete();
		$themeFile->close();
	}

}
