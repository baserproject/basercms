<?php
/**
 * ファイルアップロードビヘイビア
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.Behavior
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
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
 * @var string
 * @access public
 */
	public $savePath = '';

/**
 * 設定
 * 
 * @var array
 * @access public
 */
	public $settings = null;

/**
 * 一時ID
 * 
 * @var string
 * @access public
 */
	public $tmpId = null;

/**
 * Session
 * 
 * @var Session
 * @access public
 */
	public $Session = null;

/**
 * 画像拡張子
 * 
 * @var array 
 */
	public $imgExts = array('gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png');

/**
 * セットアップ
 * 
 * @param Model	$Model
 * @param array	actsAsの設定
 * @return void
 * @access public
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = Hash::merge(array(
				'saveDir' => '',
				'fields' => array()
				), $settings);

		if ($this->settings[$Model->alias]['saveDir']) {
			$this->savePath[$Model->alias] = WWW_ROOT . 'files' . DS . $this->settings[$Model->alias]['saveDir'] . DS;
		} else {
			$this->savePath[$Model->alias] = WWW_ROOT . 'files' . DS;
		}

		if (!is_dir($this->savePath[$Model->alias])) {
			$Folder = new Folder();
			$Folder->create($this->savePath[$Model->alias]);
			$Folder->chmod($this->savePath[$Model->alias], 0777, true);
		}

		App::uses('SessionComponent', 'Controller/Component');
		$this->Session = new SessionComponent(new ComponentCollection());
	}

/**
 * Before save
 * 
 * @param Model $Model
 * @param Model $options
 * @return boolean
 * @access public
 */
	public function beforeSave(Model $Model, $options = array()) {
		return $this->saveFiles($Model);
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
	public function afterSave(Model $Model, $created, $options = array()) {
		$this->renameToFieldBasename($Model);
		$Model->data = $Model->save($Model->data, array('callbacks' => false, 'validate' => false));
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
	public function saveTmpFiles(Model $Model, $data, $tmpId) {
		$this->Session->delete('Upload');
		$Model->data = $data;
		$this->tmpId = $tmpId;
		if ($this->saveFiles($Model)) {
			return $Model->data;
		} else {
			return false;
		}
	}

/**
 * ファイル群を保存する
 * 
 * @param Model $Model
 * @return boolean
 * @access public
 */
	public function saveFiles(Model $Model) {
		$serverData = $Model->findById($Model->id);

		foreach ($this->settings[$Model->alias]['fields'] as $key => $field) {

			if (empty($field['name'])) {
				$field['name'] = $key;
			}

			if (!empty($Model->data[$Model->name][$field['name'] . '_delete'])) {
				$file = $serverData[$Model->name][$field['name']];
				if (!$this->tmpId) {
					$this->delFile($Model, $file, $field);
					$Model->data[$Model->name][$field['name']] = '';
				} else {
					$Model->data[$Model->name][$field['name']] = $file;
				}
				continue;
			}

			if (empty($Model->data[$Model->name][$field['name']]['name']) && !empty($Model->data[$Model->name][$field['name'] . '_'])) {
				// 新しいデータが送信されず、既存データを引き継ぐ場合は、元のフィールド名に戻す
				$Model->data[$Model->name][$field['name']] = $Model->data[$Model->name][$field['name'] . '_'];
				unset($Model->data[$Model->name][$field['name'] . '_']);
			} elseif (!empty($Model->data[$Model->name][$field['name'] . '_tmp'])) {
				// セッションに一時ファイルが保存されている場合は復元する
				$this->moveFileSessionToTmp($Model, $field['name']);
			} elseif (!isset($Model->data[$Model->name][$field['name']]) ||
				!is_array($Model->data[$Model->name][$field['name']])) {
				continue;
			}

			if (!empty($Model->data[$Model->name][$field['name']]) && is_array($Model->data[$Model->name][$field['name']])) {

				if ($Model->data[$Model->name][$field['name']]['size'] == 0) {
					unset($Model->data[$Model->name][$field['name']]);
					continue;
				}

				// 拡張子を取得
				$field['ext'] = decodeContent($Model->data[$Model->name][$field['name']]['type'], $Model->data[$Model->name][$field['name']]['name']);

				/* タイプ別除外 */
				if ($field['type'] == 'image') {
					if (!in_array($field['ext'], $this->imgExts)) {
						unset($Model->data[$Model->name][$field['name']]);
						continue;
					}
				} else {
					if (is_array($field['type'])) {
						if (!in_array($field['ext'], $field['type'])) {
							unset($Model->data[$Model->name][$field['name']]);
							continue;
						}
					} else {
						if ($field['type'] != 'all' && $field['type'] != $field['ext']) {
							unset($Model->data[$Model->name][$field['name']]);
							continue;
						}
					}
				}

				if (empty($Model->data[$Model->name][$field['name']]['name'])) {

					/* フィールドに値がない場合はスキップ */
					unset($Model->data[$Model->name][$field['name']]);
					continue;
				} else {

					/* アップロードしたファイルを保存する */
					// ファイル名が重複していた場合は変更する
					$Model->data[$Model->name][$field['name']]['name'] = $this->getUniqueFileName($Model, $field['name'], $Model->data[$Model->name][$field['name']]['name'], $field);

					// 画像を保存
					$fileName = $this->saveFile($Model, $field);
					if ($fileName) {

						if (!$this->tmpId && ($field['type'] == 'all' || $field['type'] == 'image') && !empty($field['imagecopy']) && in_array($field['ext'], $this->imgExts)) {

							/* 画像をコピーする */
							foreach ($field['imagecopy'] as $copy) {
								// コピー画像が元画像より大きい場合はスキップして作成しない
								$size = $this->getImageSize($this->savePath[$Model->alias] . $fileName);
								if ($size && $size['width'] < $copy['width'] && $size['height'] < $copy['height']) {
									if (isset($copy['smallskip']) && $copy['smallskip'] === false) {
										$copy['width'] = $size['width'];
										$copy['height'] = $copy['height'];
									} else {
										continue;
									}
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

						// ファイルをリサイズ
						if (!$this->tmpId && !empty($field['imageresize']) && in_array($field['ext'], $this->imgExts)) {
							if (!empty($field['imageresize']['thumb'])) {
								$thumb = $field['imageresize']['thumb'];
							} else {
								$thumb = false;
							}
							$filePath = $this->savePath[$Model->alias] . $fileName;
							$this->resizeImage($filePath, $filePath, $field['imageresize']['width'], $field['imageresize']['height'], $thumb);
						}

						// 一時ファイルを削除
						@unlink($Model->data[$Model->name][$field['name']]['tmp_name']);
						// フィールドの値をファイル名に更新
						if (!$this->tmpId) {
							$Model->data[$Model->name][$field['name']] = $fileName;
						} else {
							$Model->data[$Model->name][$field['name']]['session_key'] = $fileName;
						}
					} else {
						// 失敗したら処理を中断してfalseを返す
						return false;
					}
				}
			}
		}

		return true;
	}

/**
 * セッションに保存されたファイルデータをファイルとして保存する
 * 
 * @param Model $Model
 * @param string $fieldName
 * @return void
 * @access public
 */
	public function moveFileSessionToTmp(Model $Model, $fieldName) {
		$sessionKey = $Model->data[$Model->alias][$fieldName . '_tmp'];
		$tmpName = $this->savePath[$Model->alias] . $sessionKey;
		$fileData = $this->Session->read('Upload.' . $sessionKey);
		$fileType = $this->Session->read('Upload.' . $sessionKey . '_type');
		$this->Session->delete('Upload.' . $sessionKey);
		$this->Session->delete('Upload.' . $sessionKey . '_type');

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
		$pos = strpos($sessionKey, '_');
		$fileName = substr($sessionKey, $pos + 1, strlen($sessionKey));

		// アップロードされたデータとしてデータを復元する
		$uploadInfo['error'] = 0;
		$uploadInfo['name'] = $fileName;
		$uploadInfo['tmp_name'] = $tmpName;
		$uploadInfo['size'] = $fileSize;
		$uploadInfo['type'] = $fileType;
		$Model->data[$Model->alias][$fieldName] = $uploadInfo;
		unset($Model->data[$Model->alias][$fieldName . '_tmp']);
	}

/**
 * ファイルを保存する
 * 
 * @param Model $Model
 * @param array 画像保存対象フィールドの設定
 * @return ファイル名 Or false
 * @access public
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

		if (!$this->tmpId) {
			$fileName = $prefix . $basename . $suffix . '.' . $field['ext'];
		} else {
			if (!empty($field['namefield'])) {
				$Model->data[$Model->alias][$field['namefield']] = $this->tmpId;
				$fileName = $this->getFieldBasename($Model, $field, $field['ext']);
			} else {
				$fileName = $this->tmpId . '_' . $field['name'] . '.' . $field['ext'];
			}
		}
		$filePath = $this->savePath[$Model->alias] . $fileName;

		if (!$this->tmpId) {
			if (copy($file['tmp_name'], $filePath)) {
				chmod($filePath, 0666);
				$ret = $fileName;
			} else {
				$ret = false;
			}
		} else {
			$_fileName = str_replace('.', '_', $fileName);
			$this->Session->write('Upload.' . $_fileName, $field);
			$this->Session->write('Upload.' . $_fileName . '.type', $file['type']);
			$this->Session->write('Upload.' . $_fileName . '.data', file_get_contents($file['tmp_name']));
			return $fileName;
		}

		return $ret;
	}

/**
 * 画像をコピーする
 * 
 * @param Model $Model
 * @param array 画像保存対象フィールドの設定
 * @return boolean
 * @access public
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
 * @param string コピー元のパス
 * @param string コピー先のパス
 * @param int 横幅
 * @param int 高さ
 * @return boolean
 * @access public
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
 * @param string $path
 * @return mixed array / false
 * @access public
 */
	public function getImageSize($path) {
		$imginfo = getimagesize($path);
		if ($imginfo) {
			return array('width' => $imginfo[0], 'height' => $imginfo[1]);
		}
		return false;
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
	public function beforeDelete(Model $Model, $cascade = true) {
		$Model->data = $Model->findById($Model->id);
		$this->delFiles($Model);
	}

/**
 * 画像ファイル群を削除する
 * 
 * @param Model $Model
 * @return boolean
 * @access public
 */
	public function delFiles(Model $Model, $fieldName = null) {
		foreach ($this->settings[$Model->alias]['fields'] as $key => $field) {
			if (empty($field['name'])) {
				$field['name'] = $key;
			}
			$file = $Model->data[$Model->name][$field['name']];
			$ret = $this->delFile($Model, $file, $field);
		}
	}

/**
 * ファイルを削除する
 * 
 * @param Model $Model
 * @param array 保存対象フィールドの設定
 * @return boolean
 * @access public
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
 * ファイル名をフィールド値ベースのファイル名に変更する
 * 
 * @param Model $Model
 * @return boolean
 * @access public
 */
	public function renameToFieldBasename(Model $Model, $copy = false) {
		foreach ($this->settings[$Model->alias]['fields'] as $key => $setting) {

			if (empty($setting['name'])) {
				$setting['name'] = $key;
			}

			if (!empty($setting['namefield']) && !empty($Model->data[$Model->alias][$setting['name']])) {

				$oldName = $Model->data[$Model->alias][$setting['name']];

				if (file_exists($this->savePath[$Model->alias] . $oldName)) {

					$pathinfo = pathinfo($oldName);
					$newName = $this->getFieldBasename($Model, $setting, $pathinfo['extension']);

					if (!$newName) {
						return true;
					}
					if ($oldName != $newName) {

						if (!empty($setting['imageresize'])) {
							$newName = $this->getFileName($Model, $setting['imageresize'], $newName);
						} else {
							$newName = $this->getFileName($Model, null, $newName);
						}

						if (!$copy) {
							rename($this->savePath[$Model->alias] . $oldName, $this->savePath[$Model->alias] . $newName);
						} else {
							copy($this->savePath[$Model->alias] . $oldName, $this->savePath[$Model->alias] . $newName);
						}

						$Model->data[$Model->alias][$setting['name']] = str_replace(DS, '/', $newName);

						if (!empty($setting['imagecopy'])) {
							foreach ($setting['imagecopy'] as $copysetting) {
								$oldCopyname = $this->getFileName($Model, $copysetting, $oldName);
								if (file_exists($this->savePath[$Model->alias] . $oldCopyname)) {
									$newCopyname = $this->getFileName($Model, $copysetting, $newName);
									if (!$copy) {
										rename($this->savePath[$Model->alias] . $oldCopyname, $this->savePath[$Model->alias] . $newCopyname);
									} else {
										copy($this->savePath[$Model->alias] . $oldCopyname, $this->savePath[$Model->alias] . $newCopyname);
									}
								}
							}
						}
					}
				} else {
					$Model->data[$Model->alias][$setting['name']] = '';
				}
			}
		}
		return true;
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
			$subdir = date($this->settings[$Model->alias]['subdirDateFormat']);
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
 * @access public
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
 * @access public
 */
	public function getBasename(Model $Model, $setting, $filename) {
		$pattern = "/^" . $prefix . "(.*?)" . $suffix . "\.[a-zA-Z0-9]*$/is";
		if (preg_match($pattern, $filename, $maches)) {
			return $maches[1];
		} else {
			return '';
		}
	}

/**
 * 一意のファイル名を取得する
 * 
 * @param string $fieldName
 * @param string $fileName
 * @return string
 * @access public
 */
	public function getUniqueFileName(Model $Model, $fieldName, $fileName, $setting = null) {
		$pathinfo = pathinfo($fileName);
		$basename = preg_replace("/\." . $pathinfo['extension'] . "$/is", '', $fileName);

		$ext = $setting['ext'];

		// 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
		$conditions[$Model->name . '.' . $fieldName . ' LIKE'] = $basename . '%' . $ext;
		if (!empty($Model->data[$Model->name]['id'])) {
			$conditions[$Model->name . '.id <>'] = $Model->data[$Model->name]['id'];
		}
		$datas = $Model->find('all', array('conditions' => $conditions, 'fields' => array($fieldName)));

		if ($datas) {
			$prefixNo = 1;
			foreach ($datas as $data) {
				$_basename = preg_replace("/\." . $ext . "$/is", '', $data[$Model->name][$fieldName]);
				$lastPrefix = str_replace($basename, '', $_basename);
				if (preg_match("/^__([0-9]+)$/s", $lastPrefix, $matches)) {
					$no = (int)$matches[1];
					if ($no > $prefixNo) {
						$prefixNo = $no;
					}
				}
			}
			return $basename . '__' . ($prefixNo + 1) . '.' . $ext;
		} else {
			return $basename . '.' . $ext;
		}
	}

}
