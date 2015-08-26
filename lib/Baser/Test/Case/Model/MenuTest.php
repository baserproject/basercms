<?php

/**
 * メニューモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Menus Community <http://sites.google.com/site/baserMenus/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS Menus Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('Menu', 'Model');

/**
 * MenuTest class
 * 
 * class NonAssosiationMenu extends Menu {
 *  public $name = 'Menu';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class MenuTest extends BaserTestCase {

  public $fixtures = array(
    'baser.Default.Menu',
  );

  public function setUp() {
    parent::setUp();
    $this->Menu = ClassRegistry::init('Menu');
  }

  public function tearDown() {
    unset($this->Menu);
    parent::tearDown();
  }

/**
 * validate
 */
  public function test必須チェック() {
    $this->Menu->create(array(
      'Menu' => array(
        'name' => '',
        'link' => '',
      )
    ));
    $this->assertFalse($this->Menu->validates());
    $this->assertArrayHasKey('name', $this->Menu->validationErrors);
    $this->assertEquals('メニュー名を入力してください。', current($this->Menu->validationErrors['name']));
    $this->assertArrayHasKey('link', $this->Menu->validationErrors);
    $this->assertEquals('リンクURLを入力してください。', current($this->Menu->validationErrors['link']));
  }

  public function test桁数チェック正常系() {
    $this->Menu->create(array(
      'Menu' => array(
        'name' => '12345678901234567890',
        'link' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
      )
    ));
    $this->assertTrue($this->Menu->validates());
  }

  public function test桁数チェック異常系() {
    $this->Menu->create(array(
      'Menu' => array(
        'name' => '123456789012345678901',
        'link' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
      )
    ));
    $this->assertFalse($this->Menu->validates());
    $this->assertArrayHasKey('name', $this->Menu->validationErrors);
    $this->assertEquals('メニュー名は20文字以内で入力してください。', current($this->Menu->validationErrors['name']));
    $this->assertArrayHasKey('link', $this->Menu->validationErrors);
    $this->assertEquals('リンクURLは255文字以内で入力してください。', current($this->Menu->validationErrors['link']));
  }

/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getControlSourceDataProvider
 */
  public function testGetControlSource($field, $expected, $message = null) {
    $result = $this->Menu->getControlSource('menu_type');
    $this->assertEquals($expected, $result, $message);
  }

/**
 * getControlSource用データプロバイダ
 *
 * @return array
 */
  public function getControlSourceDataProvider() {
    return array(
      array('menu_type', array('default' => '公開ページ','admin' => '管理画面'), 'コントロールソースを取得できません'),
    );
  }

}
