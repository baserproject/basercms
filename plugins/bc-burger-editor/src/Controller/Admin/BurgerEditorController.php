<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Utility\BcUtil;
use BaserCore\Vendor\Imageresizer;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use BcBurgerEditor\Service\BurgerEditorService;
use BcBurgerEditor\View\Helper\BurgerEditorHelper;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * BurgerEditorController
 */
class BurgerEditorController extends BcAdminAppController
{

	public $isUse = false;

	protected $imgExts = ['gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png'];    // 許可画像拡張子

	protected $imageDataMaxsize = 10485760; // アップロード可能な画像の最大サイズ10MB(1024 * 1024 * 10)
	protected $fileDataMaxSize = 10485760; // アップロード可能なファイルの最大サイズ10MB

	public $imgSizeWidthMax = 1920;    // BurgerEditorが許可する最大サイズ
	public $imgSizeWidthDefault = 1000;    //
	public $imgSizeWidthSmall = 640;    //

	protected $fileListPerPage  = 10;	// ファイルリストの1ページあたりの表示件数
	protected $imageListPerPage = 10;	// 画像リストの1ページあたりの表示件数

	/**
	 * Constructor
	 *
	 * @param $request
	 * @param $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		// 画像サイズ設定
		$uploadImageSize = Configure::read('Bge.uploadImageSize');
		if ($uploadImageSize) {
			$this->imgSizeWidthMax = $uploadImageSize['imgSizeWidthMax'];
			$this->imgSizeWidthDefault = $uploadImageSize['imgSizeWidthDefault'];
			$this->imgSizeWidthSmall = $uploadImageSize['imgSizeWidthSmall'];
		}

		//　データサイズ
		if (Configure::read('Bge.uploadImageDataSize')) {
			$this->imageDataMaxsize = Configure::read('Bge.uploadImageDataSize');
		}
		if (Configure::read('Bge.uploadFileDataSize')) {
			$this->fileDataMaxSize = Configure::read('Bge.uploadFileDataSize');
		}

		// 1ページあたりの表示件数（ファイルリスト・画像リスト）
		if (Configure::read('Bge.fileListPerPage')) {
			$configFileListPerPage = Configure::read('Bge.fileListPerPage');
			if(is_numeric($configFileListPerPage) && intval($configFileListPerPage) > 0){
				$this->fileListPerPage  = intval($configFileListPerPage);
			}
		}
		if (Configure::read('Bge.imageListPerPage')) {
			$configImageListPerPage = Configure::read('Bge.imageListPerPage');
			if(is_numeric($configImageListPerPage) && intval($configImageListPerPage) > 0){
				$this->imageListPerPage  = intval($configImageListPerPage);
			}
		}

		$this->BurgerEditorService = new BurgerEditorService();
	}

	/**
	 * before filter
	 *
	 * @param EventInterface $event
	 * @return \Cake\Http\Response|void|null
	 */
	public function beforeFilter(EventInterface $event)
	{
		$this->FormProtection->setConfig('validate', false);
		if ($this->getRequest()->getParam('action') === 'panel') {
			$this->setRequest($this->getRequest()->withAttribute('requested', true));
		}
		$result = parent::beforeFilter($event);
		BurgerEditorHelper::setSelfValue();
		$this->set("addonDir", BurgerEditorUtil::getAddonPath());
		return $result;
	}

	/**
	 * アップロード画像一覧取得
	 *
	 * @return void
	 */
	public function img_list()
	{
		$searchWord = empty($_GET["word"]) ? null : $_GET['word']; // 検索ワード
		$targetPage = empty($_GET["page"]) ? null : $_GET['page']; // ページ番号
		$selectedFilePath = empty($_GET["selected"]) ? null : $_GET['selected']; // 選択済みファイルパス
		$pageList = $this->getFormatedImageList($searchWord, $targetPage, $selectedFilePath, $this->imageListPerPage);
		$result = ['error' => false, 'data' => $pageList['data'], 'pagination' => $pageList['pagination']];
		Configure::write('debug', 0);
		$this->setResponse($this->getResponse()
			->withType('application/json')
			->withCharset('UTF-8')
		);
		echo json_encode($result);
		exit();
	}

