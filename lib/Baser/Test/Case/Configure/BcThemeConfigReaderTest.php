<?php
/**
 * BcThemeConfigReader Test
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */
App::uses('BcThemeConfigReader', 'Configure');

/**
 * BcThemeConfigReader class
 * 
 * @package Baser.Test.Case.Network
 */
class BcThemeConfigReaderTest extends BaserTestCase {

	public $fixtures = array('baser.Page.Page');

/**
 * createContents
 *
 * @param array $data データの配列
 * @param string $expect PHPコード
 * @return void
 * @dataProvider createContentsDataProvider
 */
	public function testCreateContents($data, $expect) {
		$reader = new BcThemeConfigReader();
		$this->assertEquals($expect, $reader->createContents($data));
	}

/**
 * createContents用のデータプロバイダ
 *
 * @return array
 */
	public function createContentsDataProvider() {
		$data = array();
		$contents = <<< EOF
<?php
\$title = 'タイトル';
\$description = 'シングルクォーテーションを含む説明\'';
\$author = '制作者';
\$url = 'http://basercms.net';

EOF;

		$data[] = array(
			array(
				'title' => 'タイトル',
				'description' => "シングルクォーテーションを含む説明'",
				'author' => '制作者',
				'url' => 'http://basercms.net'
			),
			$contents
		);
		return $data;
	}
}