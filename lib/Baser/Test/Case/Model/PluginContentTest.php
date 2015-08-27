<?php

/**
 * PluginContentモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS PluginContents Community <http://sites.google.com/site/baserPluginContents/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS PluginContents Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('PluginContent', 'Model');

/**
 * PluginContentTest class
 * 
 * class NonAssosiationPluginContent extends PluginContent {
 *  public $name = 'PluginContent';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class PluginContentTest extends BaserTestCase {

  public $fixtures = array(
    'baser.Default.PluginContent',
  );

  public function setUp() {
    parent::setUp();
    $this->PluginContent = ClassRegistry::init('PluginContent');
  }

  public function tearDown() {
    unset($this->PluginContent);
    parent::tearDown();
  }

/**
 * validate
 */
  public function testCreate必須チェック() {
    $this->PluginContent->create(array(
      'PluginContent' => array(
        'plugin' => '',
      )
    ));
    $this->assertFalse($this->PluginContent->validates());

    $this->assertArrayHasKey('plugin', $this->PluginContent->validationErrors);
    $this->assertEquals('プラグイン名を入力してください。', current($this->PluginContent->validationErrors['plugin']));
  }

  public function testUpdate必須チェック() {
    $this->PluginContent->save(array(
        'content_id' => '',
        'id' => '1'
      )
    );
    $this->assertFalse($this->PluginContent->validates());

    $this->assertArrayHasKey('content_id', $this->PluginContent->validationErrors);
    $this->assertEquals('コンテンツIDを入力してください。', current($this->PluginContent->validationErrors['content_id']));
  }

  public function test桁数チェック正常系() {
    $this->PluginContent->create(array(
      'PluginContent' => array(
        'name' => '12345678901234567890123456789012345678901234567890',
        'plugin' => '12345678901234567890',
      )
    ));
    $this->assertTrue($this->PluginContent->validates());
  }

  public function test桁数チェック異常系() {
    $this->PluginContent->create(array(
      'PluginContent' => array(
        'name' => '123456789012345678901234567890123456789012345678901',
        'plugin' => '123456789012345678901',
      )
    ));
    $this->assertFalse($this->PluginContent->validates());

    $this->assertArrayHasKey('name', $this->PluginContent->validationErrors);
    $this->assertEquals('コンテンツ名は50文字以内で入力してください。', current($this->PluginContent->validationErrors['name']));
    $this->assertArrayHasKey('plugin', $this->PluginContent->validationErrors);
    $this->assertEquals('プラグイン名は20文字以内で入力してください。', current($this->PluginContent->validationErrors['plugin']));
  }

  public function test重複チェック異常系() {
    $this->PluginContent->create(array(
      'PluginContent' => array(
        'name' => 'news',
      )
    ));
    $this->assertFalse($this->PluginContent->validates());

    $this->assertArrayHasKey('name', $this->PluginContent->validationErrors);
    $this->assertEquals('入力されたコンテンツ名は既に使用されています。', current($this->PluginContent->validationErrors['name']));
  }



}