	/**
	 * 画像ファイルアップロード
	 *
	 * @return void
	 */
	public function img_upload()
	{
		BurgerEditorHelper::getImageList(); // 一覧情報取得・更新
		$savePath = BurgerEditorHelper::$imageFileBaseDir;

		$hasError = false;
		if (!$_FILES) $hasError = 'ファイルがアップロードされていません';
		if (!is_writeable($savePath)) $hasError = 'アップロードフォルダに書き込めません';
		foreach($_FILES as $name => $fileData) {
			if (!$name) {
				$hasError = 'アップロードに失敗しました';
			} else {
				if ($fileData["error"] == UPLOAD_ERR_INI_SIZE) {
					$hasError = 'ファイル容量が大きすぎます';
				}
				$fileExt = BurgerEditorUtil::getExtension($fileData["name"]);
				if (!in_array(strtolower($fileExt), $this->imgExts)) {
					$hasError = "画像形式のファイルをアップロードしてください";
				}
				if ($fileData["error"] == UPLOAD_ERR_PARTIAL) {
					$hasError = "ファイルが正しくアップロードされませんでした";
				}
			}

			// 画像データサイズ制限
			if (!$hasError && $fileData['size'] > $this->imageDataMaxsize) {
				$viewSize = (($this->imageDataMaxsize / 1024) / 1024) . 'MB';
				$hasError = "データサイズは{$viewSize}以下のファイルをアップロードしてください";
			}

			if ($hasError) break;
		}

		// 何かしらエラー
		if ($hasError) {
			$result = ['error' => $hasError, 'data' => $hasError];
		} else {

			// 保存
			$uploaddir = BurgerEditorHelper::$imageFileBaseDir;

			$saveFiles = [];
			foreach($_FILES as $name => $fileData) {
				BurgerEditorHelper::$imageFileMaxId++;
				$basename = $fileData["name"];
				$filename = (BurgerEditorHelper::$imageFileMaxId) . "__" . BurgerEditorUtil::b64e(BurgerEditorUtil::getFileNameNoExtension($basename));
				// 拡張子
				$baseExt = BurgerEditorUtil::getExtension($basename);
				if ($baseExt) {
					$filename .= "." . $baseExt;
				}
				move_uploaded_file($fileData["tmp_name"], $uploaddir . $filename);
				//回転
				$this->BurgerEditorService->rotateImage($uploaddir . $filename);

				// 基本ファイル名 - 拡張子なし
				$baseFile = BurgerEditorUtil::getFileNameNoExtension($filename);

				// リサイズ除外拡張子判定
				if (in_array(strtolower($baseExt), Configure::read('Bge.noResizeExtension'))) {
					// 元サイズ - リサイズせずコピー
					$thumbFilename = $baseFile . '__org.' . $baseExt;
					copy($uploaddir . $filename, $uploaddir . $thumbFilename);
					$saveFiles[] = $thumbFilename;

					// サムネイル作成 - リサイズせずコピー
					$thumbFilename = $baseFile . '__small.' . $baseExt;
					copy($uploaddir . $filename, $uploaddir . $thumbFilename);
					$saveFiles[] = $thumbFilename;

				} else {
					// リサイズクラス生成
					$imageResizer = new Imageresizer();

					// 圧縮レベルの取得
					$quarity = Configure::read('Bge.uploadImageQuality');

					// 元サイズ - $this->imgSizeWidthMaxを超える場合は$this->imgSizeWidthMaxにリサイズ
					$thumbFilename = $baseFile . '__org.' . $baseExt;
					$imageResizer->resize($uploaddir . $filename, $uploaddir . $thumbFilename, $this->imgSizeWidthMax, null, false, $quarity);
					$saveFiles[] = $thumbFilename;

					// サムネイル作成
					$thumbFilename = $baseFile . '__small.' . $baseExt;
					$imageResizer->resize($uploaddir . $filename, $uploaddir . $thumbFilename, $this->imgSizeWidthSmall, null, false, $quarity);
					$saveFiles[] = $thumbFilename;

					// 標準サイズ
					$thumbFilename = $baseFile . '.' . $baseExt;
					$imageResizer->resize($uploaddir . $filename, $uploaddir . $thumbFilename, $this->imgSizeWidthDefault, null, false, $quarity);
					$saveFiles[] = $thumbFilename;

					unset($imageResizer);
				}

			}

			/*** BurgerEditor.afterImageSave ***/
			$this->dispatchEvent('afterImageSave', [
				'data' => [
					'files' => $saveFiles,
					'uploaddir' => $uploaddir,
				],
			]);

			// ファイル読み直し
			$fileList = $this->getFormatedImageList(null, null, null, $this->imageListPerPage);

			// アップロード後はアップロードされたファイルを選択済みとする(先頭は画像無しなので1を指定)
			if(isset($fileList['data'][1])){
				$fileList['pagination']['selectedFileId'] = $fileList['data'][1]['fileId'];
			}

			$result = ['error' => $hasError, 'data' => $fileList['data'], 'pagination' => $fileList['pagination']];
		}

		Configure::write('debug', 0);
		$this->setResponse($this->getResponse()
			->withType('application/json')
			->withCharset('UTF-8')
		);
		echo json_encode($result);
		exit();

	}

