<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Controller
 * @since			baserCMS v 4.0.9
 * @license			http://basercms.net/license/index.html
 */

App::uses('ThemeFilesController', 'Controller');

/**
 * ThemeFilesControllerTest class
 *
 * @package Baser.Test.Case.Controller
 * @property  ThemeFilesController $ThemeFilesController
 */
class ThemeFilesControllerTest extends BaserTestCase {

/**
 * set up
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * テーマファイル一覧
 */
	public function testAdmin_index() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * テーマファイル作成
 */
	public function testAdmin_add() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * テーマファイル編集
 */
	public function testAdmin_edit() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ファイルを削除する
 */
	public function testAdmin_del() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ファイルを削除する　（ajax）
 */
	public function testAdmin_ajax_del() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * テーマファイル表示
 */
	public function testAdmin_view() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * テーマファイルをコピーする
 */
	public function testAdmin_ajax_copy() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ファイルをアップロードする
 */
	public function testAdmin_upload() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * フォルダ追加
 */
	public function testAdmin_add_folder() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * フォルダ編集
 */
	public function testAdmin_edit_folder() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * フォルダ表示
 */
	public function testAdmin_view_folder() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * コアファイルを現在のテーマにコピーする
 */
	public function testAdmin_copy_to_theme() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * コアファイルのフォルダを現在のテーマにコピーする
 */
	public function testAdmin_copy_folder_to_theme() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 画像を表示する
 * コアの画像等も表示可
 */
	public function testAdmin_img() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 画像を表示する
 * コアの画像等も表示可
 */
	public function testAdmin_img_thumb() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * @test [functional] ファイルタイプに対応する拡張子のリストを入手するテスト
 * @dataProvider provider_getFileTypePattern
 * @param string $type タイプ名
 * @param array $setting Configureで設定する値。nullの場合は設定しない(デフォルトを使うため)
 * @param string $expect 結果で得られる正規表現パターン
 */
	public function test_getFileTypePattern($type, $setting, $expect) {
		//コントローラを作成
		$Controller = new ThemeFilesController(new CakeRequest(), new CakeResponse());

		//$settingがnullでなければ設定を登録
		if ($setting !== null) {
			Configure::write("ThemeFile.fileType.{$type}", $setting);
		}
		$actual = $Controller->_getFileTypePattern($type);
		$this->assertEquals($expect, $actual);
	}

/**
 * @param array
 */
	public function provider_getFileTypePattern() {
		return [
			'デフォルトのtextの拡張子リストが入手できること' => [
				'text',
				null,
				'/^(.+?)(\.ctp|\.php|\.css|\.js)$/is',
			],
			'デフォルトのimageの拡張子リストが入手できること' => [
				'image',
				null,
				'/^(.+?)(\.png|\.gif|\.jpg|\.jpeg)$/is',
			],
			'カスタマイズしたtextの拡張子リストが入手できること' => [
				'text',
				['php'],
				'/^(.+?)(\.php)$/is',
			],
			'デフォルトのimageの拡張子リストが入手できること' => [
				'image',
				['jpg'],
				'/^(.+?)(\.jpg)$/is',
			],
			'存在しないタイプの場合はデフォルトではマッチしないパターンが入手できること' => [
				'css',
				null,
				'/^$/',
			],
		];
	}

/**
 * @test [functional] ファイルタイプに対応する拡張子のリストを入手するテスト
 * @dataProvider provider_getFileTypeExtensions
 * @param string $type タイプ名
 * @param array $setting Configureで設定する値。nullの場合は設定しない(デフォルトを使うため)
 * @param string $expect 結果で得られる拡張子の列挙
 */
	public function test_getFileTypeExtensions($type, $setting, $expect) {
		//コントローラを作成
		$Controller = new ThemeFilesController(new CakeRequest(), new CakeResponse());

		//$settingがnullでなければ設定を登録
		if ($setting !== null) {
			Configure::write("ThemeFile.fileType.{$type}", $setting);
		}
		$actual = $Controller->_getFileTypeExtensions($type);
		$this->assertEquals($expect, $actual);
	}

/**
 * @param array
 */
	public function provider_getFileTypeExtensions() {
		return [
			'デフォルトのtextの拡張子リストが入手できること' => [
				'text',
				null,
				['ctp', 'php', 'css', 'js'],
			],
			'デフォルトのimageの拡張子リストが入手できること' => [
				'image',
				null,
				['png', 'gif', 'jpg', 'jpeg'],
			],
			'カスタマイズしたtextの拡張子リストが入手できること' => [
				'text',
				['php'],
				['php'],
			],
			'カスタマイズしたimageの拡張子リストが入手できること' => [
				'image',
				['jpg'],
				['jpg'],
			],
			'存在しないタイプの場合はデフォルトではemptyが入手できること' => [
				'css',
				null,
				[],
			],
		];
	}

/**
 * @test [functional] 除外ファイルのリストを入手するテスト
 * @dataProvider provider_getExcludeFileList
 * @param array $setting Configureで設定する値。nullの場合は設定しない(デフォルトを使うため)
 * @param string $expect 結果で得られる除外リスト
 */
	public function test_getExcludeFileList($setting, $expect) {
		//コントローラを作成
		$Controller = new ThemeFilesController(new CakeRequest(), new CakeResponse());

		//$settingがnullでなければ設定を登録
		if ($setting !== null) {
			Configure::write('ThemeFile.excludeEtcFileList', $setting);
		}
		$actual = $Controller->_getExcludeFileList();
		$this->assertEquals($expect, $actual);
	}

/**
 * @param array
 */
	public function provider_getExcludeFileList() {
		return [
			'デフォルトの除外リストが入手できること' => [
				null,
				[
					'screenshot.png',
					'VERSION.txt',
					'config.php',
					'AppView.php',
					'BcAppView.php'
				],
			],
			'カスタマイズした除外リストが入手できること' => [
				[
					'screenshot.png',
					'VERSION.txt',
				],
				[
					'screenshot.png',
					'VERSION.txt',
				],
			],
		];
	}

/**
 * @test [functional] 除外フォルダのリストを入手するテスト
 * @dataProvider provider_getExcludeFolderList
 * @param array $setting Configureで設定する値。nullの場合は設定しない(デフォルトを使うため)
 * @param string $expect 結果で得られる除外リスト
 */
	public function test_getExcludeFolderList($setting, $expect) {
		//コントローラを作成
		$Controller = new ThemeFilesController(new CakeRequest(), new CakeResponse());

		//$settingがnullでなければ設定を登録
		if ($setting !== null) {
			Configure::write('ThemeFile.excludeEtcFolderList', $setting);
		}
		$actual = $Controller->_getExcludeFolderList();
		$this->assertEquals($expect, $actual);
	}

/**
 * @param array
 */
	public function provider_getExcludeFolderList() {
		return [
			'デフォルトの除外リストが入手できること' => [
				null,
				[
					'Layouts',
					'Elements',
					'Emails',
					'Pages',
					'Helper',
					'Config',
					'Plugin',
					'img',
					'css',
					'js',
					'_notes',
				],
			],
			'カスタマイズした除外リストが入手できること' => [
				[
					'_notes',
				],
				[
					'_notes',
				],
			],
		];
	}

/**
 * @test [functional] テンプレートタイプの入手するテスト
 * @dataProvider provider_getTemplateTypes
 * @param array $setting Configureで設定する値。nullの場合は設定しない(デフォルトを使うため)
 * @param string $expect 結果で得られるテンプレートタイプのリスト
 */
	public function test_getTemplateTypes($setting, $expect) {
		//コントローラを作成
		$Controller = new ThemeFilesController(new CakeRequest(), new CakeResponse());

		//$settingがnullでなければ設定を登録
		if ($setting !== null) {
			Configure::write('ThemeFile.templateTypes', $setting);
		}
		$actual = $Controller->_getTemplateTypes();
		$this->assertEquals($expect, $actual);
	}

/**
 * @param array
 */
	public function provider_getTemplateTypes() {
		return [
			'デフォルトのテンプレートタイプが入手できること' => [
				null,
				[
					'Layouts'	=> __d('baser', 'レイアウトテンプレート'),
					'Elements'	=> __d('baser', 'エレメントテンプレート'),
					'Emails'	=> __d('baser', 'Eメールテンプレート'),
					'etc'		=> __d('baser', 'コンテンツテンプレート'),
					'css'		=> __d('baser', 'スタイルシート'),
					'js'		=> 'Javascript',
					'img'		=> __d('baser', 'イメージ')
				],
			],
			'カスタマイズしたテンプレートタイプが入手できること' => [
				[
					'etc' => 'テーマファイル',
				],
				[
					'etc' => 'テーマファイル',
				],
			],
		];
	}

/**
 * @test [functional] テキストタイプで選択可能な拡張子のプルダウンリストを入手するテスト
 * @dataProvider provider_getCreateTextExtensions
 * @param string $type タイプ名
 * @param array $setting Configureで設定する値。nullの場合は設定しない(デフォルトを使うため)
 * @param string $expect 結果で得られる拡張子リスト
 */
	public function test_getCreateTextExtensions($setting, $expect) {
		//コントローラを作成
		$Controller = new ThemeFilesController(new CakeRequest(), new CakeResponse());

		//$settingがnullでなければ設定を登録
		if ($setting !== null) {
			Configure::write("ThemeFile.fileType.text", $setting);
		}
		$actual = $Controller->_getCreateTextExtensions();
		$this->assertEquals($expect, $actual);
	}

/**
 * @param array
 */
	public function provider_getCreateTextExtensions() {
		return [
			'デフォルトのtextのプルダウンリストが入手できること' => [
				null,
				[
					'ctp' => '.ctp',
					'php' => '.php',
					'css' => '.css',
					'js' => '.js'
				],
			],
			'カスタマイズしたtextのプルダウンリストが入手できること' => [
				['php'],
				['php' => '.php'],
			],
		];
	}

}
