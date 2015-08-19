<?php

/**
 * test for BcCkeditorHelper
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS Users Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('View', 'View');
App::uses('BcCkeditorHelper', 'View/Helper');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcTextHelper $Helper
 */
class BcCkeditorHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array
 */
  public $fixtures = array(
    'baser.Default.SiteConfig',
    'baser.Default.Page',
    'baser.Default.PluginContent',
  );

  public function setUp() {
    parent::setUp();
    $View = new View();
    $this->BcCkeditor = new BcCkeditorHelper($View);
  }

  public function tearDown() {
    unset($this->BcCkeditor);
    parent::tearDown();
  }

/**
 * CKEditorのテキストエリアを出力する
 *
 * @param string $fieldName エディタのid, nameなどの名前を指定
 * @param array $options
 * @param boolean $expected 期待値
 * @dataProvider editorDataProvider
 */
  public function testEditor($fieldName, $options, $expected) {

    $expected = '/' . $expected . '/';
    $result = $this->BcCkeditor->editor($fieldName, $options);

    $this->assertRegExp($expected, $result);
  }

/**
 * editor用のデータプロバイダ
 * 
 * @return array
 */
  public function editorDataProvider() {
    return array(
      array('test', array(), 'test'),
      array('test', array('editorLanguage' => 'en'), '"language":"en"'),
      array('test', array('editorSkin' => 'office2013'), '"skin":"office2013"'),
      array('test', array('editorToolbar' => array('test' => '[Anchor]')), '"test":"\[Anchor\]"'),
    );
  }

}