	/**
	 * アップロードファイル削除
	 *
	 * @return void
	 */
	public function img_delete()
	{
		$filename = BurgerEditorUtil::mb_basename($this->request->getData('file'));
		$res = 0;
		if (file_exists(BurgerEditorHelper::$imageFileBaseDir . $filename)) {
			$res = unlink(BurgerEditorHelper::$imageFileBaseDir . $filename);

			// サイズ別に生成したファイルがあれば削除
			$baseFile = BurgerEditorUtil::getFileNameNoExtension($filename);
			$baseExt = BurgerEditorUtil::getExtension($filename);
			if (file_exists(BurgerEditorHelper::$imageFileBaseDir . $baseFile . '__org.' . $baseExt)) {
				unlink(BurgerEditorHelper::$imageFileBaseDir . $baseFile . '__org.' . $baseExt);
			}
			if (file_exists(BurgerEditorHelper::$imageFileBaseDir . $baseFile . '__small.' . $baseExt)) {
				unlink(BurgerEditorHelper::$imageFileBaseDir . $baseFile . '__small.' . $baseExt);
			}
		}
		echo intval($res);
		exit;
	}

	/**
	 * アップロードファイル一覧取得
	 *
	 * @return void
	 */
	public function file_list()
	{
		$searchWord = empty($_GET["word"]) ? null : $_GET['word']; // 検索ワード
		$targetPage = empty($_GET["page"]) ? null : $_GET['page']; // ページ番号
		$selectedFilePath = empty($_GET["selected"]) ? null : $_GET['selected']; // 選択済みファイルパス
		$pageList = $this->getFormatedOtherList($searchWord, $targetPage, $selectedFilePath, $this->fileListPerPage);
		$result = ['error' => false, 'data' => $pageList['data'], 'pagination' => $pageList['pagination']];
		Configure::write('debug', 0);
		$this->setResponse($this->getResponse()
			->withType('application/json')
			->withCharset('UTF-8')
		);
		echo json_encode($result);
		exit();
	}

	/**
	 * ファイルアップロード
	 *
	 * @return void
	 */
	public function file_upload()
	{
		BurgerEditorHelper::getFileList(); // ファイル一覧取得・データ更新

		$hasError = false;
		if (!$_FILES) $hasError = 'ファイルがアップロードされていません';
		foreach($_FILES as $name => $fileData) {
			if (!$name) {
				$hasError = 'アップロードに失敗しました';
			} else {
				if ($fileData["error"] == UPLOAD_ERR_INI_SIZE) {
					$hasError = 'ファイル容量が大きすぎます';
				}
				if (!BcUtil::isAdminUser() || !Configure::read('Bge.allowedAdmin')) {
					$fileExt = BurgerEditorUtil::getExtension($fileData['name']);
					if (!in_array(strtolower($fileExt), explode(',', Configure::read('Bge.allowedExt')))) {
						$hasError = '許可されていないファイル形式です';
					}
				}
				if ($fileData["error"] == UPLOAD_ERR_PARTIAL) {
					$hasError = "ファイルが正しくアップロードされませんでした";
				}
			}

			// ファイルデータサイズ制限
			if (!$hasError && $fileData['size'] > $this->fileDataMaxSize) {
				$viewSize = (($this->fileDataMaxSize / 1024) / 1024) . 'MB';
				$hasError = "データサイズは{$viewSize}以下のファイルをアップロードしてください";
			}

			if ($hasError) break;
		}

		// 何かしらエラー
		if ($hasError) {
			$result = ['error' => $hasError, 'data' => $hasError];
		} else {

			// 保存
			$uploaddir = BurgerEditorHelper::$otherFileBaseDir;
			foreach($_FILES as $name => $fileData) {
				BurgerEditorHelper::$otherFileMaxId++;
				$basename = $fileData["name"];
				$filename = (BurgerEditorHelper::$otherFileMaxId) . "__" . BurgerEditorUtil::b64e(BurgerEditorUtil::getFileNameNoExtension($basename));
				$ext = BurgerEditorUtil::getExtension($basename);
				if ($ext) {
					$filename .= "." . $ext;
				}
				move_uploaded_file($fileData["tmp_name"], $uploaddir . $filename);
			}

			// ファイル読み直し
			$fileList = $this->getFormatedOtherList(null, null, null, $this->fileListPerPage);

			// アップロード後はアップロードされたファイルを選択済みとする
			if(isset($fileList['data'][0])){
				$fileList['pagination']['selectedFileId'] = $fileList['data'][0]['fileId'];
			}

			$result = ['error' => $hasError, 'data'=> $fileList['data'], 'pagination' => $fileList['pagination']];
		}

		Configure::write('debug', 0);
		$this->setResponse($this->getResponse()
			->withType('application/json')
			->withCharset('UTF-8')
		);
		echo json_encode($result);
		exit();

	}

