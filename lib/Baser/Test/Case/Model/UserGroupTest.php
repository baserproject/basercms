<?php

/**
 * ユーザーグループモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS UserGroups Community <http://sites.google.com/site/baserUserGroups/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS UserGroups Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('UserGroup', 'Model');

/**
 * UserGroupTest class
 * 
 * class NonAssosiationUserGroup extends UserGroup {
 *  public $name = 'UserGroup';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class UserGroupTest extends BaserTestCase {

  public $fixtures = array(
    'baser.Default.User',
    'baser.Default.UserGroup',
    'baser.Default.Permission',
  );

  public function setUp() {
    parent::setUp();
    $this->UserGroup = ClassRegistry::init('UserGroup');
  }

  public function tearDown() {
    unset($this->UserGroup);
    parent::tearDown();
  }

/**
 * validate
 */
  public function test必須チェック() {
    $this->UserGroup->create(array(
      'UserGroup' => array(
        'name' => '',
        'title' => '',
        'auth_prefix' => '',
      )
    ));
    $this->assertFalse($this->UserGroup->validates());

    $this->assertArrayHasKey('name', $this->UserGroup->validationErrors);
    $this->assertEquals('ユーザーグループ名を入力してください。', current($this->UserGroup->validationErrors['name']));
    $this->assertArrayHasKey('title', $this->UserGroup->validationErrors);
    $this->assertEquals('表示名を入力してください。', current($this->UserGroup->validationErrors['title']));
    $this->assertArrayHasKey('auth_prefix', $this->UserGroup->validationErrors);
    $this->assertEquals('認証プレフィックスを入力してください。', current($this->UserGroup->validationErrors['auth_prefix']));
  }

  public function test桁数チェック正常系() {
    $this->UserGroup->create(array(
      'UserGroup' => array(
        'name' => '12345678901234567890123456789012345678901234567890',
        'title' => '12345678901234567890123456789012345678901234567890',
      )
    ));
    $this->assertTrue($this->UserGroup->validates());
  }

  public function test桁数チェック異常系() {
    $this->UserGroup->create(array(
      'UserGroup' => array(
        'name' => '123456789012345678901234567890123456789012345678901',
        'title' => '123456789012345678901234567890123456789012345678901',
      )
    ));
    $this->assertFalse($this->UserGroup->validates());

    $this->assertArrayHasKey('name', $this->UserGroup->validationErrors);
    $this->assertEquals('ユーザーグループ名は50文字以内で入力してください。', current($this->UserGroup->validationErrors['name']));
    $this->assertArrayHasKey('title', $this->UserGroup->validationErrors);
    $this->assertEquals('表示名は50文字以内で入力してください。', current($this->UserGroup->validationErrors['title']));
  }

  public function test半角英数チェック異常系() {
    $this->UserGroup->create(array(
      'UserGroup' => array(
        'name' => '１２３ａｂｃ',
      )
    ));
    $this->assertFalse($this->UserGroup->validates());

    $this->assertArrayHasKey('name', $this->UserGroup->validationErrors);
    $this->assertEquals('ユーザーグループ名は半角のみで入力してください。', current($this->UserGroup->validationErrors['name']));
  }

  public function test既存ユーザーグループチェック異常系() {
    $this->UserGroup->create(array(
      'UserGroup' => array(
        'name' => 'admins',
      )
    ));
    $this->assertFalse($this->UserGroup->validates());

    $this->assertArrayHasKey('name', $this->UserGroup->validationErrors);
    $this->assertEquals('既に登録のあるユーザーグループ名です。', current($this->UserGroup->validationErrors['name']));
  }

}
