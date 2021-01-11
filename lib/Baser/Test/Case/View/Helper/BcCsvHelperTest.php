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
App::uses('BcCsvHelper', 'View/Helper');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcTextHelper $Helper
 * @property BcCsvHelper $BcCsv
 */
class BcCsvHelperTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.SiteConfig',
		'baser.Default.Page',
	];

	public function setUp()
	{
		parent::setUp();
		$View = new View();
		$this->BcCsv = new BcCsvHelper($View);
	}

	public function tearDown()
	{
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
	public function testAddModelData($modelName, $data, $expectedHead, $expectedBody)
	{

		$this->BcCsv->addModelData($modelName, $data);
		$this->assertEquals($expectedHead, $this->BcCsv->csvHead);
		$body = '';
		$fp = $this->BcCsv->getCsvTmpDataFp();
		rewind($fp);
		while($line = fgets($fp)) {
			$body .= $line;
		}
		$this->assertEquals($expectedBody, $body);
	}

	public function addModelDataDataProvider()
	{
		return [
			[
				'model1',
				['model1' => [
					'head' => 'BaserCMS'
				]
				],
				'"head"' . "\n",
				'"BaserCMS"' . "\n"
			],

			[
				'model1',
				['model100' => [
					'head' => 'BaserCMS'
				]
				],
				'',
				''
			],

			[
				'model1',
				['model1' => [
					'head' => 'BaserCMS',
					'BaserCMS2',
					'BaserCMS3',
				]
				],
				'"head","0","1"' . "\n",
				'"BaserCMS","BaserCMS2","BaserCMS3"' . "\n"
			],

			[
				'model1',
				['model1' => [
					'head' => 'BaserCMS',
					'test1' => 'BaserCMS2',
					'test2' => 'BaserCMS3',
				]
				],
				'"head","test1","test2"' . "\n",
				'"BaserCMS","BaserCMS2","BaserCMS3"' . "\n"
			],
		];
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
	public function testAddModelDatas($modelName, $datas, $expectedHead, $expectedBody)
	{
		$datas = [$datas];
		$this->BcCsv->addModelDatas($modelName, $datas);
		$this->assertEquals($expectedHead, $this->BcCsv->csvHead);
		$body = '';
		$fp = $this->BcCsv->getCsvTmpDataFp();
		rewind($fp);
		while($line = fgets($fp)) {
			$body .= $line;
		}
		$this->assertEquals($expectedBody, $body);
	}

	public function addModelDatasDataProvider()
	{
		return [
			[
				'model1',
				[
					'model1' => [
						'head1' => 'BaserCMS1'
					],
					'model1' => [
						'head2' => 'BaserCMS2'
					],
				],
				'"head2"' . "\n",
				'"BaserCMS2"' . "\n"
			],

			[
				'model1',
				[
					'model1' => [
						'head1' => 'BaserCMS1'
					],
					'model2' => [
						'head2' => 'BaserCMS2'
					],
				],
				'"head1"' . "\n",
				'"BaserCMS1"' . "\n"
			],

		];
	}

	/**
	 * CSVファイルをダウンロードする
	 *
	 * MEMO : header()を扱う場合のテストはエラーがでるため、まだ記述されていません。
	 * $debug = false の場合、header()でファイルのダウンロードを実行します。
	 *
	 * @param string $fileName
	 * @param boolean $debug
	 * @param string $expected 期待値
	 * @dataProvider downloadDataProvider
	 */
	public function testDownload($fileName, $debug, $expected)
	{
		// csvのデータを作成
		$modelName = 'sample';
		$data = [
			'sample' => [
				'head1' => 'BaserCMS1',
				'head2' => 'BaserCMS2',
				'head3' => 'BaserCMS3',
			]
		];
		$this->BcCsv->addModelData($modelName, $data);
		$result = $this->BcCsv->download($fileName, $debug);
		$this->assertEquals($expected, $result);
	}

	public function downloadDataProvider()
	{
		return [
			['testcsv', true,
				'"head1","head2","head3"' . "\n" .
				'"BaserCMS1","BaserCMS2","BaserCMS3"' . "\n"
			],
			['', true,
				'"head1","head2","head3"' . "\n" .
				'"BaserCMS1","BaserCMS2","BaserCMS3"' . "\n"
			],
		];
	}


	/**
	 * ファイルを保存する
	 *
	 * @param $fileName
	 */
	public function testSave()
	{

		// csvのデータを作成
		$modelName = 'sample';
		$data = [
			'sample' => [
				'head1' => 'BaserCMS1',
				'head2' => 'BaserCMS2',
				'head3' => 'BaserCMS3',
			]
		];
		$this->BcCsv->addModelData($modelName, $data);

		$fileName = "test.csv";
		$expected = '"head1","head2","head3"' . "\n" .
			'"BaserCMS1","BaserCMS2","BaserCMS3"' . "\n";
		$this->BcCsv->save($fileName);
		$this->assertStringEqualsFile($fileName, $expected);

		unlink($fileName);
	}

}
