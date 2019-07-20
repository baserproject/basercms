<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.Behavior
 * @since			baserCMS v 1.5.3
 * @license			http://basercms.net/license/index.html
 */

App::uses('Imageresizer', 'Vendor');

/**
 * ファイルアップロードビヘイビア
 *
 * 《設定例》
 * public $actsAs = array(
 *  'BcUpload' => array(
 *     'saveDir'  => "editor",
 *     'fields'  => array(
 *       'image'  => array(
 *         'type'      => 'image',
 *         'namefield'    => 'id',
 *         'nameadd'    => false,
 * 			'subdirDateFormat'	=> 'Y/m'	// Or false
 *         'imageresize'  => array('prefix' => 'template', 'width' => '100', 'height' => '100'),
 * 				'imagecopy'		=> array(
 * 					'thumb'			=> array('suffix' => 'template', 'width' => '150', 'height' => '150'),
 * 					'thumb_mobile'	=> array('suffix' => 'template', 'width' => '100', 'height' => '100')
 * 				)
 *       ),
 *       'pdf' => array(
 *         'type'      => 'pdf',
 *         'namefield'    => 'id',
 *         'nameformat'  => '%d',
 *         'nameadd'    => false
 *       )
 *     )
 *   )
 * );
 *
 * @package Baser.Model.Behavior
 */
