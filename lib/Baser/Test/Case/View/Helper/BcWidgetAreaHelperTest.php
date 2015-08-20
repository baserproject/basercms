<?php

/**
 * test for BcWidgetAreaHelper
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
App::uses('BcWidgetAreaHelper', 'View/Helper');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcTextHelper $Helper
 */
class BcWidgetAreaHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array
 */
  public $fixtures = array(
    'baser.Default.WidgetArea',
  );

  public function setUp() {
    parent::setUp();
    $View = new View();
    $this->BcWidgetArea = new BcWidgetAreaHelper($View);
  }

  public function tearDown() {
    unset($this->BcWidgetArea);
    parent::tearDown();
  }

/**
 * ウィジェットエリアを表示する
 *
 * MEMO : エラーに対処できずスキップしています。
 * this->BcWidgetArea->show(1);
 * → Element Not Found: Elements/widgets/text.ctp
 *
 *
 * @param $no ウィジェットエリアNO
 * @param array $options オプション
 * @param string $expected 期待値
 * @dataProvider showDataProvider
 */
  public function testShow ($no, $options, $expected) {
    $this->markTestIncomplete('このテストは、まだ実装されていません。');
    
    $this->expectOutputRegex($expected);
    $this->BcWidgetArea->show($no);
  }

/**
 * download用のデータプロバイダ
 *
 * @return array
 */
  public function showDataProvider() {
    return array(
      array("1", array(), "fas"),
    );
  }

}