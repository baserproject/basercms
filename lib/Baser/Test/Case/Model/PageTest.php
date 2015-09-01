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
		'baser.Default.SiteConfig',
		'baser.Default.Page',
		'baser.Default.PageCategory',
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
 * validate
 */
	public function test必須チェック() {
		$this->Page->create(array(
			'Page' => array(
				'name' => '',
			)
		));
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('name', $this->Page->validationErrors);
		$this->assertEquals('ページ名を入力してください。', current($this->Page->validationErrors['name']));
	}

	public function test桁数チェック正常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => '12345678901234567890123456789012345678901234567890',
				'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'description' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
			)
		));
		$this->assertTrue($this->Page->validates());
	}

	public function test桁数チェック異常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => '123456789012345678901234567890123456789012345678901',
				'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'description' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
			)
		));
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('name', $this->Page->validationErrors);
		$this->assertEquals('ページ名は50文字以内で入力してください。', current($this->Page->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->Page->validationErrors);
		$this->assertEquals('ページタイトルは255文字以内で入力してください。', current($this->Page->validationErrors['title']));
		$this->assertArrayHasKey('description', $this->Page->validationErrors);
		$this->assertEquals('説明文は255文字以内で入力してください。', current($this->Page->validationErrors['description']));
	}

	public function test既存ページチェック正常() {
		$this->Page->create(array(
			'Page' => array(
				'name' => 'test',
				'page_category_id' => '1',
			)
		));
		$this->assertTrue($this->Page->validates());
	}

	public function test既存ページチェック異常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => 'index',
				'page_category_id' => '1',
			)
		));
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('name', $this->Page->validationErrors);
		$this->assertEquals('指定したページは既に存在します。ファイル名、またはカテゴリを変更してください。', current($this->Page->validationErrors['name']));
	}

	public function testPHP構文チェック正常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => 'test',
				'contents' => '<?php echo "正しい"; ?>',
			)
		));
		$this->assertTrue($this->Page->validates());
	}

	public function testPHP構文チェック異常系() {
		$this->Page->create(array(
			'Page' => array(
				'name' => 'test',
				'contents' => '<?php ??>',
			)
		));
		$this->assertFalse($this->Page->validates());
		$this->assertArrayHasKey('contents', $this->Page->validationErrors);
		$this->assertEquals("PHPの構文エラーです： \nPHP Parse error:  syntax error, unexpected '?' in - on line 1 \nErrors parsing -", current($this->Page->validationErrors['contents']));
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

	public function phpValidSyntaxWithInvalidDataProvider() {
		return array(
			array(1, '<?php echo \'test'),
			array(2, '<?php echo \'test\';' . PHP_EOL . 'echo \'hoge')
		);
	}

}
