<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.Test.Case.Model
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('FeedConfig', 'Feed.Model');

class FeedConfigTest extends BaserTestCase
{

	public $fixtures = [
		'plugin.feed.Default/FeedConfig',
		'baser.Default.FeedDetail',
	];

	public function setUp()
	{
		$this->FeedConfig = ClassRegistry::init('Feed.FeedConfig');
		parent::setUp();
	}

	public function tearDown()
	{
		unset($this->FeedConfig);
		parent::tearDown();
	}

	/**
	 * validate
	 */
	public function test必須チェック()
	{
		$this->FeedConfig->create([
			'FeedConfig' => []
		]);

		$this->assertFalse($this->FeedConfig->validates());

		$this->assertArrayHasKey('name', $this->FeedConfig->validationErrors);
		$this->assertEquals('フィード設定名を入力してください。', current($this->FeedConfig->validationErrors['name']));

		$this->assertArrayHasKey('display_number', $this->FeedConfig->validationErrors);
		$this->assertEquals('数値を入力してください。', current($this->FeedConfig->validationErrors['display_number']));
	}

	public function test桁数チェック異常系()
	{
		$this->FeedConfig->create([
			'FeedConfig' => [
				'name' => '123456789012345678901234567890123456789012345678901',
				'feed_title_index' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'category_index' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'display_number' => 123,
				'template' => '123456789012345678901234567890123456789012345678901',
			]
		]);
		$this->assertFalse($this->FeedConfig->validates());

		$this->assertArrayHasKey('name', $this->FeedConfig->validationErrors);
		$this->assertEquals('フィード設定名は50文字以内で入力してください。', current($this->FeedConfig->validationErrors['name']));

		$this->assertArrayHasKey('feed_title_index', $this->FeedConfig->validationErrors);
		$this->assertEquals('フィードタイトルリストは255文字以内で入力してください。', current($this->FeedConfig->validationErrors['feed_title_index']));

		$this->assertArrayHasKey('category_index', $this->FeedConfig->validationErrors);
		$this->assertEquals('カテゴリリストは255文字以内で入力してください。', current($this->FeedConfig->validationErrors['category_index']));

		$this->assertArrayHasKey('template', $this->FeedConfig->validationErrors);
		$this->assertEquals('テンプレート名は50文字以内で入力してください。', current($this->FeedConfig->validationErrors['template']));
	}


	public function test桁数チェック正常系()
	{
		$this->FeedConfig->create([
			'FeedConfig' => [
				'name' => '12345678901234567890123456789012345678901234567890',
				'feed_title_index' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'category_index' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'display_number' => 123,
				'template' => '12345678901234567890123456789012345678901234567890',
			]
		]);

		$this->assertTrue($this->FeedConfig->validates());
	}

	public function testその他異常系()
	{
		// 数値チェック
		$this->FeedConfig->create([
			'FeedConfig' => [
				'display_number' => 'hoge',
			]
		]);

		$this->assertFalse($this->FeedConfig->validates());

		$this->assertArrayHasKey('display_number', $this->FeedConfig->validationErrors);
		$this->assertEquals('数値を入力してください。', current($this->FeedConfig->validationErrors['display_number']));

		// 空白チェック
		$this->FeedConfig->create([
			'FeedConfig' => [
				'template' => '',
			]
		]);

		$this->assertFalse($this->FeedConfig->validates());

		$this->assertArrayHasKey('template', $this->FeedConfig->validationErrors);
		$this->assertEquals('テンプレート名を入力してください。', current($this->FeedConfig->validationErrors['template']));

		// 半角チェック
		$this->FeedConfig->create([
			'FeedConfig' => [
				'template' => 'テスト',
			]
		]);

		$this->assertFalse($this->FeedConfig->validates());

		$this->assertArrayHasKey('template', $this->FeedConfig->validationErrors);
		$this->assertEquals('テンプレート名は半角のみで入力してください。', current($this->FeedConfig->validationErrors['template']));
	}

	/**
	 * 初期値を取得
	 */
	public function testGetDefaultValue()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
