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
App::uses('Tool', 'Model');

/**
 * Class ToolTest
 *
 * class NonAssosiationTool extends Tool {
 *  public $name = 'Tool';
 *  public $belongsTo = [];
 *  public $hasMany = [];
 * }
 *
 * @package Baser.Test.Case.Model
 * @property Tool $Tool
 */
class ToolTest extends BaserTestCase
{

	public $fixtures = [];

	public function setUp()
	{
		parent::setUp();
		$this->Tool = ClassRegistry::init('Tool');
	}

	public function tearDown()
	{
		unset($this->Tool);
		parent::tearDown();
	}

	/**
	 * コントロールソース取得
	 *
	 * @param string $field
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getControlSourceDataProvider
	 */
	public function testGetControlSource($field, $expected, $message = null)
	{
		$result = $this->Tool->getControlSource($field);
		$this->assertContains($expected, $result, $message);
	}

	public function getControlSourceDataProvider()
	{
		return [
			['core', 'mysite_users', 'モデルリストを取得できません'],
			['plugin', 'mysite_blog_categories', 'モデルリストを取得できません'],
		];
	}

	/**
	 * データソースを指定してモデルリストを取得する
	 *
	 * @param string $configKeyName
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getListModelsDataProvider
	 */
	public function testGetListModels($configKeyName, $expected, $message = null)
	{
		$result = $this->Tool->getControlSource($configKeyName);
		$this->assertContains($expected, $result, $message);
	}

	public function getListModelsDataProvider()
	{
		return [
			['core', 'mysite_users', 'モデルリストを取得できません'],
			['plugin', 'mysite_blog_categories', 'モデルリストを取得できません'],
		];
	}

	/**
	 * スキーマを書き出す
	 *
	 * @param array $data
	 * @param string $path スキーマファイルの生成場所
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider writeSchemaDataProvider
	 */
	public function testWriteSchema($data, $path, $expected, $message = null)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$data = [
			'Tool' => [
				'baser' => [],
				'plugin' => [],
			],
		];
		$result = $this->Tool->writeSchema($data, $path);
		$this->assertEquals($expected, $result, $message);
	}

	public function writeSchemaDataProvider()
	{
		return [
			['baser', '/', 'mysite_users', 'モデルリストを取得できません'],
		];
	}

	/**
	 * スキーマファイルを読み込む
	 *
	 * @param array $data
	 * @param string $tmpPath
	 * @return boolean
	 */
	public function testLoadSchemaFile()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


}
