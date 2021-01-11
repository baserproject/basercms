<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */
App::uses('ThemeConfig', 'Model');

/**
 * Class ThemeConfigTest
 *
 * class NonAssosiationThemeConfig extends ThemeConfig {
 *  public $name = 'ThemeConfig';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 *
 * @package Baser.Test.Case.Model
 */
class ThemeConfigTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.Page',
		'baser.Default.ThemeConfig',
	];

	public function setUp()
	{
		parent::setUp();
		$this->ThemeConfig = ClassRegistry::init('ThemeConfig');
	}

	public function tearDown()
	{
		unset($this->ThemeConfig);
		parent::tearDown();
	}

	/**
	 * 画像を保存する
	 *
	 * MEMO : move_uploaded_file()が成功しないため、スキップ
	 *
	 * @param array $data
	 */
	public function testSaveImage()
	{

		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		// ダミーの画像を作成
		$sourcePath = WWW_ROOT . 'theme' . DS . 'nada-icons' . DS . 'img' . DS . 'logo.png';
		$dummyPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo.png';
		$dummyTmpPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo_tmp.png';
		copy($sourcePath, $dummyPath);
		copy($sourcePath, $dummyTmpPath);

		$data = ['ThemeConfig' => [
			'logo' => [
				'name' => 'logo.png',
				'tmp_name' => WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo_tmp.png',
			]
		]
		];
		$this->ThemeConfig->saveImage($data);

		// // ダミーの画像を削除
		@unlink($dummyPath);
		@unlink($dummyTmpPath);
	}

	/**
	 * 画像を削除する
	 *
	 * @param array $data
	 * @param array $expected true 画像が存在する / false 画像が存在しない(削除されている)
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider deleteImageDataProvider
	 */
	public function testDeleteImage($data, $expected, $message = null)
	{

		// ダミーの画像を作成
		$sourcePath = WWW_ROOT . 'theme' . DS . 'nada-icons' . DS . 'img' . DS . 'logo.png';
		$dummyPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo.png';
		copy($sourcePath, $dummyPath);

		$data = ['ThemeConfig' => $data];
		$this->ThemeConfig->deleteImage($data);

		// 画像の有無
		if ($expected) {
			$this->assertFileExists($dummyPath, $message);
		} else {
			$this->assertFileNotExists($dummyPath, $message);
		}

		// ダミーの画像を削除
		@unlink($dummyPath);

	}

	public function deleteImageDataProvider()
	{
		return [
			[['logo_delete' => true], false, '画像を削除できません'],
			[['logo' => true], true, '画像が削除されています'],
			[[], true, '画像が削除されています'],
		];
	}

	/**
	 * テーマカラー設定を保存する
	 *
	 * @param array $data 設定するテーマカラーのデータ
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider updateColorConfigDataProvider
	 */
	public function testUpdateColorConfig($data, $expected, $message = null)
	{
		$theme = Configure::read('BcSite.theme');
		Configure::write('BcSite.theme', 'nada-icons');

		// 設定元のファイル(config.css)を取得($dataが設定されてない場合、元のファイルが削除されるので、再生成するため)
		$configCssPathOriginal = getViewPath() . 'css' . DS . 'config.css';
		$FileOriginal = new File($configCssPathOriginal);
		$configOriginal = $FileOriginal->read();

		// 設定ファイルの取得
		$configCssPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css';
		$File = new File($configCssPath);

		// テーマーカラーの設定を実行
		$data = ['ThemeConfig' => $data];
		$this->ThemeConfig->updateColorConfig($data);

		// 元のファイルを再生成
		$FileOriginal->write($configOriginal);
		$FileOriginal->close();

		// 生成したconfig.cssをの内容を取得
		$setting = $File->read();
		$File->close();
		unlink($configCssPath);

		$this->assertRegExp('/' . $expected . '/s', $setting, $message);
		Configure::write('BcSite.theme', $theme);
	}

	public function updateColorConfigDataProvider()
	{
		return [
			[['color_main' => '000000'], '#000000', 'テーマカラーを設定できません'],
			[['color_main' => '000000', 'color_link' => '111111'], '#000000.*#111111', 'テーマカラーを複数設定できません'],
			[['hoge' => '000000'], "\{(\n|\r\n)}", '$dataが適切でないのにcssの要素が空ではありません'],
			[[], "\{(\n|\r\n)\}", '$dataがないのにcssの要素が空ではありません'],
		];
	}
}
