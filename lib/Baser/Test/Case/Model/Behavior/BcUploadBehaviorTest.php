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
		'baser.Default.EditorTemplate',
		'baser.Default.Page',
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
 */
	public function testSetupSetting() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
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
	public function testSaveTmpFiles() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$data = 'hoge';
		$tmpId = 1;
		$result = $this->EditorTemplate->saveTmpFiles($data, $tmpId);
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
 * @param array 画像保存対象フィールドの設定
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider copyImageDataProvider
 */
	public function testCopyImage($prefix, $suffix, $message = null) {

		$imgPath = WWW_ROOT . 'img/admin' . DS;
		$savePath = WWW_ROOT . 'files/editor/';
		$fileName = 'bg_install';

		$field = array(
			'name' => 'image',
			'prefix' => $prefix,
			'suffix' => $suffix,
			'ext' => 'png',
			'width' => 100,
			'height' => 100,
		);
		
		$this->EditorTemplate->data = array(
			'EditorTemplate' => array(
				'image' => array(
					'name' => $fileName . '_copy' . '.' . $field['ext'],
					'tmp_name' => $imgPath . $fileName . '.' . $field['ext'],
				)
			)
		);

		// コピー先ファイルのパス
		$targetPath = $savePath . $field['prefix'] . $fileName . '_copy' . $field['suffix'] . '.' . $field['ext'];

		// コピー実行
		$this->EditorTemplate->copyImage($field);
		$this->assertFileExists($targetPath, $message);

		// コピーしたファイルを削除
		@unlink($targetPath);
	}

	public function copyImageDataProvider() {
		return array(
			array('', '', '画像ファイルをコピーできません'),
			array('pre-', '-suf', '画像ファイルの名前にプレフィックスを付けてコピーできません'),
		);
	}

