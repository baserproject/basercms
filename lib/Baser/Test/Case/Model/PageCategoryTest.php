<?php

/**
 * PageCategoryモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS PageCategorys Community <http://sites.google.com/site/baserPageCategorys/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS PageCategorys Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('PageCategory', 'Model');

/**
 * PageCategoryTest class
 * 
 * class NonAssosiationPageCategory extends PageCategory {
 *  public $name = 'PageCategory';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class PageCategoryTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Page',
		'baser.Default.PageCategory',
	);

	public function setUp() {
		parent::setUp();
		$this->PageCategory = ClassRegistry::init('PageCategory');
	}

	public function tearDown() {
		unset($this->PageCategory);
		parent::tearDown();
	}

/**
 * validate
 */
	public function test必須チェック() {
		$this->PageCategory->create(array(
			'PageCategory' => array()
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリ名を入力してください。', current($this->PageCategory->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリタイトルを入力してください。', current($this->PageCategory->validationErrors['title']));
	}

	public function test桁数チェック正常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => '12345678901234567890123456789012345678901234567890',
				'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'parent_id' => null,
			)
		));
		$this->assertTrue($this->PageCategory->validates());
	}

	public function test桁数チェック異常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => '123456789012345678901234567890123456789012345678901',
				'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'parent_id' => null,
			)
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリ名は50文字以内で入力してください。', current($this->PageCategory->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリタイトルは255文字以内で入力してください。', current($this->PageCategory->validationErrors['title']));
	}

	public function test半角英数チェック異常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => '１２３ａｂｃ',
				'title' => 'hoge',
				'parent_id' => null,
			)
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリ名は半角英数字とハイフン、アンダースコアのみで入力してください。', current($this->PageCategory->validationErrors['name']));
	}

	public function test重複チェック親カテゴリなし異常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => 'mobile',
				'parent_id' => null,
			)
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('入力されたページカテゴリー名は、同一階層に既に登録されています。', current($this->PageCategory->validationErrors['name']));
	}

	public function test重複チェック親カテゴリあり異常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => 'garaphone',
				'parent_id' => 2,
			)
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('入力されたページカテゴリー名は、同一階層に既に登録されています。', current($this->PageCategory->validationErrors['name']));
	}
}
