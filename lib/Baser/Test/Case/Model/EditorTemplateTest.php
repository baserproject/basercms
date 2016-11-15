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
App::uses('EditorTemplate', 'Model');

/**
 * EditorTemplateTest class
 * 
 * class NonAssosiationEditorTemplate extends EditorTemplate {
 *  public $name = 'EditorTemplate';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class EditorTemplateTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.EditorTemplate',
	);

	public function setUp() {
		parent::setUp();
		$this->EditorTemplate = ClassRegistry::init('EditorTemplate');
	}

	public function tearDown() {
		unset($this->EditorTemplate);
		parent::tearDown();
	}

/**
 * validate
 */
	public function test必須チェック() {
		$this->EditorTemplate->create(array(
			'EditorTemplate' => array(
				'name' => '',
				'link' => '',
			)
		));
		$this->assertFalse($this->EditorTemplate->validates());
		$this->assertArrayHasKey('name', $this->EditorTemplate->validationErrors);
		$this->assertEquals('テンプレート名を入力してください。', current($this->EditorTemplate->validationErrors['name']));
	}


}
