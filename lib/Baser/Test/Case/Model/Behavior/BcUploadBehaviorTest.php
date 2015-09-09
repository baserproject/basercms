<?php
/**
 * ファイルアップロードビヘイビアのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

/**
 * PageTest class
 * 
 * @package Baser.Test.Case.Model
 */
class BcUploadBehaviorTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.BlogContent',
		'baser.Default.BlogCategory',
		'baser.Default.BlogPost',
		'baser.Default.BlogPostsBlogTag',
		'baser.Default.BlogTag',
		'baser.Default.Content',
		'baser.Default.SiteConfig',
		'baser.Default.Page',
		'baser.Default.PageCategory',
		'baser.Default.Permission',
		'baser.Default.Plugin',
		'baser.Default.PluginContent',
		'baser.Default.User',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->EditorTemplate = ClassRegistry::init('EditorTemplate');
		$this->BcUploadBehavior = ClassRegistry::init('BcUploadBehavior');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EditorTemplate);
		unset($this->BcUploadBehavior);
		parent::tearDown();
	}


/**
 * セットアップ
 * 
 * @param Model	$Model
 * @param array	actsAsの設定
 * @return void
 * @access public
 */
	public function testSetup() {

	}



/**
 * Before save
 * 
 * @param Model $Model
 * @param Model $options
 * @return boolean
 * @access public
 */
	public function beforeSave() {

	}

/**
 * After save
 * 
 * @param Model $Model
 * @param Model $created
 * @param Model $options
 * @return boolean
 * @access public
 */
	public function afterSave() {

	}

/**
 * 一時ファイルとして保存する
 * 
 * @param Model $Model
 * @param array $data
 * @param string $tmpId
 * @return boolean
 * @access public
 */
	public function saveTmpFiles() {

	}

/**
 * ファイル群を保存する
 * 
 * @param Model $Model
 * @return boolean
 * @access public
 */
	public function saveFiles() {

	}

/**
 * セッションに保存されたファイルデータをファイルとして保存する
 * 
 * @param Model $Model
 * @param string $fieldName
 * @return void
 * @access public
 */
	public function moveFileSessionToTmp() {

	}

/**
 * ファイルを保存する
 * 
 * @param Model $Model
 * @param array 画像保存対象フィールドの設定
 * @return ファイル名 Or false
 * @access public
 */
	public function saveFile() {

	}

/**
 * 画像をコピーする
 * 
 * @param Model $Model
 * @param array 画像保存対象フィールドの設定
 * @return boolean
 * @access public
 */
	public function copyImage() {

	}

/**
 * 画像ファイルをコピーする
 * リサイズ可能
 * 
 * @param string コピー元のパス
 * @param string コピー先のパス
 * @param int 横幅
 * @param int 高さ
 * @return boolean
 * @access public
 */
	public function testResizeImage() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$imgPath = WWW_ROOT . 'img/admin' . DS;
		$source = $imgPath . 'bg_install.png';
		$distination = $imgPath . 'bg_install_copy.png';

		// 通常コピー
		$this->BcUploadBehavior->resizeImage($source, $distination);
		$this->assertFileExists($distination, '画像ファイルをコピーできません');
		@unlink($distination);

		// リサイズコピー
		$this->BcUploadBehavior->resizeImage($source, $distination, 100, 100);
		$result = $this->BcUploadBehavior->getImageSize($distination);
		
	}

/**
 * 画像のサイズを取得
 *
 * @param string $imgName 画像の名前
 * @param mixed $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getImageSizeDataProvider
 */
	public function testGetImageSize($imgName, $expected, $message = null) {
		$imgPath = WWW_ROOT . 'img/admin' . DS . $imgName;
		$result = $this->BcUploadBehavior->getImageSize($imgPath);
		$this->assertEquals($expected, $result, '画像のサイズを正しく取得できません');
	}

	public function getImageSizeDataProvider() {
		return array(
			array('bg_install.png', array('width' => 240, 'height' => 205), '画像のサイズを正しく取得できません'),
		);
	}

/**
 * After delete
 * 画像ファイルの削除を行う
 * 削除に失敗してもデータの削除は行う
 * 
 * @param Model $Model
 * @return void
 * @access public
 */
	public function beforeDelete() {

	}

/**
 * 画像ファイル群を削除する
 * 
 * @param Model $Model
 * @return boolean
 * @access public
 */
	public function delFiles() {

	}

/**
 * ファイルを削除する
 * 
 * @param Model $Model
 * @param array 保存対象フィールドの設定
 * @return boolean
 * @access public
 */
	public function delFile() {

	}

/**
 * ファイル名をフィールド値ベースのファイル名に変更する
 * 
 * @param Model $Model
 * @return boolean
 * @access public
 */
	public function renameToFieldBasename() {

	}

/**
 * フィールドベースのファイル名を取得する
 *
 * @param Model $Model
 * @param array $setting
 * @param string $ext
 * @return mixed false / string
 * @access public
 */
	public function getFieldBasename() {

	}

/**
 * ベースファイル名からプレフィックス付のファイル名を取得する
 * 
 * @param Model $Model
 * @param array $setting
 * @param string $filename
 * @return string
 * @access public
 */
	public function getFileName() {

	}

/**
 * ファイル名からベースファイル名を取得する
 * 
 * @param Model $Model
 * @param array $setting
 * @param string $filename
 * @return string
 * @access public
 */
	public function getBasename() {

	}

/**
 * 一意のファイル名を取得する
 * 
 * @param string $fieldName
 * @param string $fileName
 * @return string
 * @access public
 */
	public function getUniqueFileName() {

	}


}
