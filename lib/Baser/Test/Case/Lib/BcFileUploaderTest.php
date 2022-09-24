<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model.Behavior
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcFileUploaderTest
 *
 * @package Baser.Test.Case.Model
 * @property BcFileUploader $BcFileUploader
 * @property EditorTemplate $EditorTemplate
 */
class BcFileUploaderTest extends BaserTestCase
{

	public $fixtures = [
		'baser.Default.EditorTemplate',
		'baser.Default.Page',
	];

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->EditorTemplate = ClassRegistry::init('EditorTemplate');
		$this->BcFileUploader = $this->EditorTemplate->Behaviors->BcUpload->BcFileUploader['EditorTemplate'];
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown()
	{
		session_unset();
		unset($this->EditorTemplate);
		unset($this->BcFileUploader);
		parent::tearDown();
	}


	/**
	 * ファイル等が内包されたディレクトリも削除する
	 *
	 * testGetFieldBasename()で使用します
	 *
	 * @param string $dir 対象のディレクトリのパス
	 * @return void
	 */
	public function removeDir($dir)
	{
		if ($handle = opendir("$dir")) {
			while(false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dir/$item")) {
						$this->removeDir("$dir/$item");
					} else {
						unlink("$dir/$item");
					}
				}
			}
			closedir($handle);
			rmdir($dir);
		}
	}

	/**
	 * testSaveFileの初期化を行う
	 */
	public function initTestSaveFiles($id = 1, $data = [])
	{
		$tmpFileExists = false;
		if(isset($data['tmp_name'])) {
			$tmpFileExists = true;
		}
		$fieldName = 'image';
		$tmp_name = 'tmp_file.gif';
		$savePath = $this->BcFileUploader->savePath;
		$data = array_merge(
			[
				'name' => 'basename.gif',
				'type' => 'basercms',
				'image' => ['basercms'],
				'size' => 5,
				'tmp_name' => $savePath . $tmp_name
			], $data
		);

		$imgPath = ROOT . '/lib/Baser/webroot/img/';
		$tmpSourcePath = $imgPath . 'baser.power.gif';

		// 初期化
		$this->EditorTemplate->id = $id;

		$this->EditorTemplate->data['EditorTemplate'][$fieldName] = $data;

		// ダミーファイルを生成
		if(!$tmpFileExists) {
			copy($tmpSourcePath, $data['tmp_name']);
		}
		$this->BcFileUploader->setupRequestData($this->EditorTemplate->data['EditorTemplate']);
	}


	/**
	 * testSaveFileで生成されたダミーファイルを削除する
	 */
	public function deleteDummyOnTestSaveFiles()
	{
		$tmp_name = 'tmp_file.gif';

		$savePath = $this->BcFileUploader->savePath;
		$tmpPath = $savePath . $tmp_name;
		@unlink($tmpPath);
	}

	/**
	 * リクエストされたデータを処理しやすいようにセットアップする
	 */
	public function testSetupRequestData()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 一時ファイルとして保存する
	 *
	 * @param Model $Model
	 * @param array $data
	 * @param string $tmpId
	 */
	public function testSaveTmpFiles()
	{
		$this->initTestSaveFiles();
		$data = $this->BcFileUploader->saveTmpFiles($this->EditorTemplate->data['EditorTemplate'], 1);
		$tmpId = $this->BcFileUploader->tmpId;
		$this->assertEquals('1.gif', $data['image_tmp'], 'saveTmpFiles()の返り値が正しくありません');
		$this->assertEquals(1, $tmpId, 'tmpIdが正しく設定されていません');
		$this->deleteDummyOnTestSaveFiles();
	}

	/**
	 * saveFilesのテスト
	 * ファイルを保存する
	 *
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider saveFilesCanSaveDataProvider
	 */
	public function testSaveFilesCanSave($tmpId, $message)
	{

		// TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		return;

		$this->initTestSaveFiles();

		// tmpIdを設定
		$this->BcFileUploader->tmpId = $tmpId;

		// パス情報
		$savePath = $this->BcFileUploader->savePath;
		$targetPath = $savePath . 'basename.gif';

		// 保存を実行
		$data = $this->BcFileUploader->saveFiles($this->EditorTemplate->data);

		if (!$tmpId) {
			$this->assertFileExists($targetPath, 'saveFiles()でファイルを保存できません');
			$this->assertEquals('basename.gif', $data['EditorTemplate']['image'], $message);

		} else {
			$this->assertFileNotExists($targetPath, 'saveFiles()でファイルを正しく保存できません');
			$this->assertEquals('1.gif', $data['EditorTemplate']['image']['session_key'], $message);
		}

		// 生成されたファイルを削除
		@unlink($targetPath);
		$this->deleteDummyOnTestSaveFiles();

	}

	public function saveFilesCanSaveDataProvider()
	{
		return [
			[null, 'saveFiles()でファイルを保存できません'],
			[1, 'saveFiles()でファイルを保存できません'],
		];
	}

	/**
	 * deleteFiles のテスト
	 * ファイルを削除する
	 *
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider deleteFilesDataProvider
	 */
	public function testDeleteFiles($id, $message)
	{

		// パス情報
		$savePath = $this->BcFileUploader->savePath;

		// 初期化
		$fieldName = 'image';
		$this->EditorTemplate->data['EditorTemplate'] = [
			$fieldName . '_delete' => true,
			'id' => $id
		];

		$this->initTestSaveFiles($id, $this->EditorTemplate->data['EditorTemplate']);

		$templatePath = $savePath . 'template' . $id . '.gif';
		touch($templatePath);

		// 削除を実行
		$this->BcFileUploader->deleteFiles(['id' => $id, 'image' => 'template' . $id . '.gif'], $this->EditorTemplate->data['EditorTemplate']);

		$this->assertFileNotExists($templatePath, $message);

		// 生成されたファイルを削除
		$this->deleteDummyOnTestSaveFiles();

	}

	public function deleteFilesDataProvider()
	{
		return [
			[1, 'deleteFiles()でファイルを削除できません'],
			[2, 'deleteFiles()でファイルを削除できません'],
		];
	}

	/**
	 * 削除対象かチェックしながらファイルを削除する
	 */
	public function testDeleteFileWhileChecking()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ファイル群を保存する
	 */
	public function testSaveFiles()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 保存対象かチェックしながらファイルを保存する
	 */
	public function testSaveFileWhileChecking()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


	/**
	 * saveFilesのテスト
	 * ファイルをコピーする
	 *
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider saveFilesCanCopyDataProvider
	 */
	public function testSaveFilesCanCopy($imagecopy, $message)
	{

		// TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		return;

		$this->initTestSaveFiles(1, ['name' => 'copy.gif', 'type' => 'image']);

		// パス情報
		$savePath = $this->BcFileUploader->savePath;
		$targetPath = $savePath . 'copy.gif';

		// 初期化
		$this->BcFileUploader->settings['fields']['image']['imagecopy'] = $imagecopy;

		// 保存を実行
		$this->BcFileUploader->saveFiles($this->EditorTemplate->data);
		$this->assertFileExists($targetPath, $message);

		// 生成されたファイルを削除
		@unlink($targetPath);
		$this->deleteDummyOnTestSaveFiles();

	}

	public function saveFilesCanCopyDataProvider()
	{
		return [
			[
				[['width' => 40, 'height' => 6]],
				'saveFiles()でファイルをコピーできません'
			],
			[
				[
					['width' => 40, 'height' => 6],
					['width' => 30, 'height' => 6]
				],
				'saveFiles()でファイルをコピーできません'
			],
		];
	}

	/**
	 * saveFilesのテスト
	 * ファイルをリサイズする
	 *
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider saveFilesCanResizeDataProvider
	 */
	public function testSaveFilesCanResize($imageresize, $expected, $message)
	{

		// TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		return;

		$this->initTestSaveFiles();

		// パス情報
		$savePath = $this->BcFileUploader->savePath;
		$targetPath = $savePath . 'basename.gif';

		// 初期化
		$this->BcFileUploader->settings['fields']['image']['imageresize'] = $imageresize;

		// 保存を実行
		$this->BcFileUploader->saveFiles($this->EditorTemplate->data);

		$result = $this->BcFileUploader->getImageSize($targetPath);
		$this->assertEquals($expected, $result, $message);

		// 生成されたファイルを削除
		@unlink($targetPath);
		$this->deleteDummyOnTestSaveFiles();

	}

	public function saveFilesCanResizeDataProvider()
	{
		return [
			[['width' => 20, 'height' => 10, 'thumb' => false], ['width' => 20, 'height' => 2], 'saveFiles()でファイルをリサイズできません'],
			[['width' => 20, 'height' => 10, 'thumb' => true], ['width' => 20, 'height' => 10], 'saveFiles()でファイルをリサイズできません'],
		];
	}


	/**
	 * セッションに保存されたファイルデータをファイルとして保存する
	 *
	 * @param Model $Model
	 * @param string $fieldName
	 * @return void
	 */
	public function testMoveFileSessionToTmp()
	{

		$tmpId = 1;
		$fieldName = 'image';
		$tmp_name = 'basercms_tmp';
		$basename = 'basename.gif';
		$ext = 'gif';
		$namefield = 'hoge';

		//—————————————————————————
		// セッションを設定
		//—————————————————————————

		// パス情報
		$savePath = $this->BcFileUploader->savePath;
		$tmpPath = $savePath . $tmp_name;

		// 初期化
		$field = [
			'name' => $fieldName,
			'namefield' => $namefield,
		];
		$this->BcFileUploader->tmpId = $tmpId;

		$this->EditorTemplate->data['EditorTemplate'][$fieldName] = [
			'name' => $basename,
			'tmp_name' => $tmpPath,
			'type' => 'basercms'
		];

		// ダミーファイルの作成
		$file = new File($tmpPath);
		$file->write(file_get_contents(BASER_WEBROOT . 'img/baser.power.gif'));
		$file->close();

		$this->initTestSaveFiles(1, $this->EditorTemplate->data['EditorTemplate'][$fieldName]);
		$uploadingFile = $this->BcFileUploader->getUploadingFiles()[$fieldName];

		// セッションを設定
		$this->BcFileUploader->saveTmpFile($field, $uploadingFile, $this->EditorTemplate->data['EditorTemplate']);

		//—————————————————————————
		// 本題
		//—————————————————————————

		// パス情報
		$targetName = $tmpId . '_' . $fieldName . '_' . $ext;
		$targetPath = $savePath . $targetName;

		// 初期化
		$this->EditorTemplate->data['EditorTemplate'][$fieldName . '_tmp'] = $targetName;

		// セッションからファイルを保存
		$this->BcFileUploader->moveFileSessionToTmp($this->EditorTemplate->data['EditorTemplate'], $fieldName);

		// 判定
		$this->assertFileExists($targetPath, 'セッションに保存されたファイルデータをファイルとして保存できません');

		$result = $this->BcFileUploader->getUploadingFiles()[$fieldName];
		$expected = [
			'error' => 0,
			'name' => $targetName,
			'tmp_name' => $targetPath,
			'size' => 219,
			'type' => 'basercms',
			'uploadable' => true,
			'ext' => false
		];
		$this->assertEquals($expected, $result, 'アップロードされたデータとしてデータを復元できません');

		// 生成されたファイルを削除
		@unlink($tmpPath);
		@unlink($targetPath);

	}

	/**
	 * ファイルを保存する
	 *
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider saveFileDataProvider
	 */
	public function testSaveFile($prefix, $suffix, $namefield, $message = null)
	{

		$fieldName = 'image';
		$tmp_name = 'tmp_file';
		$basename = 'basename';
		$ext = 'gif';

		// パス情報
		$savePath = $this->BcFileUploader->savePath;
		$tmpPath = $savePath . $tmp_name;
		$targetPath = $savePath . $prefix . $basename . $suffix . '.' . $ext;

		// 初期化
		$field = [
			'name' => $fieldName,
			'ext' => $ext,
			'prefix' => $prefix,
			'suffix' => $suffix,
			'namefield' => $namefield,
		];

		$this->EditorTemplate->data['EditorTemplate'][$fieldName] = [
			'name' => $basename,
			'tmp_name' => $tmpPath,
			'type' => 'basercms',
		];

		// ダミーファイルの作成
		touch($tmpPath);

		$this->initTestSaveFiles(1, $this->EditorTemplate->data['EditorTemplate']);
		$file = $this->BcFileUploader->getUploadingFiles()[$fieldName];

		// ファイル保存を実行
		$this->BcFileUploader->saveFile($field, $file);

		$this->assertFileExists($targetPath, $message);

		// 生成されたファイルを削除
		@unlink($tmpPath);
		@unlink($targetPath);

	}

	public function saveFileDataProvider()
	{
		return [
			['', '', null, 'ファイルを保存できません'],
			['pre-', '-suf', null, 'プレフィックス付きのファイルを保存できません'],
			['', '', 'hoge', 'namefieldに指定がある場合にファイルを保存できません'],
		];
	}

	/**
	 * 保存用ファイル名を取得する
	 */
	public function testGetSaveFileName()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 画像をExif情報を元に正しい確度に回転する
	 */
	public function testRotateImage()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 画像をコピーする
	 *
	 * @param array $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider copyImageDataProvider
	 */
	public function testCopyImage($prefix, $suffix, $message = null)
	{

		// TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		return;

		$imgPath = ROOT . '/lib/Baser/webroot/img/';
		$savePath = $this->BcFileUploader->savePath;
		$fileName = 'baser.power';

		$field = [
			'name' => 'image',
			'prefix' => $prefix,
			'suffix' => $suffix,
			'ext' => 'gif',
			'width' => 100,
			'height' => 100,
		];

		$this->EditorTemplate->data = [
			'EditorTemplate' => [
				'image' => [
					'name' => $fileName . '_copy' . '.' . $field['ext'],
					'tmp_name' => $imgPath . $fileName . '.' . $field['ext'],
				]
			]
		];

		// コピー先ファイルのパス
		$targetPath = $savePath . $field['prefix'] . $fileName . '_copy' . $field['suffix'] . '.' . $field['ext'];

		// コピー実行
		$this->BcFileUploader->copyImage($field);
		$this->assertFileExists($targetPath, $message);

		// コピーしたファイルを削除
		@unlink($targetPath);

	}

	public function copyImageDataProvider()
	{
		return [
			['', '', '画像ファイルをコピーできません'],
			['pre-', '-suf', '画像ファイルの名前にプレフィックスを付けてコピーできません'],
		];
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
	public function testResizeImage($width, $height, $thumb, $expected, $message = null)
	{

		// TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		return;

		$imgPath = ROOT . '/lib/Baser/webroot/img/';
		$source = $imgPath . 'baser.power.gif';
		$distination = $imgPath . 'baser.power_copy.gif';

		$savePath = $this->BcFileUploader->savePath;


		// コピー実行
		$this->BcFileUploader->resizeImage($source, $distination, $width, $height, $thumb);

		if (!$width && !$height) {
			$this->assertFileExists($distination, $message);

		} else {
			$result = $this->BcFileUploader->getImageSize($distination);
			$this->assertEquals($expected, $result, $message);

		}

		// コピーした画像を削除
		@unlink($distination);

	}

	public function resizeImageDataProvider()
	{
		return [
			[false, false, false, null, '画像ファイルをコピーできません'],
			[100, 100, false, ['width' => 98, 'height' => 13], '画像ファイルを正しくリサイズしてコピーできません'],
			[100, 100, true, ['width' => 100, 'height' => 100], '画像ファイルをサムネイルとしてコピーできません'],
		];
	}

	/**
	 * 画像のサイズを取得
	 *
	 * @param string $imgName 画像の名前
	 * @param mixed $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getImageSizeDataProvider
	 */
	public function testGetImageSize($imgName, $expected, $message = null)
	{
		$imgPath = ROOT . '/lib/Baser/webroot/img/' . $imgName;

		$result = $this->BcFileUploader->getImageSize($imgPath);
		$this->assertEquals($expected, $result, '画像のサイズを正しく取得できません');
	}

	public function getImageSizeDataProvider()
	{
		return [
			['baser.power.gif', ['width' => 98, 'height' => 13], '画像のサイズを正しく取得できません'],
		];
	}

	/**
	 * 画像ファイル群を削除する
	 *
	 * @param Model $Model
	 * @return boolean
	 */
	public function testDelFiles()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ファイルを削除する
	 *
	 * @param string $prefix 対象のファイルの接頭辞
	 * @param string $suffix 対象のファイルの接尾辞
	 * @param array $imagecopy
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider deleteFileDataProvider
	 */
	public function testDeleteFile($prefix, $suffix, $imagecopy, $message)
	{

		// TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		return;

		$savePath = $this->BcFileUploader->savePath;
		$tmpPath = TMP;
		$fileName = 'dummy';
		$field = [
			'ext' => 'gif',
			'prefix' => $prefix,
			'suffix' => $suffix,
			'imagecopy' => $imagecopy,
			'name' => $fileName,
			'width' => 600,
			'height' => 600
		];
		$targetPath = $savePath . $field['prefix'] . $fileName . $field['suffix'] . '.' . $field['ext'];

		// ダミーのファイルを生成
		touch($targetPath);

		// copyのダミーファイルを生成
		if (is_array($field['imagecopy'])) {
			copy(ROOT . '/lib/Baser/webroot/img/baser.power.gif', $tmpPath . $fileName . '.' . $field['ext']);
			$this->EditorTemplate->data['EditorTemplate'][$fileName] = [
				'name' => $fileName . '.' . $field['ext'],
				'tmp_name' => $tmpPath . $fileName . '.' . $field['ext'],
			];
			foreach($field['imagecopy'] as $copy) {
				$copy['name'] = $fileName;
				$copy['ext'] = $field['ext'];
				$this->BcFileUploader->copyImage($copy);
			}

		}

		// 削除を実行
		$this->BcFileUploader->deleteFile($fileName, $field);

		$this->assertFileNotExists($targetPath, $message);

		@unlink($targetPath);

	}

	public function deleteFileDataProvider()
	{
		return [
			[null, null, null, 'ファイルを削除できません'],
			['pre', null, null, '接頭辞を指定した場合のファイル削除ができません'],
			[null, 'suf', null, '接尾辞を指定した場合のファイル削除ができません'],
			['pre', 'suf', null, '接頭辞と接尾辞を指定した場合のファイル削除ができません'],
			[null, null, [
				'thumb' => ['suffix' => 'thumb', 'width' => '150', 'height' => '150']
			], 'ファイルを複数削除できません'],
			[null, null, [
				'thumb' => ['suffix' => 'thumb', 'width' => '150', 'height' => '150'],
				'thumb_mobile' => ['suffix' => 'thumb_mobile', 'width' => '100', 'height' => '100'],
			], 'ファイルを複数削除できません'],
		];
	}

	/**
	 * ファイル名をフィールド値ベースのファイル名に変更する
	 *
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider renameToFieldBasenameDataProvider
	 */
	public function testRenameToFieldBasename($oldName, $ext, $copy, $imagecopy, $message = null)
	{

		// 初期化
		$entity = $this->EditorTemplate->read(null, 1)['EditorTemplate'];
		$oldName = $oldName . '.' . $ext;
		$entity['image'] = $oldName;
		$this->BcFileUploader->setUploadingFiles(['image' => ['name' => $oldName, 'ext' => $ext]]);
		$setting = $this->BcFileUploader->settings['fields']['image'];

		if ($imagecopy) {
			$this->BcFileUploader->settings['fields']['image']['imagecopy'] = $imagecopy;
		}

		// パス情報
		$savePath = $this->BcFileUploader->savePath;
		$oldPath = $savePath . $oldName;

		// ダミーファイルの生成
		touch($oldPath);

		if ($imagecopy) {
			foreach($imagecopy as $copysetting) {
				$oldCopynames = $this->BcFileUploader->getFileName($copysetting, $oldName);
				touch($savePath . $oldCopynames);
			}
		}

		// テスト実行
		$entity = $this->BcFileUploader->renameToBasenameFields($entity, $copy);
		$newPath = $savePath . $entity['image'];
		$this->assertFileExists($newPath, $message);

		// 生成されたファイルを削除
		@unlink($newPath);


		// ファイルを複数生成する場合テスト
		if ($copy) {
			$this->assertFileExists($oldPath, $message);
			@unlink($oldPath);
		}

		if ($imagecopy) {
			$newName = $this->BcFileUploader->getFileName($setting['imageresize'], '1.gif');
			foreach($imagecopy as $copysetting) {
				$newCopyname = $this->BcFileUploader->getFileName($copysetting, $newName);
				$this->assertFileExists($savePath . $newCopyname, $message);
				@unlink($savePath . $newCopyname);
			}
		}

	}

	public function renameToFieldBasenameDataProvider()
	{
		return [
			['oldName', 'gif', false, false, 'ファイル名をフィールド値ベースのファイル名に変更できません'],
			['oldName', 'gif', true, false, 'ファイル名をフィールド値ベースのファイル名に変更してコピーができません'],
			['oldName', 'gif', false, [
				['prefix' => 'pre-', 'suffix' => '-suf'],
				['prefix' => 'pre2-', 'suffix' => '-suf2'],
			], '複数のファイルをフィールド値ベースのファイル名に変更できません'],
		];
	}

	/**
	 * 全フィールドのファイル名をフィールド値ベースのファイル名に変更する
	 */
	public function testRenameToBasenameFields()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ファイル名をフィールド値ベースのファイル名に変更する
	 */
	public function testRenameToBasenameField()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * フィールドベースのファイル名を取得する
	 *
	 * @param string $namefield namefieldパラメータの値
	 * @param string $basename basenameパラメータの値
	 * @param string $basename $Model->idの値
	 * @param array $setting 設定する値
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getFieldBasenameDataProvider
	 */
	public function testGetFieldBasename($namefield, $basename, $modelId, $setting, $expected, $message = null)
	{

		// 初期化
        $entity = [];
        if ($namefield) {
            $entity[$namefield] = $basename;
        }
        $entity['id'] = $modelId;

		$this->EditorTemplate->data['EditorTemplate'][$namefield] = $basename;
		$this->EditorTemplate->id = $modelId;

		$issetSubdirDataFormat = isset($setting['subdirDateFormat']);
		if ($issetSubdirDataFormat) {
			$this->BcFileUploader->settings = [];
			$this->BcFileUploader->settings['subdirDateFormat'] = $setting['subdirDateFormat'];
		}

		$setting['namefield'] = $namefield;

		// テスト実行
		$result = $this->BcFileUploader->getFieldBasename($setting, ['ext' => 'ext'], $entity);


		if (!$issetSubdirDataFormat) {
			$this->assertEquals($expected, $result, $message);

		} else {
			$savePath = $this->BcFileUploader->savePath;
			$subDir = date($setting['subdirDateFormat']) . '/';

			$expected = $subDir . $expected;

			$this->assertEquals($expected, $result, $message);

			@$this->removeDir($savePath . $subDir);
		}

	}

	public function getFieldBasenameDataProvider()
	{
		return [
			['namefield', 'basename', 'modelId', ['name' => 'name'],
				'basename_name.ext', 'フィールドベースのファイル名を正しく取得できません'],
			[null, 'basename', 'modelId', [],
				false, 'namefieldを指定しなかった場合にfalseが返ってきません'],
			['id', null, 'modelId', ['name' => 'name'],
				'modelId_name.ext', 'namefieldがidかつbasenameが指定されていない場合のファイル名を正しく取得できません'],
			['id', null, null, [],
				false, 'namefieldがidかつbasenameとModelIdが指定されていない場合にfalseが返ってきません'],
			['namefield', null, 'modelId', [],
				false, 'basenameが指定されていない場合にfalseが返ってきません'],
			['namefield', 'basename', 'modelId', ['name' => 'name', 'nameformat' => 'ho-%s-ge'],
				'ho-basename-ge_name.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
			['namefield', 'basename', 'modelId', ['name' => 'name', 'nameadd' => false],
				'basename.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
			['namefield', 'basename', 'modelId', ['name' => 'name', 'subdirDateFormat' => 'Y-m'],
				'basename_name.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
		];
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
	public function testGetFileName($prefix, $suffix, $expected, $message = null)
	{
		$setting = [
			'prefix' => $prefix,
			'suffix' => $suffix,
		];
		$fileName = 'hoge.gif';

		$result = $this->BcFileUploader->getFileName($setting, $fileName);
		$this->assertEquals($expected, $result, $message);
	}

	public function getFileNameDataProvider()
	{
		return [
			[null, null, 'hoge.gif', 'ベースファイル名からファイル名を取得できません'],
			['pre-', null, 'pre-hoge.gif', 'ベースファイル名から接頭辞付きファイル名を取得できません'],
			[null, '-suf', 'hoge-suf.gif', 'ベースファイル名から接尾辞付きファイル名を取得できません'],
			['pre-', '-suf', 'pre-hoge-suf.gif', 'ベースファイル名からプレフィックス付のファイル名を取得できません'],
		];
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
	public function testGetBasename($prefix, $suffix, $expected, $message = null)
	{
		$setting = [
			'prefix' => $prefix,
			'suffix' => $suffix,
		];
		$fileName = 'pre-hoge-suf.gif';

		$result = $this->BcFileUploader->getBasename($setting, $fileName);
		$this->assertEquals($expected, $result, $message);
	}

	public function getBasenameDataProvider()
	{
		return [
			[null, null, 'pre-hoge-suf', 'ファイル名からベースファイル名を正しく取得できません'],
			['pre-', null, 'hoge-suf', 'ファイル名からベースファイル名を正しく取得できません'],
			[null, '-suf', 'pre-hoge', 'ファイル名からベースファイル名を正しく取得できません'],
			['pre-', '-suf', 'hoge', 'ファイル名からベースファイル名を正しく取得できません'],
		];
	}

	/**
	 * 一意のファイル名を取得する
	 *
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getUniqueFileNameDataProvider
	 */
	public function testGetUniqueFileName($fieldName, $fileName, $expected, $message = null)
	{
		$setting = ['name' => $fieldName];
		$savePath = $this->BcFileUploader->savePath;
		touch($savePath . 'template1.gif');
		$file = ['name' => $fileName, 'ext' => 'gif'];
		$result = $this->BcFileUploader->getUniqueFileName($setting, $file, ['id' => null]);
		$this->assertEquals($expected, $result, $message);

		@unlink($savePath . 'template1.gif');
	}

	public function getUniqueFileNameDataProvider()
	{
		return [
			['image', 'hoge.gif', 'hoge.gif', '一意のファイル名を正しく取得できません'],
			['image', 'template.gif', 'template.gif', '一意のファイル名を正しく取得できません'],
			['image', 'template1.gif', 'template1__2.gif', '一意のファイル名を正しく取得できません'],
		];
	}

	/**
	 * 保存先のフォルダを取得する
	 */
	public function testGetSaveDir()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 既に存在するデータのファイルを削除する
	 */
	public function testDeleteExistingFiles()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 画像をコピーする
	 */
	public function testCopyImages()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