	/**
	 * アップロードファイル削除
	 *
	 * @return void
	 */
	public function file_delete()
	{
		$filename = BurgerEditorUtil::mb_basename($this->request->getData('file'));
		if (file_exists(BurgerEditorHelper::$otherFileBaseDir . $filename)) {
			unlink(BurgerEditorHelper::$otherFileBaseDir . $filename);
		}
		echo '1';
		exit;
	}

	/**
	 * base64encodeされたファイル名をdecodeして変換
	 *
	 * @param $encodedFileName
	 * @return void
	 */
	public function get_filename($encodedFileName)
	{
		// no__から始まるファイル名のみ
		Configure::write('debug', 0);
		$this->setResponse($this->getResponse()
			->withType('application/json')
			->withCharset('UTF-8')
		);

		$fileId = preg_match("/^(\d+)__/", $encodedFileName, $maches);
		if (isset($maches[1])) {
			$fileId = (isset($maches[1]))? $maches[1] : '';
			$basename = BurgerEditorUtil::getFileNameNoExtension(preg_replace("/^\d+__/", "", $encodedFileName));
			$ext = '';
			if (BurgerEditorUtil::getExtension($encodedFileName)) {
				$ext = "." . BurgerEditorUtil::getExtension($encodedFileName);
			}
			$filename = $fileId . '.' . BurgerEditorUtil::b64d($basename) . $ext;
			echo json_encode(['filename' => $filename]);

		} else {
			echo json_encode(['filename' => $encodedFileName]);
		}
		exit;
	}

