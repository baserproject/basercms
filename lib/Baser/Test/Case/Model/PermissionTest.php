<?php

/**
 * Permissionモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Permissions Community <http://sites.google.com/site/baserPermissions/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS Permissions Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('Permission', 'Model');

/**
 * PermissionTest class
 * 
 * class NonAssosiationPermission extends Permission {
 *  public $name = 'Permission';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class PermissionTest extends BaserTestCase {

  public $fixtures = array(
    'baser.Default.Page',
    'baser.Default.PluginContent',
    'baser.Default.Permission',
  );

  public function setUp() {
    parent::setUp();
    $this->Permission = ClassRegistry::init('Permission');
  }

  public function tearDown() {
    unset($this->Permission);
    parent::tearDown();
  }

/**
 * validate
 */
  public function test必須チェック() {
    $this->Permission->create(array(
      'Permission' => array(
        'name' => '',
        'url' => '',
      )
    ));
    $this->assertFalse($this->Permission->validates());
    $this->assertArrayHasKey('name', $this->Permission->validationErrors);
    $this->assertEquals('設定名を入力してください。', current($this->Permission->validationErrors['name']));
    $this->assertArrayHasKey('user_group_id', $this->Permission->validationErrors);
    $this->assertEquals('ユーザーグループを選択してください。', current($this->Permission->validationErrors['user_group_id']));
    $this->assertArrayHasKey('url', $this->Permission->validationErrors);
    $this->assertEquals('設定URLを入力してください。', current($this->Permission->validationErrors['url']));
  }

  public function test桁数チェック正常系() {
    $this->Permission->create(array(
      'Permission' => array(
        'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
        'user_group_id' => '1',
        'url' => '/admin/12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
      )
    ));
    $this->assertTrue($this->Permission->validates());
  }

  public function test桁数チェック異常系() {
    $this->Permission->create(array(
      'Permission' => array(
        'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
        'user_group_id' => '1',
        'url' => '/admin/1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
      )
    ));
    $this->assertFalse($this->Permission->validates());
    $this->assertArrayHasKey('name', $this->Permission->validationErrors);
    $this->assertEquals('設定名は255文字以内で入力してください。', current($this->Permission->validationErrors['name']));
    $this->assertArrayHasKey('url', $this->Permission->validationErrors);
    $this->assertEquals('設定URLは255文字以内で入力してください。', current($this->Permission->validationErrors['url']));
  }

  public function testアクセス拒否チェック異常系() {
    $this->Permission->create(array(
      'Permission' => array(
        'user_group_id' => '1',
        'url' => '/index',
      )
    ));
    $this->assertFalse($this->Permission->validates());
    $this->assertArrayHasKey('url', $this->Permission->validationErrors);
    $this->assertEquals('アクセス拒否として設定できるのは認証ページだけです。', current($this->Permission->validationErrors['url']));
  }
  public function testアクセス拒否チェック正常系() {
    $this->Permission->create(array(
      'Permission' => array(
        'user_group_id' => '1',
        'url' => '/admin/index',
      )
    ));
    $this->assertTrue($this->Permission->validates());
  }
}
