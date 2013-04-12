<?php
/* SVN FILE: $Id$ */
/**
 * ファイルアップロードビヘイビア
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models.behaviors
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
include BASER_VENDORS.'imageresizer.php';
/**
 * ファイルアップロードビヘイビア
 * 
 * 《設定例》
 * public $actsAs = array(
 *	'BcUpload' => array(
 * 		'saveDir'	=> "editor",
 * 		'fields'	=> array(
 * 			'image'	=> array(
 * 				'type'			=> 'image',
 * 				'namefield'		=> 'id',
 * 				'nameadd'		=> false,
 *				'subdirDateFormat'	=> 'Y/m'	// Or false
 * 				'imageresize'	=> array('prefix' => 'template', 'width' => '100', 'height' => '100'),
 *				'imagecopy'		=> array(
 *					'thumb'			=> array('suffix' => 'template', 'width' => '150', 'height' => '150'),
 *					'thumb_mobile'	=> array('suffix' => 'template', 'width' => '100', 'height' => '100')
 *				)
 * 			),
 * 			'pdf' => array(
 * 				'type'			=> 'pdf',
 * 				'namefield'		=> 'id',
 * 				'nameformat'	=> '%d',
 * 				'nameadd'		=> false
 * 			)
 * 		)
 * 	)
 * );
 * 
 * @subpackage baser.models.behaviors
 */
