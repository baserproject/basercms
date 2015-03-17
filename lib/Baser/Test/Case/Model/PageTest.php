<?php
/**
 * ページモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */
App::uses('Page', 'Model');

/**
 * PageTest class
 * 
 * @package Baser.Test.Case.Model
 */
class PageTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Page'
	);

/**
 * Page
 * 
 * @var Page
 */
	public $Page = null;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Page = ClassRegistry::init('Page');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Page);
		parent::tearDown();
	}

/**
 * 固定ページとして管理されているURLかチェックする
 * 
 * @param string $url URL
 * @param bool $expects true Or false
 * @return void
 * @dataProvider isPageUrlDataProvider
 */
	public function testIsPageUrl($url, $expects) {
		$result = $this->Page->isPageUrl($url);
		$this->assertEquals($result, $expects);
	}

/**
 * testIsPageUrl 用データプロバイダ
 *
 * @return array
 */
	public function isPageUrlDataProvider() {
		return array(
			array('/service', true),
			array('/service.html', true),
			array('/servce.css', false),
			array('/', true),
			array('/hoge', false)
		);
	}

/**
 * PHP構文チェック
 *
 * @param array $code PHPのコード
 * @param bool $expected 期待値
 * @return void
 * @dataProvider phpValidSyntaxDataProvider
 */
	public function testPhpValidSyntax($code, $expected) {
		$this->assertEquals($expected, $this->Page->phpValidSyntax($code));
	}

/**
 * testPhpValidSyntax用データプロバイダ
 *
 * @return array
 */
	public function phpValidSyntaxDataProvider() {
		return array(
			array(array('contents' => '<?php $this->BcBaser->setTitle(\'test\');'), true),
			array(array('contents' => '<?php echo "test'), false)
		);
	}
}
