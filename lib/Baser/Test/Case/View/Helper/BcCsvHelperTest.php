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
App::uses('BcCsvHelper', 'View/Helper');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcTextHelper $Helper
 */
class BcCsvHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array
 */
	public $fixtures = array(
		'baser.Default.SiteConfig',
		'baser.Default.Page',
	);

	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcCsv = new BcCsvHelper($View);
	}

	public function tearDown() {
		unset($this->BcCsv);
		parent::tearDown();
	}


/**
 * データを追加する（単数）
 * 
 * @param string $modelName
 * @param array $data
 * @param string $expectedHead csvHeadの期待値
 * @param string $expectedBody csvBodyの期待値
 * @dataProvider addModelDataDataProvider
 */
	public function testAddModelData($modelName, $data, $expectedHead, $expectedBody) {

		$this->BcCsv->addModelData($modelName, $data);
		$this->assertEquals($expectedHead, $this->BcCsv->csvHead);
		$this->assertEquals($expectedBody, $this->BcCsv->csvBody);

	}

	public function addModelDataDataProvider() {
		return array(

			array(
				'model1',
				array('model1' => array(
					'head' => 'BaserCMS'
					)
				),
				'"head"' . "\n",
				'"BaserCMS"' . "\n"
			),

			array(
				'model1',
				array('model100' => array(
					'head' => 'BaserCMS'
					)
				),
				'',
				''
			),

			array(
				'model1',
				array( 'model1' => array(
					'head' => 'BaserCMS',
					'BaserCMS2',
					'BaserCMS3',
					)
				),
				'"head","0","1"' . "\n",
				'"BaserCMS","BaserCMS2","BaserCMS3"' . "\n"
			),

			array(
				'model1',
				array( 'model1' => array(
					'head' => 'BaserCMS',
					'test1' => 'BaserCMS2',
					'test2' => 'BaserCMS3',
					)
				),
				'"head","test1","test2"' . "\n",
				'"BaserCMS","BaserCMS2","BaserCMS3"' . "\n"
			),

		);
	}


/**
 * データをセットする（複数）
 *
 * @param string $modelName
 * @param array $datas
 * @param string $expectedHead csvHeadの期待値
 * @param string $expectedBody csvBodyの期待値
 * @dataProvider addModelDatasDataProvider
 */
	public function testAddModelDatas($modelName, $datas, $expectedHead, $expectedBody) {
		$datas = array($datas);
		$this->BcCsv->addModelDatas($modelName, $datas);
		$this->assertEquals($expectedHead, $this->BcCsv->csvHead);
		$this->assertEquals($expectedBody, $this->BcCsv->csvBody);
	}

	public function addModelDatasDataProvider() {
		return array(

			array(
				'model1',
				array(
					'model1' => array(
						'head1' => 'BaserCMS1'
					),
					'model1' => array(
						'head2' => 'BaserCMS2'
					),
				),
				'"head2"' . "\n",
				'"BaserCMS2"' . "\n"
			),
			array(
				'model1',
				array(
					'model1' => array(
						'head1' => 'BaserCMS1'
					),
					'model2' => array(
						'head2' => 'BaserCMS2'
					),
				),
				'"head1"' . "\n",
				'"BaserCMS1"' . "\n"
			),


		);
	}

/**
 * CSVファイルをダウンロードする
 *
 * MEMO : header()を扱う場合のテストはエラーがでるため、まだ記述されていません。
 * $debug = true の場合、header()でファイルのダウンロードを実行します。
 *
 * @param string $fileName
 * @param boolean $debug
 * @param string $expected 期待値
 * @dataProvider downloadDataProvider
 */
	public function testDownload($fileName, $debug, $expected) {

		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		// csvのデータを作成
		$modelName = 'sample';
		$data = array(
			'sample' => array(
					'head1' => 'BaserCMS1',
					'head2' => 'BaserCMS2',
					'head3' => 'BaserCMS3',
			)
		);
		$this->BcCsv->addModelData($modelName, $data);

		$result = $this->BcCsv->download($fileName, $debug);

		$this->assertEquals($expected, $result);

	}

	public function downloadDataProvider() {
		return array(
			array('testcsv', true,
				'"head1","head2","head3"' . "\n" .
				'"BaserCMS1","BaserCMS2","BaserCMS3"' . "\n"
			),
			array('testcsv', false,
				'"head1","head2","head3"' . "\n" .
				'"BaserCMS1","BaserCMS2","BaserCMS3"' . "\n"
			),
		);
	}


/**
 * ファイルを保存する
 *
 * @param $fileName
 */
	public function testSave() {

		// csvのデータを作成
		$modelName = 'sample';
		$data = array(
			'sample' => array(
					'head1' => 'BaserCMS1',
					'head2' => 'BaserCMS2',
					'head3' => 'BaserCMS3',
			)
		);
		$this->BcCsv->addModelData($modelName, $data);

		$fileName = "test.csv";
		$expected = '"head1","head2","head3"' . "\n" .
				'"BaserCMS1","BaserCMS2","BaserCMS3"' . "\n";
		$this->BcCsv->save($fileName);
		$this->assertStringEqualsFile($fileName, $expected);

		unlink($fileName);
	}

}