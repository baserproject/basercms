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
App::uses('Tool', 'Model');

/**
 * ToolTest class
 * 
 * class NonAssosiationTool extends Tool {
 *  public $name = 'Tool';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 * @property Tool $Tool
 */
class ToolTest extends BaserTestCase {

	public $fixtures = array();

	public function setUp() {
		parent::setUp();
		$this->Tool = ClassRegistry::init('Tool');
	}

	public function tearDown() {
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
	public function testGetControlSource($field, $expected, $message = null) {
		$result = $this->Tool->getControlSource($field);
		$this->assertContains($expected, $result, $message);
	}

	public function getControlSourceDataProvider() {
		return array(
			array('core', 'mysite_users', 'モデルリストを取得できません'),
			array('plugin', 'mysite_blog_categories', 'モデルリストを取得できません'),
		);
	}

/**
 * データソースを指定してモデルリストを取得する
 * 
 * @param string $configKeyName
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getListModelsDataProvider
 */
	public function testGetListModels($configKeyName, $expected, $message = null) {
		$result = $this->Tool->getControlSource($configKeyName);
		$this->assertContains($expected, $result, $message);
	}

	public function getListModelsDataProvider() {
		return array(
			array('core', 'mysite_users', 'モデルリストを取得できません'),
			array('plugin', 'mysite_blog_categories', 'モデルリストを取得できません'),
		);
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
	public function testWriteSchema($data, $path, $expected, $message = null) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$data = array(
			'Tool' => array(
				'baser' => array(),
				'plugin' => array(),
			),
		);
		$result = $this->Tool->writeSchema($data, $path);
		$this->assertEquals($expected, $result, $message);
	}

	public function writeSchemaDataProvider() {
		return array(
			array('baser', '/', 'mysite_users', 'モデルリストを取得できません'),
		);
	}

/**
 * スキーマファイルを読み込む
 * 
 * @param array $data
 * @param string $tmpPath
 * @return boolean
 */
	public function testLoadSchemaFile() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


}
