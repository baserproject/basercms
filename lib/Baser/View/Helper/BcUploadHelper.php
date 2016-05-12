<?php
/**
 * アップロードヘルパー
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * Include files
 */
App::uses('BcAppHelper', 'View/Helper');

/**
 * アップロードヘルパー
 *
 * @package Baser.View.Helper
 */
class BcUploadHelper extends BcAppHelper {

/**
 * ヘルパ
 * 
 * @var array
 */
	public $helpers = array('Html', 'BcForm');
	
/**
 * ファイルインプットボックス出力
 * 
 * 画像の場合は画像タグ、その他の場合はファイルへのリンク
 * そして削除用のチェックボックスを表示する
 * 
 * 《オプション》
 * imgsize	画像のサイズを指定する
 * rel		A タグの rel 属性を指定
 * title	A タグの title 属性を指定
 * link		大きいサイズへの画像へのリンク有無
 * delCheck	削除用チェックボックスの利用可否
 * force	ファイルの存在有無に関わらず強制的に画像タグを表示するかどうか
 * 
 * @param string $fieldName
 * @param array $options
 * @return string
 * @deprecated since version 3.0.6
 */
	public function file($fieldName, $options = array()) {
		trigger_error(deprecatedMessage('メソッド：BcUploadHelper::file()', '3.0.6', '3.1.0', 'BcFormHelper::file() を利用してください。'), E_USER_DEPRECATED);
		return $this->BcForm->file($fieldName, $options);
	}
	
/**
 * ファイルへのリンクを取得する
 *
 * @param string $fieldName
 * @param array $options
 * @return string
 */
	public function fileLink($fieldName, $options = array()) {

		$options = array_merge(array(
			'imgsize' => 'medium', // 画像サイズ
			'rel' => '', // rel属性
			'title' => '', // タイトル属性
			'link' => true, // 大きいサイズの画像へのリンク有無
			'force' => false,
			'width' => '', // 横幅
			'height' => '', // 高さ
			), $options);

		extract($options);

		if(strpos($fieldName, '.') === false) {
			throw new BcException('BcUploadHelper を利用するには、$fieldName に、モデル名とフィールド名をドットで区切って指定する必要があります。');
		}
		$this->setEntity($fieldName);
		$field = $this->field();

		$tmp = false;
		$Model = ClassRegistry::init($this->model());

		try{
			$settings = $this->getBcUploadSetting();
		} catch (BcException $e){
			throw $e ;
		}

		$basePath = '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';

		if (empty($options['value'])) {
			$value = $this->value($fieldName);
		} else {
			$value = $options['value'];
		}

		if (is_array($value)) {
			if (empty($value['session_key']) && empty($value['name'])) {
				$data = $Model->findById($Model->id);
				if (!empty($data[$Model->alias][$field])) {
					$value = $data[$Model->alias][$field];
				} else {
					$value = '';
				}
			} else {
				if (isset($value['session_key'])) {
					$tmp = true;
					$value = str_replace('/', '_', $value['session_key']);
					$basePath = '/uploads/tmp/';
				} else {
					return false;
				}
			}
		}

		/* ファイルのパスを取得 */
		/* 画像の場合はサイズを指定する */
		if (isset($settings['saveDir'])) {
			if ($value && !is_array($value)) {
				$uploadSettings = $settings['fields'][$field];
				$ext = decodeContent('', $value);
				if ($uploadSettings['type'] == 'image' || in_array($ext, $Model->Behaviors->BcUpload->imgExts)) {
					$options = array(
						'imgsize' => $imgsize, 
						'rel' => $rel, 
						'title' => $title, 
						'link' => $link, 
						'force' => $force,
						'width' => $width, // 横幅
						'height' => $height // 高さ
					);
					if ($tmp) {
						$options['tmp'] = true;
					}
					$fileLinkTag = $this->uploadImage($fieldName, $value, $options) . '<br /><span class="file-name">' . mb_basename($value) . '</span>';
				} else {
					$filePath = $basePath . $value;
					$fileLinkTag = $this->Html->link('ダウンロード ≫', $filePath, array('target' => '_blank')) . '<br /><span class="file-name">' . mb_basename($value) . '</span>';
				}
			} else {
				$fileLinkTag = $value;
			}
		} else {
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
	public function uploadImage($fieldName, $fileName, $options = array()) {

		$options = array_merge(array(
			'imgsize' => 'medium', // 画像サイズ
			'link' => true, // 大きいサイズの画像へのリンク有無
			'escape' => false, // エスケープ
			'mobile' => false, // モバイル
			'alt' => '', // alt属性
			'width' => '', // 横幅
			'height' => '', // 高さ
			'noimage' => '', // 画像がなかった場合に表示する画像
			'tmp' => false,
			'force' => false,
			'output' => '', // 出力タイプ tag ,url を指定、未指定(or false)の場合は、tagで出力(互換性のため)
			), $options);

		extract($options);

		unset($options['imgsize']);
		unset($options['link']);
		unset($options['escape']);
		unset($options['mobile']);
		unset($options['alt']);
		unset($options['width']);
		unset($options['height']);
		unset($options['noimage']);
		unset($options['tmp']);
		unset($options['force']);
		unset($options['output']);

		$imgOptions = array(
			'alt' => $alt,
			'width' => $width,
			'height' => $height
		);

		if ($imgOptions['width'] === '') {
			unset($imgOptions['width']);
		}
		if ($imgOptions['height'] === '') {
			unset($imgOptions['height']);
		}

		$linkOptions = array(
			'rel' => 'colorbox',
			'escape' => $escape
		);

		if (is_array($fileName)) {
			if (isset($fileName['session_key'])) {
				$fileName = $fileName['session_key'];
				$tmp = true;
			} else {
				return '';
			}
		}

		if ($noimage) {
			if (!$fileName) {
				$fileName = $noimage;
			}
		} else {
			if (!$fileName) {
				return '';
			}
		}

		if (strpos($fieldName, '.') === false) {
			trigger_error('フィールド名は、 ModelName.field_name で指定してください。', E_USER_WARNING);
			return false;
		}

		$this->setEntity($fieldName);
		$field = $this->field();

		try{
			$settings = $this->getBcUploadSetting();
		} catch (BcException $e){
			throw $e ;
		}

		$fileUrl = $this->getBasePath($settings);
		$filePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS;

		if (isset($settings['fields'][$field]['imagecopy'])) {
			$copySettings = $settings['fields'][$field]['imagecopy'];
		} else {
			$copySettings = "";
		}

		if ($tmp) {
			$link = false;
			$fileUrl = '/uploads/tmp/';
			if ($imgsize) {
				$fileUrl .= $imgsize . '/';
			}
		}

		if ($fileName == $noimage) {
			$mostSizeUrl = $fileName;
		} elseif ($tmp) {
			$mostSizeUrl = $fileUrl . $fileName;
		} else {
			$check = false;
			$maxSizeExists = false;
			$mostSizeExists = false;

			if ($copySettings) {

				foreach ($copySettings as $key => $copySetting) {

					if ($key == $imgsize) {
						$check = true;
					}

					if (isset($copySetting['mobile'])) {
						if ($copySetting['mobile'] != $mobile) {
							continue;
						}
					} else {
						if ($mobile != preg_match('/^mobile_/', $key)) {
							continue;
						}
					}

					$imgPrefix = '';
					$imgSuffix = '';

					if (isset($copySetting['suffix'])) {
						$imgSuffix = $copySetting['suffix'];
					}
					if (isset($copySetting['prefix'])) {
						$imgPrefix = $copySetting['prefix'];
					}
					
					$pathinfo = pathinfo($fileName);
					$ext = $pathinfo['extension'];
					$basename = basename($fileName, '.' . $ext);

					$subdir = str_replace($basename . '.' . $ext, '', $fileName);
					if (file_exists($filePath . str_replace('/', DS, $subdir) . $imgPrefix . $basename . $imgSuffix . '.' . $ext) || $force) {
						if ($check && !$mostSizeExists) {
							$mostSizeUrl = $fileUrl . $subdir . $imgPrefix . $basename . $imgSuffix . '.' . $ext . '?' . rand();
							$mostSizeExists = true;
						} elseif (!$mostSizeExists && !$maxSizeExists) {
							$maxSizeUrl = $fileUrl . $subdir . $imgPrefix . $basename . $imgSuffix . '.' . $ext . '?' . rand();
							$maxSizeExists = true;
						}
					}
				}
			}

			if (!isset($mostSizeUrl)) {
				$mostSizeUrl = $fileUrl . $fileName . '?' . rand();
			}
			if (!isset($maxSizeUrl)) {
				$maxSizeUrl = $fileUrl . $fileName . '?' . rand();
			}
		}

		switch($output){
			case 'url' :
				return $mostSizeUrl;
			case 'tag' :
				return $this->Html->image($mostSizeUrl, am($options, $imgOptions));
			default :
				if ($link && !($noimage == $fileName)) {
					return $this->Html->link($this->Html->image($mostSizeUrl, $imgOptions), $maxSizeUrl, am($options, $linkOptions));
				} else {
					return $this->Html->image($mostSizeUrl, am($options, $imgOptions));
				}
		}
	}

/**
 * アップロード先のベースパスを取得
 *
 * @param string $fieldName 格納されているDBのフィールド名、ex) BlogPost.eye_catch
 * @return string パス
 */
	public function getBasePath($settings = null) {
		if(! $settings){
			try{
				$settings = $this->getBcUploadSetting();
			} catch (BcException $e){
				throw $e ;
			}
		}
		return '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';
	}

/**
 * アップロードの設定を取得する
 *
 * @param string $modelName
 * @return array
 */
	protected function getBcUploadSetting(){
		$modelName = $this->model();
		$Model = ClassRegistry::init($modelName);
		if (empty($Model->Behaviors->BcUpload)) {
			throw new BcException('BcUploadHelper を利用するには、モデルで BcUploadBehavior の利用設定が必要です。');
		}
		return $Model->Behaviors->BcUpload->settings[$modelName];
	}
}