	/**
	 * JSON変換用画像ファイルリストを取得する
	 *
	 * @return array
	 */
	protected function getFormatedImageList($searchWord = null, $targetPage = null, $selectedFilePath = null, $pageNum = 10)
	{
		$fileList = [];
		$imageFileList = BurgerEditorHelper::getImageList();
		$selectedFileId = null;
		foreach($imageFileList as $filePath) {
			$fileId = preg_match("/^(\d+)__/", BurgerEditorUtil::mb_basename($filePath), $maches);
			$fileId = (isset($maches[1]))? $maches[1] : '';
			$filename = preg_replace("/^\d+__/", "", BurgerEditorUtil::mb_basename($filePath));
			$basename = BurgerEditorUtil::getFileNameNoExtension($filename);
			$ext = BurgerEditorUtil::getExtension($filePath);
			$orgImage = 0;
			$smallImage = 0;
			if ($filename != BurgerEditorUtil::mb_basename($filePath)) {
				$filename = BurgerEditorUtil::b64d($basename) . "." . $ext;

				if (file_exists(BurgerEditorHelper::$imageFileBaseDir . $fileId . '__' . $basename . '__org.' . $ext)) {
					$orgImage = h(BurgerEditorHelper::$imageFileBaseURL . $fileId . '__' . $basename . '__org.' . $ext);
				}
				if (file_exists(BurgerEditorHelper::$imageFileBaseDir . $fileId . '__' . $basename . '__small.' . $ext)) {
					$smallImage = h(BurgerEditorHelper::$imageFileBaseURL . $fileId . '__' . $basename . '__small.' . $ext);
				}
			}
			if (file_exists($filePath)) {
				if ($searchWord === null || (strpos($fileId, $searchWord) !== false || strpos($filename, $searchWord) !== false)) {
					$fileList[] = [
						'url' => h(BurgerEditorHelper::$imageFileBaseURL . BurgerEditorUtil::mb_basename($filePath)),
						'fileId' => $fileId,
						'name' => mb_convert_encoding($filename, 'UTF-8', 'UTF-8'),  // 文字化けファイルがアップロードされた場合JSONが変換できないためUTF-8として読み込める文字に変換
						'filetime' => date('Y/m/d H:i', filemtime($filePath)),
						'size' => filesize($filePath),
						'original' => $orgImage,
						'thumb' => $smallImage,
					];

					// 選択済みファイルIDを保持
					if($selectedFilePath && BurgerEditorUtil::mb_basename($filePath) === BurgerEditorUtil::mb_basename($selectedFilePath)) $selectedFileId = $fileId;
				}
			}
		}

		// No(fileId)の降順に並べ替える
		$fileList = Hash::sort($fileList, '{n}.fileId', 'DESC');

		$webroot = $this->getRequest()->getAttribute('webroot');

		// 画像なしを先頭へ追加
		array_unshift(
			$fileList,
			[
				"url" => $webroot . 'files/bgeditor/bg-noimage.gif',
				'fileId' => '',
				'name' => '画像無し',
				'filetime' => '',
				'size' => 0,
				'original' => 0,
				'thumb' => 0,
			]
		);

		// ページ切り出し＆ページネーション作成
		return BurgerEditorHelper::getFileListWithPagination($fileList, $targetPage, $selectedFileId, $pageNum);
	}

	/**
	 * JSON変換用ファイルリストを取得する
	 *
	 * @return array
	 */
	protected function getFormatedOtherList($searchWord = null, $targetPage = null, $selectedFilePath = null, $pageNum = 10)
	{
		$fileList = [];
		$user = BcUtil::loginUser();
		$otherFileList = BurgerEditorHelper::getFileList();
		$selectedFileId = null;
		foreach($otherFileList as $filePath) {
			$fileId = preg_match("/^(\d+)__/", BurgerEditorUtil::mb_basename($filePath), $maches);
			$fileId = (isset($maches[1]))? $maches[1] : '';
			$filename = preg_replace("/^\d+__/", "", BurgerEditorUtil::mb_basename($filePath));
			$basename = BurgerEditorUtil::getFileNameNoExtension($filename);
			if ($filename != BurgerEditorUtil::mb_basename($filePath)) {
				$filename = BurgerEditorUtil::b64d($basename) . "." . BurgerEditorUtil::getExtension($filePath);
			}

			// ファイルパスのディレクトリを取得する
			$fileNameAry = str_replace(BurgerEditorHelper::$otherFileBaseDir, '', $filePath);
			// 設定値により、ユーザ別にファイル場所を設置している場合
			if (!Configure::read("Bge.fileShare")) {
				$fileNameAry = $user['id'] . DS . $fileNameAry;
			}
			$urlAry = ['prefix' => false, 'plugin' => 'BcBurgerEditor', 'controller' => 'burger_editor', 'action' => 'dl'] + explode(DS, $fileNameAry);

			if (file_exists($filePath)) {
				if ($searchWord === null || (strpos($fileId, $searchWord) !== false || strpos($filename, $searchWord) !== false)) {
					$fileList[] = [
						'url' => h(Router::url($urlAry)),
						'fileId' => $fileId,
						'name' => mb_convert_encoding($filename, 'UTF-8', 'UTF-8'), // 文字化けファイルがアップロードされた場合JSONが変換できないためUTF-8として読み込める文字に変換
						'filetime' => date('Y/m/d H:i', filemtime($filePath)),
						'size' => filesize($filePath)
					];

					// 選択済みファイルIDを保持
					if($selectedFilePath && BurgerEditorUtil::mb_basename($filePath) === BurgerEditorUtil::mb_basename($selectedFilePath)) $selectedFileId = $fileId;
				}
			}
		}

		// No(fileId)の降順に並べ替える
		$fileList = Hash::sort($fileList, '{n}.fileId', 'DESC');

		// ページ切り出し＆ページネーション作成
		return BurgerEditorHelper::getFileListWithPagination($fileList, $targetPage, $selectedFileId, $pageNum);
	}

}

