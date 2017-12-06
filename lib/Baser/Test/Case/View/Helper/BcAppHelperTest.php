<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('BcAppHelper', 'View/Helper');
App::uses('BcHtmlHelper', 'View/Helper');

/**
 * ArrayHelper
 *
 * @package Baser.View.Helper
 * @property BcAppHelper $BcAppHelper
 * @property BcHtmlHelper $BcHtmlHelper
 * @property View $View
 */
class BcAppHelperTest extends BaserTestCase {
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcHtmlHelper = new BcHtmlHelper($View);
	}

	public function tearDown() {
		parent::tearDown();
	}

/**
 * コンストラクタ
 */
	public function test__construct() {
		$this->assertEquals('<input type="checkbox" name="%s[]"%s />&nbsp;', $this->BcHtmlHelper->_tags['checkboxmultiple'], "コンストラクタの結果が違います。");
	}

/**
 * 出力時にインデント用のタブを除去
 */
	public function testAfterLayout() {
		//改行後にタブがついている文字列が変換されることのテスト
		$this->BcHtmlHelper->_View->output = '
					aa';
		$this->BcHtmlHelper->afterLayout('');
		$this->assertRegExp('/' . '\naa' . '/', $this->BcHtmlHelper->_View->output);

		//改行なしのタブ付きは変換されない
		$this->BcHtmlHelper->_View->output = '			a';
		$this->BcHtmlHelper->afterLayout('');
		$this->assertRegExp('/' . '\t\t\ta' . '/', $this->BcHtmlHelper->_View->output);
	}

	public function testDispatchEvent() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function testUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function testWebroot() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
}