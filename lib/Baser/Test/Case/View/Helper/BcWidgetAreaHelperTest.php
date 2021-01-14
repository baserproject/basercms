<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.View.Helper
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('BcWidgetAreaHelper', 'View/Helper');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcTextHelper $Helper
 * @property BcWidgetAreaHelper $BcWidgetArea
 */
class BcWidgetAreaHelperTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.WidgetArea',
	];

	public function setUp()
	{
		parent::setUp();
		$View = new View();
		$this->BcWidgetArea = new BcWidgetAreaHelper($View);
	}

	public function tearDown()
	{
		unset($this->BcWidgetArea);
		parent::tearDown();
	}

	/**
	 * ウィジェットエリアを表示する
	 *
	 * @param $no ウィジェットエリアNO
	 * @param array $options オプション
	 * @param string $expected 期待値
	 * @dataProvider showDataProvider
	 *
	 * MEMO: $pathがわからないため保留
	 */
	public function testShow($fileName, $no, $expected)
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$path = APP . 'Elements/widgets/' . $fileName . '.ctp';
		$fh = fopen($path, 'w');
		fwrite($fh, '東京' . PHP_EOL . '埼玉' . PHP_EOL . '大阪' . PHP_EOL);
		fclose($fh);

		ob_start();
		//エラーでファイルが残留するため,tryで確実に削除を実行
		try {
			$this->BcWidgetArea->show($no);
		} catch (Exception $e) {
			echo 'error: ', $e->getMessage(), "\n";
		}
		$result = ob_get_clean();
		unlink($path);

		pr($result);
		$this->assertEquals($expected, $result);
	}

	public function showDataProvider()
	{
		return [
			['test', 1, ''],
			['test', 2, '']
		];
	}

}
