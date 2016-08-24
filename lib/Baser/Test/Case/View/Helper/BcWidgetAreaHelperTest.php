<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
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
 * @param $no ウィジェットエリアNO
 * @param array $options オプション
 * @param string $expected 期待値
 */
	public function testShow () {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}