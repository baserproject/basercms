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
 * Class BcUploadBehaviorTest
 *
 * @package Baser.Test.Case.Model
 * @property BcUploadBehavior $BcUploadBehavior
 * @property EditorTemplate $EditorTemplate
 */
class BcUploadBehaviorTest extends BaserTestCase
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
		$this->BcUploadBehavior = ClassRegistry::init('BcUploadBehavior');
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
		unset($this->BcUploadBehavior);
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

		$fieldName = 'image';
		$tmp_name = 'tmp_file.gif';
		$data = array_merge(
			[
				'name' => 'basename.gif',
				'type' => 'basercms',
				'image' => ['basercms'],
				'size' => 5,
			], $data
		);

		// パス
		$savePath = $this->BcUploadBehavior->BcFileUploader['EditorTemplate']->savePath;
		$imgPath = ROOT . '/lib/Baser/webroot/img/';
		$tmpSourcePath = $imgPath . 'baser.power.gif';
		$tmpPath = $savePath . $tmp_name;

		// 初期化
		$this->EditorTemplate->id = $id;

		$data['tmp_name'] = $tmpPath;
		$this->EditorTemplate->data['EditorTemplate'][$fieldName] = $data;

		// ダミーファイルを生成
		copy($tmpSourcePath, $tmpPath);
		$this->EditorTemplate->Behaviors->BcUpload->BcFileUploader['EditorTemplate']->setupRequestData([$fieldName => $data]);
	}


	/**
	 * testSaveFileで生成されたダミーファイルを削除する
	 */
	public function deleteDummyOnTestSaveFiles()
	{
		$tmp_name = 'tmp_file.gif';

		$savePath = $this->BcUploadBehavior->BcFileUploader['EditorTemplate']->savePath;
		$tmpPath = $savePath . $tmp_name;
		@unlink($tmpPath);
	}

	/**
	 * セットアップ
	 */
	public function testSetup()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * Before Validate
	 */
	public function testBeforeValidate()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * Before save
	 */
	public function testBeforeSave()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * After save
	 *
	 * @param Model $Model
	 * @param Model $created
	 * @param Model $options
	 * @return boolean
	 */
	public function testAfterSave()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$this->EditorTemplate->data = [
			'EditorTemplate' => [
				'name' => '',
				'link' => '',
			]
		];
		$this->EditorTemplate->save([
			'EditorTemplate' => [
				'name' => 'hoge',
				'link' => 'hoge',
			]
		]);
		$data = $this->EditorTemplate->find('all');
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
		$data = $this->EditorTemplate->saveTmpFiles($this->EditorTemplate->data, 1);
		$tmpId = $this->BcUploadBehavior->BcFileUploader['EditorTemplate']->tmpId;
		$this->assertEquals('1.gif', $data['EditorTemplate']['image_tmp'], 'saveTmpFiles()の返り値が正しくありません');
		$this->assertEquals(1, $tmpId, 'tmpIdが正しく設定されていません');
		$this->deleteDummyOnTestSaveFiles();
	}

	/**
	 * Before delete
	 * 画像ファイルの削除を行う
	 * 削除に失敗してもデータの削除は行う
	 *
	 * @param Model $Model
	 * @return void
	 */
	public function testBeforeDelete()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