class BcUploadBehavior extends ModelBehavior {
/**
 * 保存ディレクトリ
 * 
 * @var string
 * @access public
 */
	var $savePath = '';
/**
 * 設定
 * 
 * @var array
 * @access public
 */
	var $settings =  null;
/**
 * 一時ID
 * 
 * @var string
 * @access public
 */
	var $tmpId = null;
/**
 * Session
 * 
 * @var Session
 * @access public
 */
	var $Session = null;
/**
 * 画像拡張子
 * 
 * @var array 
 */
	var $imgExts = array('gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png');
/**
 * セットアップ
 * 
 * @param Model	$model
 * @param array	actsAsの設定
 * @return void
 * @access public
 */
	function setup($model, $config = array()) {

		$this->settings = Set::merge(array(
			'saveDir'	=> '',
			'fields'	=> array()
		), $config);
		if($this->settings['saveDir']) {
			$this->savePath = WWW_ROOT . 'files'.DS.$this->settings['saveDir'] . DS;
		} else {
			$this->savePath = WWW_ROOT . 'files'.DS;
		}
		
		if(!is_dir($this->savePath)) {
			$Folder = new Folder();
			
			$Folder->create($this->savePath);
			$Folder->chmod($this->savePath, 0777, true);
		}
		App::import('Component', 'Session');
		$this->Session = new SessionComponent();

	}
/**
 * Before save
 * 
 * @param Model $model
 * @param Model $options
 * @return boolean
 * @access public
 */
	function beforeSave($model, $options) {
		
		return $this->saveFiles($model);
		
	}
/**
 * After save
 * 
 * @param Model $model
 * @param Model $created
 * @param Model $options
 * @return boolean
 * @access public
 */
	function afterSave($model, $created, $options) {
		
		$this->renameToFieldBasename($model);
		$model->data = $model->save($model->data, array('callbacks'=>false,'validate'=>false));
		
	}
/**
 * 一時ファイルとして保存する
 * 
 * @param Model $model
 * @param array $data
 * @param string $tmpId
 * @return boolean
 * @access public
 */
	function saveTmpFiles($model,$data,$tmpId) {
		
		$this->Session->delete('Upload');
		$model->data = $data;
		$this->tmpId = $tmpId;
		if($this->saveFiles($model)) {
			return $model->data;
		}else {
			return false;
		}
		
	}
/**
 * ファイル群を保存する
 * 
 * @param Model $model
 * @return boolean
 * @access public
 */
	function saveFiles($model) {

		$serverData = $model->findById($model->id);

		foreach($this->settings['fields'] as $key => $field) {

			if(empty($field['name'])) $field['name'] = $key;

			if(!empty($model->data[$model->name][$field['name'].'_delete'])) {
				$file = $serverData[$model->name][$field['name']];
				if(!$this->tmpId) {
					$this->delFile($model,$file,$field);
					$model->data[$model->name][$field['name']] = '';
				}else {
					$model->data[$model->name][$field['name']] = $file;
				}
				continue;
			}

			if(empty($model->data[$model->name][$field['name']]['name']) && !empty($model->data[$model->name][$field['name'].'_'])) {
				// 新しいデータが送信されず、既存データを引き継ぐ場合は、元のフィールド名に戻す
				$model->data[$model->name][$field['name']] = $model->data[$model->name][$field['name'].'_'];
				unset($model->data[$model->name][$field['name'].'_']);
			}elseif(!empty($model->data[$model->name][$field['name'].'_tmp'])) {
				// セッションに一時ファイルが保存されている場合は復元する
				$this->moveFileSessionToTmp($model,$field['name']);
			}elseif(!isset($model->data[$model->name][$field['name']]) ||
					!is_array($model->data[$model->name][$field['name']])) {
				continue;
			}

			if(!empty($model->data[$model->name][$field['name']]) && is_array($model->data[$model->name][$field['name']])) {

				if($model->data[$model->name][$field['name']]['size'] == 0) {
					unset($model->data[$model->name][$field['name']]);
					continue;
				}

				// 拡張子を取得
				$field['ext'] = decodeContent($model->data[$model->name][$field['name']]['type'],$model->data[$model->name][$field['name']]['name']);

				/* タイプ別除外 */
				if($field['type'] == 'image') {
					if(!in_array($field['ext'], $this->imgExts)) {
						unset($model->data[$model->name][$field['name']]);
						continue;
					}
				}else {
					if(is_array($field['type'])) {
						if(!in_array($field['ext'], $field['type'])) {
							unset($model->data[$model->name][$field['name']]);
							continue;
						}
					}else {
						if($field['type'] != 'all' && $field['type']!=$field['ext']) {
							unset($model->data[$model->name][$field['name']]);
							continue;
						}
					}
				}

				if(empty($model->data[$model->name][$field['name']]['name'])) {

					/* フィールドに値がない場合はスキップ */
					unset($model->data[$model->name][$field['name']]);
					continue;

				}else {

					/* アップロードしたファイルを保存する */
					// ファイル名が重複していた場合は変更する
					$model->data[$model->name][$field['name']]['name'] = $this->getUniqueFileName($model,$field['name'],$model->data[$model->name][$field['name']]['name'],$field);

					// 画像を保存
					$fileName = $this->saveFile($model,$field);
					if($fileName) {

						if(!$this->tmpId && ($field['type']=='all' || $field['type']=='image') && !empty($field['imagecopy']) && in_array($field['ext'],$this->imgExts)) {

							/* 画像をコピーする */
							foreach($field['imagecopy'] as $copy) {
								// コピー画像が元画像より大きい場合はスキップして作成しない
								$size = $this->getImageSize($this->savePath . $fileName);
								if($size && $size['width'] < $copy['width'] && $size['height'] < $copy['height']) {
									if(isset($copy['smallskip']) && $copy['smallskip']===false) {
										$copy['width'] = $size['width'];
										$copy['height'] = $copy['height'];
									}else {
										continue;
									}
								}
								$copy['name'] = $field['name'];
								$copy['ext'] = $field['ext'];
								$ret = $this->copyImage($model,$copy);
								if(!$ret) {
									// 失敗したら処理を中断してfalseを返す
									return false;
								}
							}

						}

						// 一時ファイルを削除
						@unlink($model->data[$model->name][$field['name']]['tmp_name']);
						// フィールドの値をファイル名に更新
						if(!$this->tmpId) {
							$model->data[$model->name][$field['name']] = $fileName;
						}else {
							$model->data[$model->name][$field['name']]['session_key'] = $fileName;
						}
					}else {
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
 * @param Model $model
 * @param string $fieldName
 * @return void
 * @access public
 */
	function moveFileSessionToTmp($model,$fieldName) {

		$sessionKey = $model->data[$model->alias][$fieldName.'_tmp'];
		$tmpName = $this->savePath.$sessionKey;
		$fileData = $this->Session->read('Upload.'.$sessionKey);
		$fileType = $this->Session->read('Upload.'.$sessionKey.'_type');
		$this->Session->delete('Upload.'.$sessionKey);
		$this->Session->delete('Upload.'.$sessionKey.'_type');

		// サイズを取得
		if (ini_get('mbstring.func_overload') & 2 && function_exists('mb_strlen')) {
			$fileSize = mb_strlen($fileData, 'ASCII');
		} else {
			$fileSize = strlen($fileData);
		}

		if($fileSize == 0) {
			return false;
		}

		// ファイルを一時ファイルとして保存
		$file = new File($tmpName,true,0666);
		$file->write($fileData);
		$file->close();

		// 元の名前を取得
		$pos = strpos($sessionKey,'_');
		$fileName = substr($sessionKey,$pos+1,strlen($sessionKey));

		// アップロードされたデータとしてデータを復元する
		$uploadInfo['error'] = 0;
		$uploadInfo['name'] = $fileName;
		$uploadInfo['tmp_name'] = $tmpName;
		$uploadInfo['size'] = $fileSize;
		$uploadInfo['type'] = $fileType;
		$model->data[$model->alias][$fieldName] = $uploadInfo;
		unset($model->data[$model->alias][$fieldName.'_tmp']);

	}
/**
 * ファイルを保存する
 * 
 * @param Model $model
 * @param array 画像保存対象フィールドの設定
 * @return ファイル名 Or false
 * @access public
 */
	function saveFile($model,$field) {

		// データを取得
		$file = $model->data[$model->name][$field['name']];

		if (empty($file['tmp_name'])) return false;
		if (!empty($file['error']) && $file['error']!=0) return false;

		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if(!empty($field['prefix'])) $prefix = $field['prefix'];
		if(!empty($field['suffix'])) $suffix = $field['suffix'];

		// 保存ファイル名を生成
		$basename = preg_replace("/\.".$field['ext']."$/is",'',$file['name']);

		if(!$this->tmpId) {
			$fileName = $prefix . $basename . $suffix . '.'.$field['ext'];
		}else {
			if(!empty($field['namefield'])) {
				$model->data[$model->alias][$field['namefield']] = $this->tmpId;
				$fileName = $this->getFieldBasename($model, $field, $field['ext']);
			} else {
				$fileName = $this->tmpId.'_'.$field['name'].'.'.$field['ext'];
			}
		}
		$filePath = $this->savePath . $fileName;

		if(!$this->tmpId) {

			if(copy($file['tmp_name'], $filePath)) {

				chmod($filePath,0666);
				// ファイルをリサイズ
				if(!empty($field['imageresize']) && in_array($field['ext'], $this->imgExts)) {
					if(!empty($field['imageresize']['thumb'])) {
						$thumb = $field['imageresize']['thumb'];
					}else {
						$thumb = false;
					}
					$this->resizeImage($filePath,$filePath,$field['imageresize']['width'],$field['imageresize']['height'],$thumb);
				}
				$ret = $fileName;

			}else {
				$ret =  false;
			}

		}else {
			$_fileName = str_replace('.','_',$fileName);
			$this->Session->write('Upload.'.$_fileName, $field);
			$this->Session->write('Upload.'.$_fileName.'.type', $file['type']);
			$this->Session->write('Upload.'.$_fileName.'.data', file_get_contents($file['tmp_name']));
			return $fileName;
		}

		return $ret;

	}
/**
 * 画像をコピーする
 * 
 * @param Model $model
 * @param array 画像保存対象フィールドの設定
 * @return boolean
 * @access public
 */
	function copyImage($model,$field) {

		// データを取得
		$file = $model->data[$model->name][$field['name']];

		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if(!empty($field['prefix'])) $prefix = $field['prefix'];
		if(!empty($field['suffix'])) $suffix = $field['suffix'];

		// 保存ファイル名を生成
		$basename = preg_replace("/\.".$field['ext']."$/is",'',$file['name']);
		$fileName = $prefix . $basename . $suffix . '.'.$field['ext'];
		$filePath = $this->savePath . $fileName;

		if(!empty($field['thumb'])) {
			$thumb = $field['thumb'];
		}else {
			$thumb = false;
		}

		return $this->resizeImage($model->data[$model->name][$field['name']]['tmp_name'],$filePath,$field['width'],$field['height'], $thumb);

	}
/**
 * 画像ファイルをコピーする
 * リサイズ可能
 * 
 * @param Model	$model
 * @param string コピー元のパス
 * @param string コピー先のパス
 * @param int 横幅
 * @param int 高さ
 * @return boolean
 * @access public
 */
	function resizeImage($source,$distination,$width=0,$height=0,$thumb = false) {

		if($width>0 || $height>0) {
			$imageresizer = new Imageresizer();
			$ret = $imageresizer->resize($source,$distination,$width,$height, $thumb);
		}else {
			$ret = copy($source,$distination);
		}

		if($ret) {
			chmod($distination,0666);
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
	function getImageSize($path) {
		
		$imginfo = getimagesize($path);
		if($imginfo) {
			return array('width' => $imginfo[0], 'height' => $imginfo[1]);
		}
		return false;
		
	}
/**
 * After delete
 * 画像ファイルの削除を行う
 * 削除に失敗してもデータの削除は行う
 * 
 * @param Model $model
 * @return void
 * @access public
 */
	function beforeDelete($model) {

		$model->data = $model->findById($model->id);
		$this->delFiles($model);

	}
/**
 * 画像ファイル群を削除する
 * 
 * @param Model $model
 * @return boolean
 * @access public
 */
	function delFiles($model,$fieldName = null) {

		foreach($this->settings['fields'] as $key => $field) {
			if(empty($field['name'])) $field['name'] = $key;
			$file = $model->data[$model->name][$field['name']];
			$ret = $this->delFile($model,$file,$field);
		}

	}
/**
 * ファイルを削除する
 * 
 * @param Model $model
 * @param array 保存対象フィールドの設定
 * @return boolean
 * @access public
 */
	function delFile($model,$file,$field,$delImagecopy=true) {

		if(!$file) {
			return true;
		}

		if(empty($field['ext'])) {
			$pathinfo = pathinfo($file);
			$field['ext'] = $pathinfo['extension'];
		}

		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if(!empty($field['prefix'])) $prefix = $field['prefix'];
		if(!empty($field['suffix'])) $suffix = $field['suffix'];

		// 保存ファイル名を生成
		$basename = preg_replace("/\.".$field['ext']."$/is",'',$file);
		$fileName = $prefix . $basename . $suffix . '.'.$field['ext'];
		$filePath = $this->savePath . $fileName;

		if(!empty($field['imagecopy']) && $delImagecopy) {
			foreach($field['imagecopy'] as $copy) {
				$copy['name'] = $field['name'];
				$copy['ext'] = $field['ext'];
				$this->delFile($model,$file,$copy,false);
			}
		}

		if(file_exists($filePath)) {
			return unlink($filePath);
		}

		return true;

	}
/**
 * ファイル名をフィールド値ベースのファイル名に変更する

 * @param Model $model
 * @return boolean
 * @access public
 */
	function renameToFieldBasename($model, $copy = false) {

		foreach($this->settings['fields'] as $key => $setting) {

			if(empty($setting['name'])) $setting['name'] = $key;

			if(!empty($setting['namefield']) && !empty($model->data[$model->alias][$setting['name']])) {

				$oldName = $model->data[$model->alias][$setting['name']];

				if(file_exists($this->savePath.$oldName)) {

					$pathinfo = pathinfo($oldName);
					$newName = $this->getFieldBasename($model,$setting,$pathinfo['extension']);
					
					if(!$newName) {
						return true;
					}
					if($oldName != $newName) {
						
						if(!empty($setting['imageresize'])) {
							$newName = $this->getFileName($model, $setting['imageresize'], $newName);
						} else {
							$newName = $this->getFileName($model, null, $newName);
						}
						
						if(!$copy) {
							rename($this->savePath.$oldName,$this->savePath.$newName);
						} else {
							copy($this->savePath.$oldName,$this->savePath.$newName);
						}
						
						$model->data[$model->alias][$setting['name']] = str_replace(DS, '/', $newName);
						
						if(!empty($setting['imagecopy'])) {
							foreach($setting['imagecopy'] as $copysetting) {
								$oldCopyname = $this->getFileName($model,$copysetting,$oldName);
								if(file_exists($this->savePath.$oldCopyname)) {
									$newCopyname = $this->getFileName($model,$copysetting,$newName);
									if(!$copy) {
										rename($this->savePath.$oldCopyname,$this->savePath.$newCopyname);
									} else {
										copy($this->savePath.$oldCopyname,$this->savePath.$newCopyname);
									}
								}
							}
						}
						
					}
				}else {
					$model->data[$model->alias][$setting['name']] = '';
				}
			}
		}
		return true;
		
	}
/**
 * フィールドベースのファイル名を取得する
 *
 * @param Model $model
 * @param array $setting
 * @param string $ext
 * @return mixed false / string
 * @access public
 */
	function getFieldBasename($model,$setting,$ext) {

		if(empty($setting['namefield'])) {
			return false;
		}
		$data = $model->data[$model->alias];
		if(!isset($data[$setting['namefield']])){
			if($setting['namefield'] == 'id' && $model->id) {
			$basename = $model->id;
			} else {
				return false;
			}
		} else {
			$basename = $data[$setting['namefield']];
		}

		if(!empty($setting['nameformat'])) {
			$basename = sprintf($setting['nameformat'],$basename);
		}
		
		if(!isset($setting['nameadd']) || $setting['nameadd'] !== false) {
			$basename .= '_' . $setting['name'];
		}
		
		$subdir = '';
		if(!empty($this->settings['subdirDateFormat'])) {
			$subdir = date($this->settings['subdirDateFormat']);
			if(!preg_match('/\/$/', $subdir)) {
				$subdir .= '/';
			}
			$subdir = str_replace('/', DS, $subdir);
			$path = $this->savePath . $subdir;
			if(!is_dir($path)) {
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
 * @param Model $model
 * @param array $setting
 * @param string $filename
 * @return string
 * @access public
 */
	function getFileName($model,$setting,$filename) {

		if(empty($setting)) {
			return $filename;
		}
		$pathinfo = pathinfo($filename);
		$ext = $pathinfo['extension'];
		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if(!empty($setting['prefix'])) $prefix = $setting['prefix'];
		if(!empty($setting['suffix'])) $suffix = $setting['suffix'];

		$basename = preg_replace("/\.".$ext."$/is",'',$filename);
		return $prefix . $basename . $suffix . '.' . $ext;

	}
/**
 * ファイル名からベースファイル名を取得する
 * 
 * @param Model $model
 * @param array $setting
 * @param string $filename
 * @return string
 * @access public
 */
	function getBasename($model,$setting,$filename) {
		
		$pattern = "/^".$prefix."(.*?)".$suffix."\.[a-zA-Z0-9]*$/is";
		if(preg_match($pattern, $filename,$maches)) {
			return $maches[1];
		}else {
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
	function getUniqueFileName($model, $fieldName, $fileName, $setting = null) {

		$pathinfo = pathinfo($fileName);
		$basename = preg_replace("/\.".$pathinfo['extension']."$/is",'',$fileName);

		$ext = $setting['ext'];
		// 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
		$conditions[$model->name.'.'.$fieldName.' LIKE'] = $basename.'%'.$ext;
		if(!empty($model->data[$model->name]['id'])) {
			$conditions[$model->name.'.id <>'] = $model->data[$model->name]['id'];
		}
		$datas = $model->find('all', array('conditions' => $conditions, 'fields' => array($fieldName)));

		if($datas) {
			$prefixNo = 1;
			foreach($datas as $data) {
				$_basename = preg_replace("/\.".$ext."$/is",'',$data[$model->name][$fieldName]);
				$lastPrefix = str_replace($basename,'',$_basename);
				if(preg_match("/^__([0-9]+)$/s",$lastPrefix,$matches)) {
					$no = (int)$matches[1];
					if($no > $prefixNo) $prefixNo = $no;
				}

			}
			return $basename.'__'.($prefixNo+1).'.'.$ext;

		}else {
			return $basename.'.'.$ext;
		}

	}
	
}
