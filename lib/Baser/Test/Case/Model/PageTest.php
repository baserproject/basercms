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
 * 成功時
 *
 * @param string $code PHPのコード
 * @return void
 * @dataProvider phpValidSyntaxDataProvider
 */
	public function testPhpValidSyntax($code) {
		$this->assertTrue($this->Page->phpValidSyntax(array('contents' => $code)));
	}

/**
 * testPhpValidSyntax 成功時 用データプロバイダ
 *
 * @return array
 */
	public function phpValidSyntaxDataProvider() {
		return array(
			array('<?php $this->BcBaser->setTitle(\'test\');'),
		);
	}

/**
 * PHP構文チェック
 * 失敗時
 *
 * @param string $line エラーが起こる行
 * @param string $code PHPコード
 * @return void
 * @dataProvider phpValidSyntaxWithInvalidDataProvider
 */
	public function testPhpValidSyntaxWithInvalid($line, $code) {
		$this->assertContains("on line {$line}", $this->Page->phpValidSyntax(array('contents' => $code)));
	}

/**
 * testPhpValidSyntax 失敗時 用データプロバイダ
 *
 * @return array
 */
	public function phpValidSyntaxWithInvalidDataProvider() {
		return array(
			array(1, '<?php echo \'test'),
			array(2, '<?php echo \'test\';' . PHP_EOL . 'echo \'hoge')
		);
	}

}
