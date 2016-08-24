<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Configure
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */
App::uses('BcThemeConfigReader', 'Configure');

/**
 * BcThemeConfigReader Test
 * 
 * @package Baser.Test.Case.Network
 */
class BcThemeConfigReaderTest extends BaserTestCase {

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

		$contents = preg_replace("/\r\n|\r|\n/", PHP_EOL, $contents);

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