/**
 * 画像ファイルをコピーする
 * リサイズ可能
 * 
 * @param int $width 横幅
 * @param int $height 高さ
 * @param boolean $$thumb サムネイルとしてコピーするか
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider resizeImageDataProvider
 */
	public function testResizeImage($width, $height, $thumb, $expected, $message = null) {

		$imgPath = WWW_ROOT . 'img/admin' . DS;
		$source = $imgPath . 'bg_install.png';
		$distination = $imgPath . 'bg_install_copy.png';

		// コピー実行
		$this->BcUploadBehavior->resizeImage($source, $distination, $width, $height, $thumb);
			
		if (!$width && !$height) {
			$this->assertFileExists($distination, $message);
		
		} else {
			$result = $this->BcUploadBehavior->getImageSize($distination);
			$this->assertEquals($expected, $result, $message);
		
		}
		
		// コピーした画像を削除
		@unlink($distination);
		
	}

	public function resizeImageDataProvider() {
		return array(
			array(false, false, false, null, '画像ファイルをコピーできません'),
			array(100, 100, false, array('width' => 100, 'height' => 85), '画像ファイルを正しくリサイズしてコピーできません'),
			array(100, 100, true, array('width' => 100, 'height' => 100), '画像ファイルをサムネイルとしてコピーできません'),
		);
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
	public function testDelFiles() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ファイルを削除する
 * 
 * @param string $prefix 対象のファイルの接頭辞
 * @param string $suffix 対象のファイルの接尾辞
 * @param array $imagecopy 
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider delFileDataProvider
 */
	public function testDelFile($prefix, $suffix, $imagecopy, $message) {
		$savePath = '/vagrant/app/webroot/files/editor/';
		$field = array(
			'ext' => 'gif',
			'prefix' => $prefix,
			'suffix' => $suffix,
			'imagecopy' => $imagecopy,
		);
		$fileName = 'dummy';
		$targetPath = $savePath . $field['prefix'] . 'dummy'. $field['suffix'] . '.' . $field['ext'];

		// ダミーのファイルを生成
		touch($targetPath);

		// copyのダミーファイルを生成
		// if (is_array($field['imagecopy'])) {
		// 	$field['name'] = $field['imagecopy'][0]['name'];
		// 	$field['ext'] = $field['imagecopy'][0]['ext'];

		// 	foreach ($field['imagecopy'] as $copy) {
		// 		touch($savePath . $copy['name'] . '.' .  $copy['ext']);
		// 	}
		// }

		// 削除を実行
		$this->EditorTemplate->delFile($fileName, $field);

		$this->assertFileNotExists($targetPath, $message);

		@unlink($targetPath);

	}

	public function delFileDataProvider() {
		return array(
			array(null, null, null, 'ファイルを削除できません'),
			array('pre', null, null, '接頭辞を指定した場合のファイル削除ができません'),
			array(null, 'suf', null, '接尾辞を指定した場合のファイル削除ができません'),
			array('pre', 'suf', null, '接頭辞と接尾辞を指定した場合のファイル削除ができません'),
			// array(null, null, array(
			// 			array('name' => 'dummy1', 'ext' => 'gif'),
			// 			), 'ファイルを複数削除できません'),
			// array(null, null, array(
			// 			array('name' => 'dummy1', 'ext' => 'gif'),
			// 			array('name' => 'dummy2', 'ext' => 'gif'),
			// 			), 'ファイルを複数削除できません'),
		);
	}

/**
 * ファイル名をフィールド値ベースのファイル名に変更する
 * 
 * @param Model $Model
 * @return boolean
 * @access public
 */
	public function testRenameToFieldBasename() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * フィールドベースのファイル名を取得する
 *
 * @param string $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getFieldBasenameDataProvider
 */
	public function testGetFieldBasename($namefield, $basename, $modelId, $setting, $expected, $message = null) {

		// 初期化
		$this->EditorTemplate->data['EditorTemplate'][$namefield] = $basename;
		$this->EditorTemplate->id = $modelId;

		$issetSubdirDataFormat = isset($setting['subdirDateFormat']);
		if ($issetSubdirDataFormat) {
			$this->EditorTemplate->settings = array();
			$this->EditorTemplate->settings['EditorTemplate']['subdirDateFormat'] = $setting['subdirDateFormat'];
		}

		$setting['namefield'] = $namefield;


		// テスト実行
		$result = $this->EditorTemplate->getFieldBasename($setting, 'ext');
		$this->assertEquals($expected, $result, $message);

		if ($issetSubdirDataFormat) {

		}
	}

	public function getFieldBasenameDataProvider() {
		return array(
			array('namefield', 'basename', 'modelId', array('name' => 'name'),
						'basename_name.ext', 'フィールドベースのファイル名を正しく取得できません'),
			array(null, 'basename', 'modelId', array(),
						false, 'namefieldを指定しなかった場合にfalseが返ってきません'),	
			array('id', null, 'modelId', array('name' => 'name'),
						'modelId_name.ext', 'namefieldがidかつbasenameが指定されていない場合のファイル名を正しく取得できません'),
			array('id', null, null, array(),
						false, 'namefieldがidかつbasenameとModelIdが指定されていない場合にfalseが返ってきません'),
			array('namefield', null, 'modelId', array(),
						false, 'basenameが指定されていない場合にfalseが返ってきません'),
			array('namefield', 'basename', 'modelId', array('name' => 'name', 'nameformat' => 'ho-%s-ge'),
						'ho-basename-ge_name.ext', 'formatを指定した場合に正しくファイル名を取得できません'),
			array('namefield', 'basename', 'modelId', array('name' => 'name', 'nameadd' => false),
						'basename.ext', 'formatを指定した場合に正しくファイル名を取得できません'),
			// array('namefield', 'basename', 'modelId', array('name' => 'name', 'subdirDateFormat' => 'test'),
			// 			'basename_name.ext', 'formatを指定した場合に正しくファイル名を取得できません'),
		);
	}


/**
 * ベースファイル名からプレフィックス付のファイル名を取得する
 * 
 * @param string $prefix 対象のファイルの接頭辞
 * @param string $suffix 対象のファイルの接尾辞
 * @param string $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getFileNameDataProvider
 */
	public function testGetFileName($prefix, $suffix, $expected, $message = null) {
		$setting = array(
			'prefix' => $prefix,
			'suffix' => $suffix,
		);
		$fileName = 'hoge.gif';

		$result = $this->EditorTemplate->getFileName($setting, $fileName);
		$this->assertEquals($expected, $result, $message);
	}

	public function getFileNameDataProvider() {
		return array(
			array(null, null, 'hoge.gif', 'ベースファイル名からファイル名を取得できません'),
			array('pre-', null, 'pre-hoge.gif', 'ベースファイル名から接頭辞付きファイル名を取得できません'),
			array(null, '-suf', 'hoge-suf.gif', 'ベースファイル名から接尾辞付きファイル名を取得できません'),
			array('pre-', '-suf', 'pre-hoge-suf.gif', 'ベースファイル名からプレフィックス付のファイル名を取得できません'),
		);
	}

/**
 * ファイル名からベースファイル名を取得する
 * 
 * @param string $prefix 対象のファイルの接頭辞
 * @param string $suffix 対象のファイルの接尾辞
 * @param string $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getBasenameDataProvider
 */
	public function testGetBasename($prefix, $suffix, $expected, $message = null) {
		$setting = array(
			'prefix' => $prefix,
			'suffix' => $suffix,
		);
		$fileName = 'pre-hoge-suf.gif';

		$result = $this->EditorTemplate->getBasename($setting, $fileName);
		$this->assertEquals($expected, $result, $message);
	}

	public function getBasenameDataProvider() {
		return array(
			array(null, null, 'pre-hoge-suf', 'ファイル名からベースファイル名を正しく取得できません'),
			array('pre-', null, 'hoge-suf', 'ファイル名からベースファイル名を正しく取得できません'),
			array(null, '-suf', 'pre-hoge', 'ファイル名からベースファイル名を正しく取得できません'),
			array('pre-', '-suf', 'hoge', 'ファイル名からベースファイル名を正しく取得できません'),
		);
	}

/**
 * 一意のファイル名を取得する
 * 
 * @param string $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getUniqueFileNameDataProvider
 */
	public function testGetUniqueFileName($fieldName, $fileName, $expected, $message = null) {
		$setting = array('ext' => 'gif');

		$result = $this->EditorTemplate->getUniqueFileName($fieldName, $fileName, $setting);
		$this->assertEquals($expected, $result, $message);
	}

	public function getUniqueFileNameDataProvider() {
		return array(
			array('image', 'hoge.gif', 'hoge.gif', '一意のファイル名を正しく取得できません'),
			array('image', 'template.gif', 'template.gif', '一意のファイル名を正しく取得できません'),
			array('image', 'template1.gif', 'template1__2.gif', '一意のファイル名を正しく取得できません'),
		);
	}

}
