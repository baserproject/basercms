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
 * @property BcWidgetAreaHelper $BcWidgetArea
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
 * @dataProvider showDataProvider
 *
 * MEMO: ディレクトリが存在しない場合、自動で作成されずパスエラーになる(Blog.ElementsとElementsを一緒にテストできない)
 */
	public function testShow($widgetPath, $fileName, $no, $expected) {
		$path =  realpath('lib') . '/Baser/View/'. $widgetPath . '/' . $fileName . '.ctp';

		$fh = fopen($path, 'w');
		fwrite($fh, '東京' . PHP_EOL . '埼玉' . PHP_EOL . '大阪' . PHP_EOL);
		fclose($fh);

		ob_start();
		//エラーでファイルが残留するため,tryで確実に削除を実行
		try {
			$this->BcWidgetArea->show($no);
		}catch (Exception $e) {
			echo 'error: ',  $e->getMessage(), "\n";
		}
		$result = ob_get_clean();
		unlink($path);

		$this->assertRegExp('/' . $expected . '/', $result);
	}

	public function showDataProvider() {
		return array(
			array('Elements/widgets', 'text', 1, '東京\n埼玉\n大阪\n'),
			array('Elements/widgets', 'blog_category_archives', 2, '東京\n埼玉\n大阪\n東京\n埼玉\n大阪\n'),
//			array('Blog.Elements/widgets', 'blog_monthly_archives', 5, '東京\n埼玉\n大阪\n'),
			array('Elements/widgets', '', 1, '東京\n埼玉\n大阪\n')
		);
	}

}