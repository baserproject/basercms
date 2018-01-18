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

App::uses('BcAppView', 'View');
App::uses('BcAppHelper', 'View/Helper');
App::uses('PagesController', 'Controller');
App::uses('BcHtmlHelper', 'View/Helper');

/**
 * ArrayHelper
 *
 * @package Baser.View.Helper
 * @property BcAppHelper $BcAppHelper
 * @property BcHtmlHelper $BcHtmlHelper
 * @property BcAppView $View
 */
class BcAppHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array
 */
	public $fixtures = [
		'baser.View.Helper.BcBaserHelper.SiteConfigBcBaserHelper',
		'baser.Default.User',
		'baser.Default.Site',
		'baser.Default.Content',
	];

	public function setUp() {
		parent::setUp();
		$this->View = new BcAppView(new PagesController($this->_getRequest('/')));
		$this->BcHtmlHelper = new BcHtmlHelper($this->View);
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
 *
 * 改行+タブの文字列がrenderのafterLayoutが呼ばれた後、改行のみに変換されていることを確認するテスト
 */
	public function testAfterLayout() {
		//error時にファイルが残留しないようにするためtryを使用
		try {
			$input = "poge\n\t\thoge";
			$expects = "poge\nhoge";

			$fileName = 'company';
			$path = APP . 'View/Pages/' . $fileName . '.php';
			$fh = fopen($path, 'w');
			fwrite($fh, $input);
			fclose($fh);
			$this->View->set('pagePath', $fileName);
			$output = $this->View->render('templates/default');
			unlink($path);
			$this->assertRegExp('/' . $expects . '/s', $output);
		}catch (Exception $e) {
			echo 'error: ',  $e->getMessage(), "\n";
			//テストを失敗させないとテストが成功して通るため失敗assertion実行
			$this->assertTrue(False, "BcAppHelperTest:testAfterLayoutでエラーがでました。");
		}
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