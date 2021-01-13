<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Configure
 * @since           baserCMS v 3.0.7
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcThemeConfigReader', 'Configure');

/**
 * Class BcThemeConfigReaderTest
 *
 * @package Baser.Test.Case.Configure
 */
class BcThemeConfigReaderTest extends BaserTestCase
{

	/**
	 * 指定されたテーマ名の設定ファイルを読み込む
	 */
	public function testRead()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 与えられた連想配列を設定ファイルにPHPコードとして保存する
	 * 追記ではなく上書きする
	 */
	public function testDump()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


	/**
	 * createContents
	 *
	 * @param array $data データの配列
	 * @param string $expect PHPコード
	 * @return void
	 * @dataProvider createContentsDataProvider
	 */
	public function testCreateContents($data, $expect)
	{
		$reader = new BcThemeConfigReader();
		$this->assertEquals($expect, $reader->createContents($data));
	}

	/**
	 * createContents用のデータプロバイダ
	 *
	 * @return array
	 */
	public function createContentsDataProvider()
	{
		$data = [];
		$contents = <<< EOF
<?php
\$title = 'タイトル';
\$description = 'シングルクォーテーションを含む説明\'';
\$author = '制作者';
\$url = 'https://basercms.net';

EOF;

		$contents = preg_replace("/\r\n|\r|\n/", PHP_EOL, $contents);

		$data[] = [
			[
				'title' => 'タイトル',
				'description' => "シングルクォーテーションを含む説明'",
				'author' => '制作者',
				'url' => 'https://basercms.net'
			],
			$contents
		];
		return $data;
	}
}