class BcUploadBehavior extends ModelBehavior {

/**
 * 保存ディレクトリ
 *
 * @var string[]
 */
	public $savePath = [];

/**
 * 保存時にファイルの重複確認を行うディレクトリ
 *
 * @var array
 */
	public $existsCheckDirs = [];

/**
 * 設定
 *
 * @var array
 */
	public $settings = null;

/**
 * 一時ID
 *
 * @var string
 */
	public $tmpId = null;

/**
 * Session
 *
 * @var \SessionComponent
 */
	public $Session = null;

/**
 * 画像拡張子
 *
 * @var array
 */
	public $imgExts = array('gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png');

/**
 * アップロードしたかどうか
 *
 * afterSave のリネーム判定に利用
 * モデルごとに設定する
 *
 * @var array
 */
	public $uploaded = [];

/**
 * セットアップ
 *
 * @param Model	$Model
 * @param array	$settings actsAsの設定
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = Hash::merge([
			'saveDir' => '',
			'existsCheckDirs' => [],
			'fields' => []
		], $settings);
		foreach ($this->settings[$Model->alias]['fields'] as $key => $field) {
			if (empty($field['name'])) {
				$this->settings[$Model->alias]['fields'][$key]['name'] = $field['name'] = $key;
			}
			if (!empty($field['imageresize'])) {
				if (empty($field['imageresize']['thumb'])) {
					$this->settings[$Model->alias]['fields'][$key]['imageresize']['thumb'] = false;
				}
			} else {
				$this->settings[$Model->alias]['fields'][$key]['imageresize'] = false;
			}
			if(!isset($field['getUniqueFileName'])) {
				$this->settings[$Model->alias]['fields'][$key]['getUniqueFileName'] = true;
			}
		}
		$this->savePath[$Model->alias] = $this->getSaveDir($Model);
		if (!is_dir($this->savePath[$Model->alias])) {
			$Folder = new Folder();
			$Folder->create($this->savePath[$Model->alias]);
			$Folder->chmod($this->savePath[$Model->alias], 0777, true);
		}

		$this->existsCheckDirs[$Model->alias] = $this->getExistsCheckDirs($Model);

		App::uses('SessionComponent', 'Controller/Component');
		$this->Session = new SessionComponent(new ComponentCollection());
	}

/**
 * Before Validate
 *
 * @param Model $Model
 * @param array $options
 * @return mixed
 */
	public function beforeValidate(Model $Model, $options = array()) {
		$this->setupRequestData($Model);
		return parent::beforeValidate($Model, $options);
	}

/**
 * Before save
 *
 * @param Model $Model
 * @param array $options
 * @return boolean
 */
	public function beforeSave(Model $Model, $options = array()) {
		if($Model->exists()) {
			$this->deleteExistingFiles($Model);
		}
		$Model->data = $this->deleteFiles($Model, $Model->data);

		$result = $this->saveFiles($Model, $Model->data);
		if ($result) {
			$Model->data = $result;
			return true;
		} else {
			return false;
		}
	}

/**
 * リクエストされたデータを処理しやすいようにセットアップする
 *
 * @param Model $Model
 */
	public function setupRequestData(Model $Model) {
		foreach ($this->settings[$Model->alias]['fields'] as $key => $field) {
			$data = [];
			$upload = false;
			if(!empty($Model->data[$Model->name])) {
				$data = $Model->data[$Model->name];
			}
			if (!empty($data[$field['name']]) && is_array($data[$field['name']]) && $data[$field['name']]['size'] != 0) {
				if (!empty($data[$field['name']]['name'])) {
					$upload = true;
				}
			} else {
				if (!empty($Model->data[$Model->name][$field['name'] . '_tmp'])) {
					// セッションに一時ファイルが保存されている場合は復元する
					if($this->moveFileSessionToTmp($Model, $field['name'])) {
						$data = $Model->data[$Model->name];
						$upload = true;
					}
				} elseif (!empty($Model->data[$Model->name][$field['name'] . '_'])) {
					// 新しいデータが送信されず、既存データを引き継ぐ場合は、元のフィールド名に戻す
					$Model->data[$Model->name][$field['name']] = $Model->data[$Model->name][$field['name'] . '_'];
					unset($Model->data[$Model->name][$field['name'] . '_']);
				}
			}
			if ($upload) {
				// 拡張子を取得
				$this->settings[$Model->alias]['fields'][$key]['ext'] = $field['ext'] = decodeContent($data[$field['name']]['type'], $data[$field['name']]['name']);
				// タイプ別除外
				$targets = [];
				if ($field['type'] == 'image') {
					$targets = $this->imgExts;
				} elseif (is_array($field['type'])) {
					$targets = $field['type'];
				} elseif ($field['type'] != 'all') {
					$targets = [$field['type']];
				}
				if ($targets && !in_array($field['ext'], $targets)) {
					$upload = false;
				}
			}
			$this->settings[$Model->alias]['fields'][$key]['upload'] = $upload;
		}
	}

/**
 * After save
 *
 * @param Model $Model
 * @param bool $created
 * @param array $options
 * @return bool
 */
	public function afterSave(Model $Model, $created, $options = []) {
		if($this->uploaded[$Model->name]) {
			$Model->data = $this->renameToBasenameFields($Model);
			$Model->data = $Model->save($Model->data, array('callbacks' => false, 'validate' => false));
			$this->uploaded[$Model->name] = false;
		}
		foreach($this->settings[$Model->alias]['fields'] as $key => $value) {
			$this->settings[$Model->alias]['fields'][$key]['upload'] = false;
		}
		return true;
	}

/**
 * 一時ファイルとして保存する
 *
 * @param Model $Model
 * @param array $data
 * @param string $tmpId
 * @return mixed false|array
 */
	public function saveTmpFiles(Model $Model, $data, $tmpId) {
		$this->Session->delete('Upload');
		$Model->data = $data;
		$this->tmpId = $tmpId;
		$this->setupRequestData($Model);
		$Model->data = $this->deleteFiles($Model, $Model->data);
		$result = $this->saveFiles($Model, $Model->data);
		if ($result) {
			$Model->data = $result;
			return $Model->data;
		} else {
			return false;
		}
	}

/**
 * 削除対象かチェックしながらファイル群を削除する
 *
 * @param Model $Model
 * @param array $requestData
 * @return array
 */
	public function deleteFiles(Model $Model, $requestData) {

		$oldData = $Model->find('first', [
			'conditions' => [
				$Model->alias . '.' . $Model->primaryKey => $Model->id
			]
		]);
		foreach ($this->settings[$Model->alias]['fields'] as $key => $field) {
			$oldValue = '';
			if($oldData && !empty($oldData[$Model->name][$field['name']])) {
				$oldValue = $oldData[$Model->name][$field['name']];
			} elseif(!empty($Model->data[$Model->name][$field['name']]) && !is_array($Model->data[$Model->name][$field['name']])) {
				$oldValue = $Model->data[$Model->name][$field['name']];
			}
			$requestData = $this->deleteFileWhileChecking($Model, $field, $requestData, $oldValue);
		}
		return $requestData;
	}

/**
 * 削除対象かチェックしながらファイルを削除する
 *
 * @param Model $Model
 * @param array $fieldSetting
 * @param array $requestData
 * @param string $oldValue
 * @return array $requestData
 */
	public function deleteFileWhileChecking(Model $Model, $fieldSetting, $requestData, $oldValue = null) {
		$fieldName = $fieldSetting['name'];
		if (!empty($requestData[$Model->name][$fieldName . '_delete'])) {
			if (!$this->tmpId) {
				$this->delFile($Model, $oldValue, $fieldSetting);
				$requestData[$Model->name][$fieldName] = '';
			} else {
				$requestData[$Model->name][$fieldName] = $oldValue;
			}
		}
		return $requestData;
	}

/**
 * ファイル群を保存する
 *
 * @param Model $Model
 * @param array $requestData
 * @return mixed false|array
 */
	public function saveFiles(Model $Model, $requestData) {
		$this->uploaded[$Model->name] = false;
		foreach ($this->settings[$Model->alias]['fields'] as $key => $field) {
			$result = $this->saveFileWhileChecking($Model, $field, $requestData);
			if($result) {
				$requestData = $result;
			} else {
				// 失敗したら処理を中断してfalseを返す
				return false;
			}
		}
		return $requestData;
	}

/**
 * 保存対象かチェックしながらファイルを保存する
 *
 * @param Model $Model
 * @param array $fieldSetting
 * @param array $requestData
 * @param array $options
 * 	- deleteTmpFiles : 一時ファイルを削除するかどうか
 * @return mixed bool|$requestData
 */
	public function saveFileWhileChecking(Model $Model, $fieldSetting, $requestData, $options = []) {
		$options = array_merge([
			'deleteTmpFiles' => true
		], $options);

		if (empty($requestData[$Model->name][$fieldSetting['name']])
			|| !is_array($requestData[$Model->name][$fieldSetting['name']])
		) {
			return $requestData;
		}

		if(!$this->tmpId && empty($fieldSetting['upload'])) {
			if(!empty($requestData[$Model->name][$fieldSetting['name']]) && is_array($requestData[$Model->name][$fieldSetting['name']])) {
				unset($requestData[$Model->name][$fieldSetting['name']]);
			}
			return $requestData;
		}
		// ファイル名が重複していた場合は変更する
		if($fieldSetting['getUniqueFileName'] && !$this->tmpId) {
			$requestData[$Model->name][$fieldSetting['name']]['name'] = $this->getUniqueFileName($Model, $fieldSetting['name'], $requestData[$Model->name][$fieldSetting['name']]['name'], $fieldSetting);
		}
		// 画像を保存
		$tmpName = (!empty($requestData[$Model->name][$fieldSetting['name']]['tmp_name'])) ? $requestData[$Model->name][$fieldSetting['name']]['tmp_name'] : false;
		if(!$tmpName) {
			return $requestData;
		}
		$fileName = $this->saveFile($Model, $fieldSetting);
		if ($fileName) {
			if(!$this->copyImages($Model, $fieldSetting, $fileName)) {
				return false;
			}
			// ファイルをリサイズ
			if (!$this->tmpId) {
				if(!empty($fieldSetting['imageresize'])) {
					$filePath = $this->savePath[$Model->alias] . $fileName;
					$this->resizeImage($filePath, $filePath, $fieldSetting['imageresize']['width'], $fieldSetting['imageresize']['height'], $fieldSetting['imageresize']['thumb']);
				}
				$requestData[$Model->name][$fieldSetting['name']] = $fileName;
			} else {
				$requestData[$Model->name][$fieldSetting['name']]['session_key'] = $fileName;
			}
			// 一時ファイルを削除
			if($options['deleteTmpFiles']) {
				@unlink($tmpName);
			}
			$this->uploaded[$Model->name] = true;
		} else {
			if($this->tmpId) {
				return $requestData;
			} else {
				return false;
			}
		}
		return $requestData;
	}

/**
 * セッションに保存されたファイルデータをファイルとして保存する
 *
 * @param Model $Model
 * @param string $fieldName
 * @return boolean
 */
	public function moveFileSessionToTmp(Model $Model, $fieldName) {
		$fileName = $Model->data[$Model->alias][$fieldName . '_tmp'];
		$sessionKey = str_replace(array('.', '/'), array('_', '_'), $fileName);
		$tmpName = $this->savePath[$Model->alias] . $sessionKey;
		$fileData = $this->Session->read('Upload.' . $sessionKey . '.data');
		$fileType = $this->Session->read('Upload.' . $sessionKey . '.type');
		$this->Session->delete('Upload.' . $sessionKey);

		// サイズを取得
		if (ini_get('mbstring.func_overload') & 2 && function_exists('mb_strlen')) {
			$fileSize = mb_strlen($fileData, 'ASCII');
		} else {
			$fileSize = strlen($fileData);
		}

		if ($fileSize == 0) {
			return false;
		}

		// ファイルを一時ファイルとして保存
		$file = new File($tmpName, true, 0666);
		$file->write($fileData);
		$file->close();

		// 元の名前を取得
		/*$pos = strpos($sessionKey, '_');
		$fileName = substr($sessionKey, $pos + 1, strlen($sessionKey));*/

		// アップロードされたデータとしてデータを復元する
		$uploadInfo['error'] = 0;
		$uploadInfo['name'] = $fileName;
		$uploadInfo['tmp_name'] = $tmpName;
		$uploadInfo['size'] = $fileSize;
		$uploadInfo['type'] = $fileType;
		$Model->data[$Model->alias][$fieldName] = $uploadInfo;
		unset($Model->data[$Model->alias][$fieldName . '_tmp']);
		return true;
	}

/**
 * ファイルを保存する
 *
 * @param Model $Model
 * @param array $field 画像保存対象フィールドの設定
 * @return mixed false|ファイル名
 */
	public function saveFile(Model $Model, $field) {
		// データを取得
		$file = $Model->data[$Model->name][$field['name']];

		if (empty($file['tmp_name'])) {
			return false;
		}
		if (!empty($file['error']) && $file['error'] != 0) {
			return false;
		}

		$fileName = $this->getSaveFileName($Model, $field, $file['name']);
		$filePath = $this->savePath[$Model->alias] . $fileName;
		$this->rotateImage($file['tmp_name']);

		if (!$this->tmpId) {
			if (copy($file['tmp_name'], $filePath)) {
				chmod($filePath, 0666);
				$ret = $fileName;
			} else {
				$ret = false;
			}
		} else {
			$_fileName = str_replace(array('.', '/'), array('_', '_'), $fileName);
			$this->Session->write('Upload.' . $_fileName, $field);
			$this->Session->write('Upload.' . $_fileName . '.type', $file['type']);
			$this->Session->write('Upload.' . $_fileName . '.data', file_get_contents($file['tmp_name']));
			return $fileName;
		}

		return $ret;
	}

/**
 * 保存用ファイル名を取得する
 *
 * @param Model $Model
 * @param $field
 * @param $name
 * @return mixed|string
 */
	public function getSaveFileName(Model $Model, $field, $name) {
		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if (!empty($field['prefix'])) {
			$prefix = $field['prefix'];
		}
		if (!empty($field['suffix'])) {
			$suffix = $field['suffix'];
		}
		// 保存ファイル名を生成
		if (!$this->tmpId) {
			$basename = preg_replace("/\." . $field['ext'] . "$/is", '', $name);
			$fileName = $prefix . $basename . $suffix . '.' . $field['ext'];
			$existsFile = false;
			foreach ($this->existsCheckDirs[$Model->alias] as $existsCheckDir) {
				if (file_exists($existsCheckDir . $fileName)) {
					$existsFile = true;
					break;
				}
			}
			if ($existsFile) {
				if(preg_match('/(.+_)([0-9]+)$/', $basename, $matches)) {
					$basename = $matches[1] . ((int) $matches[2] + 1);
				} else {
					$basename = $basename . '_1';
				}
				$fileName = $this->getSaveFileName($Model, $field, $basename . '.' . $field['ext']);
			}
		} else {
			if (!empty($field['namefield'])) {
				$Model->data[$Model->alias][$field['namefield']] = $this->tmpId;
				$fileName = $this->getFieldBasename($Model, $field, $field['ext']);
			} else {
				$fileName = $this->tmpId . '_' . $field['name'] . '.' . $field['ext'];
			}
		}
		return $fileName;
	}

/**
 * 画像をExif情報を元に正しい確度に回転する
 *
 * @param $file
 * @return bool
 */
	public function rotateImage($file) {
		if(!function_exists('exif_read_data')) {
			return false;
		}
		$exif = @exif_read_data($file);
		if(empty($exif) || empty($exif['Orientation'])) {
			return true;
		}
		switch($exif['Orientation']) {
			case 3:
				$angle = 180;
				break;
			case 6:
				$angle = 270;
				break;
			case 8:
				$angle = 90;
				break;
			default:
				return true;
		}
		$imgInfo = getimagesize($file);
		$imageType = $imgInfo[2];
		// 元となる画像のオブジェクトを生成
		switch($imageType) {
			case IMAGETYPE_GIF:
				$srcImage = imagecreatefromgif($file);
				break;
			case IMAGETYPE_JPEG:
				$srcImage = imagecreatefromjpeg($file);
				break;
			case IMAGETYPE_PNG:
				$srcImage = imagecreatefrompng($file);
				break;
			default:
				return false;
		}
		$rotate = imagerotate($srcImage, $angle, 0);
		switch($imageType) {
			case IMAGETYPE_GIF:
				imagegif($rotate, $file);
				break;
			case IMAGETYPE_JPEG:
				imagejpeg($rotate, $file, 100);
				break;
			case IMAGETYPE_PNG:
				imagepng($rotate, $file);
				break;
			default:
				return false;
		}
		imagedestroy($srcImage);
		imagedestroy($rotate);
		return true;
	}

/**
 * 画像をコピーする
 *
 * @param Model $Model
 * @param array $field 画像保存対象フィールドの設定
 * @return boolean
 */
	public function copyImage(Model $Model, $field) {
		// データを取得
		$file = $Model->data[$Model->name][$field['name']];

		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if (!empty($field['prefix'])) {
			$prefix = $field['prefix'];
		}
		if (!empty($field['suffix'])) {
			$suffix = $field['suffix'];
		}

		// 保存ファイル名を生成
		$basename = preg_replace("/\." . $field['ext'] . "$/is", '', $file['name']);
		$fileName = $prefix . $basename . $suffix . '.' . $field['ext'];

		$filePath = $this->savePath[$Model->alias] . $fileName;

		if (!empty($field['thumb'])) {
			$thumb = $field['thumb'];
		} else {
			$thumb = false;
		}

		return $this->resizeImage($Model->data[$Model->name][$field['name']]['tmp_name'], $filePath, $field['width'], $field['height'], $thumb);
	}

/**
 * 画像ファイルをコピーする
 * リサイズ可能
 *
 * @param string $source コピー元のパス
 * @param string $distination コピー先のパス
 * @param int $width 横幅
 * @param int $height 高さ
 * @param boolean $thumb サムネイルとしてコピーするか
 * @return boolean
 */
	public function resizeImage($source, $distination, $width = 0, $height = 0, $thumb = false) {
		if ($width > 0 || $height > 0) {
			$imageresizer = new Imageresizer();
			$ret = $imageresizer->resize($source, $distination, $width, $height, $thumb);
		} else {
			$ret = copy($source, $distination);
		}

		if ($ret) {
			chmod($distination, 0666);
		}

		return $ret;
	}

/**
 * 画像のサイズを取得
 *
 * 指定したパスにある画像のサイズを配列(高さ、横幅)で返す
 *
 * @param string $path 画像のパス
 * @return mixed array / false
 */
	public function getImageSize($path) {
		$imginfo = getimagesize($path);
		if ($imginfo) {
			return array('width' => $imginfo[0], 'height' => $imginfo[1]);
		}
		return false;
	}

/**
 * Before delete
 * 画像ファイルの削除を行う
 * 削除に失敗してもデータの削除は行う
 *
 * @param Model $Model
 * @param bool $cascade
 * @return bool
 */
	public function beforeDelete(Model $Model, $cascade = true) {
		$Model->data = $Model->find('first', [
			'conditions' => [
				$Model->alias . '.' . $Model->primaryKey => $Model->id
			]
		]);
		$this->delFiles($Model);
		return true;
	}

/**
 * 画像ファイル群を削除する
 *
 * @param Model $Model
 * @param string $fieldName フィールド名
 */
	public function delFiles(Model $Model, $fieldName = null) {
		foreach ($this->settings[$Model->alias]['fields'] as $key => $field) {
			if (empty($field['name'])) {
				$field['name'] = $key;
			}
			if (!$fieldName || ($fieldName && $fieldName == $field['name'])) {
				if (!empty($Model->data[$Model->name][$field['name']])) {
					$file = $Model->data[$Model->name][$field['name']];

					// DBに保存されているファイル名から拡張子を取得する
					preg_match('/\.([^.]+)\z/', $file, $match);
					if (!empty($match[1])) {
						$field['ext'] = $match[1];
					}

					$this->delFile($Model, $file, $field);
				}
			}
		}
	}

/**
 * ファイルを削除する
 *
 * @param Model $Model
 * @param string $file
 * @param array $field 保存対象フィールドの設定
 * - ext 対象のファイル拡張子
 * - prefix 対象のファイルの接頭辞
 * - suffix 対象のファイルの接尾辞
 * @param boolean $delImagecopy
 * @return boolean
 */
	public function delFile(Model $Model, $file, $field, $delImagecopy = true) {
		if (!$file) {
			return true;
		}

		if (empty($field['ext'])) {
			$pathinfo = pathinfo($file);
			$field['ext'] = $pathinfo['extension'];
		}

		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if (!empty($field['prefix'])) {
			$prefix = $field['prefix'];
		}
		if (!empty($field['suffix'])) {
			$suffix = $field['suffix'];
		}

		// 保存ファイル名を生成
		$basename = preg_replace("/\." . $field['ext'] . "$/is", '', $file);
		$fileName = $prefix . $basename . $suffix . '.' . $field['ext'];
		$filePath = $this->savePath[$Model->alias] . $fileName;
		if (!empty($field['imagecopy']) && $delImagecopy) {
			foreach ($field['imagecopy'] as $copy) {
				$copy['name'] = $field['name'];
				$copy['ext'] = $field['ext'];
				$this->delFile($Model, $file, $copy, false);
			}
		}

		if (file_exists($filePath)) {
			return unlink($filePath);
		}

		return true;
	}

/**
 * 全フィールドのファイル名をフィールド値ベースのファイル名に変更する
 *
 * @param Model $Model
 * @param bool $copy
 * @return array
 */
	public function renameToBasenameFields(Model $Model, $copy = false) {
		$data = $Model->data;
		foreach ($this->settings[$Model->alias]['fields'] as $key => $setting) {
			if (empty($setting['name'])) {
				$setting['name'] = $key;
			}
			$value = $this->renameToBasenameField($Model, $setting, $copy);
			if($value !== false) {
				$data[$Model->alias][$setting['name']] = $value;
			}
		}
		return $data;
	}

/**
 * ファイル名をフィールド値ベースのファイル名に変更する
 *
 * @param \Model $Model
 * @param array $setting
 * @param bool $copy
 * @return bool|mixed
 */
	public function renameToBasenameField(Model $Model, $setting, $copy = false) {
		if (empty($setting['namefield']) || empty($Model->data[$Model->alias][$setting['name']])) {
			return false;
		}
		$oldName = $Model->data[$Model->alias][$setting['name']];
		if(is_array($oldName)) {
			return false;
		}
		$saveDir = $this->savePath[$Model->alias];
		$saveDirInTheme = $this->getSaveDir($Model, true);
		$oldSaveDir = '';
		if(file_exists($saveDir . $oldName)) {
			$oldSaveDir = $saveDir;
		} elseif(file_exists($saveDirInTheme . $oldName)) {
			$oldSaveDir = $saveDirInTheme;
		}
		if (!file_exists($oldSaveDir . $oldName)) {
			return '';
		}
		$pathinfo = pathinfo($oldName);
		$newName = $this->getFieldBasename($Model, $setting, $pathinfo['extension']);
		if (!$newName) {
			return false;
		}
		if ($oldName == $newName) {
			return false;
		}
		if (!empty($setting['imageresize'])) {
			$newName = $this->getFileName($Model, $setting['imageresize'], $newName);
		} else {
			$newName = $this->getFileName($Model, null, $newName);
		}

		if (!$copy) {
			rename($oldSaveDir . $oldName, $saveDir . $newName);
		} else {
			copy($oldSaveDir . $oldName, $saveDir . $newName);
		}
		if (!empty($setting['imagecopy'])) {
			foreach ($setting['imagecopy'] as $copysetting) {
				$oldCopyname = $this->getFileName($Model, $copysetting, $oldName);
				if (file_exists($oldSaveDir . $oldCopyname)) {
					$newCopyname = $this->getFileName($Model, $copysetting, $newName);
					if (!$copy) {
						rename($oldSaveDir . $oldCopyname, $saveDir . $newCopyname);
					} else {
						copy($oldSaveDir . $oldCopyname, $saveDir . $newCopyname);
					}
				}
			}
		}
		return str_replace(DS, '/', $newName);
	}

/**
 * フィールドベースのファイル名を取得する
 *
 * @param Model $Model
 * @param array $setting
 * - namefield 対象となるファイルのベースの名前が格納されたフィールド名
 * - nameformat ファイル名のフォーマット
 * - name ファイル名の後に追加する名前
 * - nameadd nameを追加しないか
 * @param string $ext ファイルの拡張子
 * @return mixed false / string
 */
	public function getFieldBasename(Model $Model, $setting, $ext) {
		if (empty($setting['namefield'])) {
			return false;
		}
		$data = $Model->data[$Model->alias];
		if (!isset($data[$setting['namefield']])) {
			if ($setting['namefield'] == 'id' && $Model->id) {
				$basename = $Model->id;
			} else {
				return false;
			}
		} else {
			$basename = $data[$setting['namefield']];
		}

		if (!empty($setting['nameformat'])) {
			$basename = sprintf($setting['nameformat'], $basename);
		}

		if (!isset($setting['nameadd']) || $setting['nameadd'] !== false) {
			$basename .= '_' . $setting['name'];
		}

		$subdir = '';
		if (!empty($this->settings[$Model->alias]['subdirDateFormat'])) {
			$subdir .= date($this->settings[$Model->alias]['subdirDateFormat']);
			if (!preg_match('/\/$/', $subdir)) {
				$subdir .= '/';
			}
			$subdir = str_replace('/', DS, $subdir);
			$path = $this->savePath[$Model->alias] . $subdir;
			if (!is_dir($path)) {
				$Folder = new Folder();
				$Folder->create($path);
				$Folder->chmod($path, 0777);
			}
		}

		return $subdir . $basename . '.' . $ext;
	}

/**
 * ベースファイル名からプレフィックス付のファイル名を取得する
 *
 * @param Model $Model
 * @param array $setting
 * @param string $filename
 * @return string
 */
	public function getFileName(Model $Model, $setting, $filename) {
		if (empty($setting)) {
			return $filename;
		}

		$pathinfo = pathinfo($filename);
		$ext = $pathinfo['extension'];
		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if (!empty($setting['prefix'])) {
			$prefix = $setting['prefix'];
		}
		if (!empty($setting['suffix'])) {
			$suffix = $setting['suffix'];
		}

		$basename = preg_replace("/\." . $ext . "$/is", '', $filename);
		return $prefix . $basename . $suffix . '.' . $ext;
	}

/**
 * ファイル名からベースファイル名を取得する
 *
 * @param Model $Model
 * @param array $setting
 * @param string $filename
 * @return string
 */
	public function getBasename(Model $Model, $setting, $filename) {
		$pattern = "/^" . $setting['prefix'] . "(.*?)" . $setting['suffix'] . "\.[a-zA-Z0-9]*$/is";
		if (preg_match($pattern, $filename, $maches)) {
			return $maches[1];
		} else {
			return '';
		}
	}

/**
 * 一意のファイル名を取得する
 *
 * @param string $fieldName 一意の名前を取得する元となるフィールド名
 * @param string $fileName 対象のファイル名
 * @return string
 */
	public function getUniqueFileName(Model $Model, $fieldName, $fileName, $setting = null) {
		$pathinfo = pathinfo($fileName);
		$basename = preg_replace("/\." . $pathinfo['extension'] . "$/is", '', $fileName);

		$ext = $setting['ext'];

		// 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
		$conditions[$Model->name . '.' . $fieldName . ' LIKE'] = $basename . '%' . $ext;
		$datas = $Model->find('all', array('conditions' => $conditions, 'fields' => array($fieldName), 'order' => "{$Model->name}.{$fieldName}"));
		$datas = Hash::extract($datas, "{n}.{$Model->name}.{$fieldName}");
		$numbers = array();

		if ($datas) {
			foreach($datas as $data) {
				$_basename = preg_replace("/\." . $ext . "$/is", '', $data);
				$lastPrefix = preg_replace('/^' . preg_quote($basename, '/') . '/', '', $_basename);
				if(!$lastPrefix) {
					$numbers[1] = 1;
				} elseif (preg_match("/^__([0-9]+)$/s", $lastPrefix, $matches)) {
					$numbers[$matches[1]] = true;
				}
			}
			if($numbers) {
				$prefixNo = 1;
				while(true) {
					if(!isset($numbers[$prefixNo])) {
						break;
					}
					$prefixNo++;
				}
				if($prefixNo == 1) {
					return $basename . '.' . $ext;
				} else {
					return $basename . '__' . ($prefixNo) . '.' . $ext;
				}
			} else {
				return $basename . '.' . $ext;
			}
		} else {
			return $basename . '.' . $ext;
		}

	}

/**
 * 保存先のフォルダを取得する
 *
 * @param Model $Model
 * @param bool $isTheme
 * @return string $saveDir
 */
	public function getSaveDir(Model $Model, $isTheme = false, $limited = false) {
		if(!$isTheme) {
			$basePath = WWW_ROOT . 'files' . DS;
		} else {
			$siteConfig = Configure::read('BcSite');
			$theme = $siteConfig['theme'];
			if($theme) {
				$basePath = WWW_ROOT . 'theme' . DS . $theme . DS . 'files' . DS;
			} else {
				$basePath = getViewPath() . 'files' . DS;
			}
		}
		if($limited) {
			$basePath = $basePath . $limited . DS;
		}
		if ($this->settings[$Model->alias]['saveDir']) {
			$saveDir = $basePath . $this->settings[$Model->alias]['saveDir'] . DS;
		} else {
			$saveDir = $basePath;
		}
		return $saveDir;
	}

/**
 * 保存時にファイルの重複確認を行うディレクトリのリストを取得する
 *
 * @param Model $Model
 * @return array $existsCheckDirs
 */
	private function getExistsCheckDirs(Model $Model) {
		$existsCheckDirs = [];
		$existsCheckDirs[] = $this->savePath[$Model->alias];

		$basePath = WWW_ROOT . 'files' . DS;
		if ($this->settings[$Model->alias]['existsCheckDirs']) {
			foreach ($this->settings[$Model->alias]['existsCheckDirs'] as $existsCheckDir) {
				$existsCheckDirs[] = $basePath . $existsCheckDir . DS;
			}
		}

		return $existsCheckDirs;
	}

/**
 * 既に存在するデータのファイルを削除する
 *
 * @param Model $Model
 */
	public function deleteExistingFiles(Model $Model) {
		$dataTmp = $Model->data[$Model->alias];
		$uploadFields = array_keys($this->settings[$Model->alias]['fields']);
		$targetFields = [];
		foreach($uploadFields as $field) {
			if(!empty($dataTmp[$field]['tmp_name'])) {
				$targetFields[] = $field;
			}
		}
		if(!$targetFields) {
			return;
		}
		$Model->set($Model->find('first', [
			'conditions' => [$Model->alias . '.' . $Model->primaryKey => $Model->data[$Model->alias][$Model->primaryKey]],
			'recursive' => -1
		]));
		foreach($targetFields as $field) {
			$this->delFiles($Model, $field);
		}
		$Model->set($dataTmp);
	}

/**
 * 画像をコピーする
 *
 * @param Model $Model
 * @param string $fileName
 * @param array $field
 * @return bool
 */
	public function copyImages(Model $Model, $field, $fileName) {
		if (!$this->tmpId && ($field['type'] == 'all' || $field['type'] == 'image') && !empty($field['imagecopy']) && in_array($field['ext'], $this->imgExts)) {
			foreach ($field['imagecopy'] as $copy) {
				// コピー画像が元画像より大きい場合はスキップして作成しない
				$size = $this->getImageSize($this->savePath[$Model->alias] . $fileName);
				if ($size && $size['width'] < $copy['width'] && $size['height'] < $copy['height']) {
					if (isset($copy['smallskip']) && $copy['smallskip'] === false) {
						$copy['width'] = $size['width'];
						$copy['height'] = $size['height'];
					} else {
						continue;
					}
				}

				// ファイル名の重複を回避する為の処理、元画像ファイルと同様に、コピー画像ファイルにも対応する
				if (isset($Model->data[$Model->alias]['name']['name']) && $fileName !== $Model->data[$Model->alias]['name']['name']) {
					$Model->data[$Model->alias]['name']['name'] = $fileName;
				}
				$copy['name'] = $field['name'];
				$copy['ext'] = $field['ext'];
				$ret = $this->copyImage($Model, $copy);
				if (!$ret) {
					// 失敗したら処理を中断してfalseを返す
					return false;
				}
			}
		}
		return true;
	}

}
