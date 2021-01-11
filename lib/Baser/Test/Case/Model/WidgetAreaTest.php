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
App::uses('WidgetArea', 'Model');

/**
 * Class WidgetAreaTest
 *
 * class NonAssosiationWidgetArea extends WidgetArea {
 *  public $name = 'WidgetArea';
 *  public $belongsTo = [];
 *  public $hasMany = [];
 * }
 *
 * @package Baser.Test.Case.Model
 */
class WidgetAreaTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.WidgetArea',
	];

	public function setUp()
	{
		parent::setUp();
		$this->WidgetArea = ClassRegistry::init('WidgetArea');
	}

	public function tearDown()
	{
		unset($this->WidgetArea);
		parent::tearDown();
	}

	/**
	 * validate
	 */
	public function test必須チェック()
	{
		$this->WidgetArea->create([
			'WidgetArea' => [
				'name' => '',
			]
		]);
		$this->assertFalse($this->WidgetArea->validates());
		$this->assertArrayHasKey('name', $this->WidgetArea->validationErrors);
		$this->assertEquals('ウィジェットエリア名を入力してください。', current($this->WidgetArea->validationErrors['name']));
	}

	public function test桁数チェック正常系()
	{
		$this->WidgetArea->create([
			'WidgetArea' => [
				'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
			]
		]);
		$this->assertTrue($this->WidgetArea->validates());
	}

	public function test桁数チェック異常系()
	{
		$this->WidgetArea->create([
			'WidgetArea' => [
				'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
			]
		]);
		$this->assertFalse($this->WidgetArea->validates());
		$this->assertArrayHasKey('name', $this->WidgetArea->validationErrors);
		$this->assertEquals('ウィジェットエリア名は255文字以内で入力してください。', current($this->WidgetArea->validationErrors['name']));
	}

	/**
	 * コントロールソース取得
	 *
	 * @param string $field
	 */
	public function testGetControlSource()
	{
		$result = $this->WidgetArea->getControlSource('id');
		$this->assertEquals([1 => 'ウィジェットエリア', 2 => 'ブログサイドバー'], $result, 'コントロールソースを取得できません');
	}

}
