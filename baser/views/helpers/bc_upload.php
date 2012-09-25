<?php
/* SVN FILE: $Id$ */
/**
 * アップロードヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Helper', 'Form');
/**
 * アップロードヘルパー
 *
 * @package baser.views.helpers
 */
class BcUploadHelper extends FormHelper {
/**
 * ファイルインプットボックス出力
 * 画像の場合は画像タグ、その他の場合はファイルへのリンク
 * そして削除用のチェックボックスを表示する
 * [カスタムオプション]
 * imgsize・・・画像のサイズを指定する
 *
 * @param string $fieldName
 * @param array $options
 * @return string
 * @access public
 */
	function file($fieldName, $options = array()) {

		$linkOptions = $_options = array('imgsize'=>'midium','rel'=>'','title'=>'', 'link' => true);
		
		if(isset($options['link'])) {
			$linkOptions['link'] = $options['link'];
			unset($options['link']);
		}
		if(isset($options['imgsize'])) {
			$linkOptions['imgsize'] = $options['imgsize'];
			unset($options['imgsize']);
		}
		if(isset($options['rel'])) {
			$linkOptions['rel'] = $options['rel'];
			unset($options['rel']);
		}
		if(isset($options['title'])) {
			$linkOptions['title'] = $options['title'];
			unset($options['title']);
		}
		
		$options = $this->_initInputField($fieldName, Set::merge($_options,$options));
		
		$view =& ClassRegistry::getObject('view');
		$_field = $view->entity();
		$modelName = $_field[0];
		$field = $_field[1];

		if (ClassRegistry::isKeySet($modelName)) {
			$model =& ClassRegistry::getObject($modelName);
		}else {
			return;
		}

		$fileLinkTag = $this->fileLink($fieldName, $linkOptions);
		$fileTag = parent::file($fieldName,$options);
		$delCheckTag = parent::checkbox($modelName.'.'.$field.'_delete').parent::label($modelName.'.'.$field.'_delete','削除する');
		$hiddenValue = $this->value($fieldName.'_');
		$fileValue = $this->value($fieldName);
		if(is_array($fileValue) && empty($fileValue['tmp_name']) && $hiddenValue) {
			$hiddenTag = parent::hidden($modelName.'.'.$field.'_',array('value'=>$hiddenValue));
		}else {
			$hiddenTag = parent::hidden($modelName.'.'.$field.'_',array('value'=>$this->value($fieldName)));
		}
		$out = $fileTag;
		if($fileLinkTag) {
			$out .= '&nbsp;'.$delCheckTag.$hiddenTag.'<br />'.$fileLinkTag;
		}

		return '<div class="upload-file">'.$out.'</div>';

	}
/**
 * ファイルへのリンクを取得する
 *
 * @param string $fieldName
 * @param array $options
 * @return string
 */
	function fileLink($fieldName, $options = array()) {

		$_options = array('imgsize' => 'midium', 'rel' => '', 'title' => '', 'link' => true);
		
		$link = $imgsize = $rel = $title = '';
		if(isset($options['link'])) {
			$link = $options['link'];
			unset($options['link']);
		}
		if(isset($options['imgsize'])) {
			$imgsize= $options['imgsize'];
			unset($options['imgsize']);
		}
		if(isset($options['rel'])) {
			$rel = $options['rel'];
			unset($options['rel']);
		}
		if(isset($options['title'])) {
			$title = $options['title'];
			unset($options['title']);
		}
		
		$options = $this->_initInputField($fieldName, Set::merge($_options,$options));
		$view =& ClassRegistry::getObject('view');
		$tmp = false;
		$_field = $view->entity();
		$modelName = $_field[0];
		$field = $_field[1];
		if (ClassRegistry::isKeySet($modelName)) {
			$model =& ClassRegistry::getObject($modelName);
		}else {
			return;
		}
		
		$basePath = $this->base.DS.'files'.DS.$model->actsAs['BcUpload']['saveDir'].DS;

		if(empty($options['value'])) {
			$value = $this->value($fieldName);
		}else {
			$value = $options['value'];
		}

		if(is_array($value)) {
			if(empty($value['session_key']) && empty($value['name'])) {
				$data = $model->findById($model->id);
				if(!empty($data[$model->alias][$field])) {
					$value = $data[$model->alias][$field];
				}else {
					$value = '';
				}
			}else {
				if(isset($value['session_key'])) {
					$tmp = true;
					$value = $value['session_key'];
					$basePath = $this->base.DS.'uploads'.DS.'tmp'.DS;
				}else{
					return false;
				}
			}
		}

		/* ファイルのパスを取得 */
		/* 画像の場合はサイズを指定する */
		if(isset($model->actsAs['BcUpload']['saveDir'])) {
			if($value && !is_array($value)) {
				$uploadSettings = $model->actsAs['BcUpload']['fields'][$field];
				if($uploadSettings['type']=='image') {
					$options = array('imgsize' => $imgsize, 'rel' => $rel, 'title' => $title, 'link' => $link);
					if($tmp) {
						$options['tmp'] = true;
					}
					$fileLinkTag = $this->uploadImage($fieldName, $value, $options).'<br /><span class="file-name">'.$value.'</span>';
				}else {
					$filePath = $basePath.$value;
					$fileLinkTag = $this->Html->link('ダウンロード ≫',$filePath).'<br /><span class="file-name">'.$value.'</span>';
				}
			}else {
				$fileLinkTag = $value;
			}
		}else {
			return false;
		}
		return $fileLinkTag;

	}
/**
 * アップロードした画像のタグをリンク付きで出力する
 * Uploadビヘイビアの設定による
 * 上から順に大きい画像を並べている事が前提で
 * 指定したサイズ内で最大の画像を出力
 * リンク先は存在する最大の画像へのリンクとなる
 *
 * @param string $fieldName
 * @param string $fileName
 * @param array $options
 * @return string
 */
	function uploadImage($fieldName, $fileName, $options = array()) {

		$_options = array('imgsize'=>'midium', 'escape'=>false, 'link'=>true, 'mobile'=>false);
		$options = Set::merge($_options,$options);
		$imgOptions = array();
		$linkOptions = array();
		$noimage = false;
		$link = true;
		$tmp = false;

		if(is_array($fileName)) {
			if(isset($fileName['session_key'])) {
				$fileName = $fileName['session_key'];
				$tmp = true;
			} else {
				return '';
			}
		}

		$imgsize = $options['imgsize'];
		if(isset($options['alt'])) {
			$imgOptions['alt'] = $options['alt'];
			unset($options['alt']);
		}
		if(isset($options['width'])) {
			$imgOptions['width'] = $options['width'];
			unset($options['width']);
		}
		if(isset($options['height'])) {
			$imgOptions['height'] = $options['height'];
			unset($options['height']);
		}
		unset($options['imgsize']);
		if(isset($options['noimage'])) {
			if(!$fileName) {
				$fileName = $options['noimage'];
				$noimage = true;
			}
			unset($options['noimage']);
		} else {
			if(!$fileName) {
				return '';
			}
		}
		if(isset($options['link'])) {
			$link = $options['link'];
			unset($options['link']);
		}
		if(isset($options['escape'])) {
			$linkOptions['escape'] = $options['escape'];
			unset($options['escape']);
		}
		if(isset($options['tmp'])) {
			$tmp = true;
			unset($options['tmp']);
		}
		$mobile = $options['mobile'];

		$_field = split('\.',$fieldName);
		$modelName = $_field[0];
		$field = $_field[1];

		if (ClassRegistry::isKeySet($modelName)) {
			$model =& ClassRegistry::getObject($modelName);
		}else {
			return;
		}

		$fileUrl = '/files/'.$model->actsAs['BcUpload']['saveDir'].'/';
		$filePath = WWW_ROOT.'files'.DS.$model->actsAs['BcUpload']['saveDir'].DS;
		$copySettings = $model->actsAs['BcUpload']['fields'][$field]['imagecopy'];

		if($tmp) {
			$link = false;
			$fileUrl = $this->base.'/uploads/tmp/';
			if($imgsize) {
				$fileUrl .= $imgsize.'/';
			}
		}

		if($noimage) {
			$mostSizeUrl = $fileName;
		} elseif($tmp) {
			$mostSizeUrl = $fileUrl.$fileName;
		} else {
			$check = false;
			$maxSizeExists = false;
			$mostSizeExists = false;

			foreach($copySettings as $key => $copySetting) {

				if($key == $imgsize) {
					$check = true;
				}
				if(isset($copySetting['mobile'])) {
					if($copySetting['mobile'] != $mobile) {
						continue;
					}
				}else{
					if($mobile != preg_match('/^mobile_/', $key)) {
						continue;
					}
				}
				$imgPrefix = '';
				$imgSuffix = '';
				if(isset($copySetting['suffix']))
					$imgSuffix = $copySetting['suffix'];
				if(isset($copySetting['prefix']))
					$imgPrefix = $copySetting['prefix'];
				$pathinfo = pathinfo($fileName);
				$ext = $pathinfo['extension'];
				$basename = basename($fileName,'.'.$ext);

				if(file_exists($filePath.$imgPrefix.$basename.$imgSuffix.'.'.$ext)) {
					if($check && !$mostSizeExists) {
						$mostSizeUrl = $fileUrl.$imgPrefix.$basename.$imgSuffix.'.'.$ext.'?'.rand();
						$mostSizeExists = true;
					} elseif(!$mostSizeExists && !$maxSizeExists) {
						$maxSizeUrl = $fileUrl.$imgPrefix.$basename.$imgSuffix.'.'.$ext.'?'.rand();
						$maxSizeExists = true;
					}
				}

			}

			if(!isset($mostSizeUrl)) {
				$mostSizeUrl = $fileUrl.$fileName.'?'.rand();
			}
			if(!isset($maxSizeUrl)) {
				$maxSizeUrl = $fileUrl.$fileName.'?'.rand();
			}

		}

		if($link && !$noimage) {
			return $this->Html->link($this->Html->image($mostSizeUrl,$imgOptions),$maxSizeUrl,am($options, $linkOptions));
		}else {
			return $this->Html->image($mostSizeUrl,am($options, $imgOptions));
		}

	}

}