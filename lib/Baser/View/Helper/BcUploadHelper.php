<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcAppHelper', 'View/Helper');

/**
 * アップロードヘルパー
 *
 * @package Baser.View.Helper
 * @property HtmlHelper $Html
 */
class BcUploadHelper extends BcAppHelper
{

	/**
	 * ヘルパ
	 *
	 * @var array
	 */
	public $helpers = ['Html', 'BcForm'];

	/**
	 * ファイルへのリンクを取得する
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @return string
	 */
	public function fileLink($fieldName, $options = [])
	{
		$options = array_merge([
			'imgsize' => 'medium', // 画像サイズ
			'rel' => '', // rel属性
			'title' => '', // タイトル属性
			'link' => true, // 大きいサイズの画像へのリンク有無
			'force' => false,
			'width' => '', // 横幅
			'height' => '', // 高さ
			'figure' => null,
			'img' => ['class' => ''],
			'figcaption' => null
		], $options);

		if (strpos($fieldName, '.') === false) {
			throw new BcException(__d('baser', 'BcUploadHelper を利用するには、$fieldName に、モデル名とフィールド名をドットで区切って指定する必要があります。'));
		}
		$this->setEntity($fieldName);
		$field = $this->field();

		$tmp = false;
		$Model = ClassRegistry::init($this->model());

		try {
			$settings = $this->getBcUploadSetting();
		} catch (BcException $e) {
			throw $e;
		}

		// EVENT BcUpload.beforeFileLInk
		$event = $this->dispatchEvent('beforeFileLink', [
			'formId' => $this->__id,
			'settings' => $settings,
			'fieldName' => $fieldName,
			'options' => $options
		], ['class' => 'BcUpload', 'plugin' => '']);
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true)? $event->data['options'] : $event->result;
			$settings = $event->data['settings'];
		}

		$this->setBcUploadSetting($settings);

		$basePath = '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';

		if (empty($options['value'])) {
			$value = $this->value($fieldName);
		} else {
			$value = $options['value'];
		}

		if (is_array($value)) {
			$sessionKey = $this->value($fieldName . '_tmp');
			$oldValue = $this->value($fieldName . '_');
			if (!$sessionKey && empty($value['name']) && $oldValue) {
				$value = $oldValue;
			} else {
				if ($sessionKey) {
					$tmp = true;
					$value = str_replace('/', '_', $sessionKey);
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
				$figureOptions = $figcaptionOptions = [];
				if (!empty($options['figcaption'])) {
					$figcaptionOptions = $options['figcaption'];
				}
				if (!empty($options['figure'])) {
					$figureOptions = $options['figure'];
				}
				if (!empty($figcaptionOptions['class'])) {
					$figcaptionOptions['class'] .= ' file-name';
				} else {
					$figcaptionOptions['class'] = 'file-name';
				}
				if ($uploadSettings['type'] == 'image' || in_array($ext, $Model->Behaviors->BcUpload->BcFileUploader[$Model->alias]->imgExts)) {
					$imgOptions = array_merge([
						'imgsize' => $options['imgsize'],
						'rel' => $options['rel'],
						'title' => $options['title'],
						'link' => $options['link'],
						'force' => $options['force'],
						'width' => $options['width'], // 横幅
						'height' => $options['height'] // 高さ
					], $options['img']);
					if ($tmp) {
						$imgOptions['tmp'] = true;
					}
					$out = $this->Html->tag('figure', $this->uploadImage($fieldName, $value, $imgOptions) . '<br>' . $this->Html->tag('figcaption', mb_basename($value), $figcaptionOptions), $figureOptions);
				} else {
					$filePath = $basePath . $value;
					$linkOptions = ['target' => '_blank'];
					if (is_array($options['link'])) {
						$linkOptions = array_merge($linkOptions, $options['link']);
					}
					$out = $this->Html->tag('figure', $this->Html->link(__d('baser', 'ダウンロード') . ' ≫', $filePath, $linkOptions) . '<br>' . $this->Html->tag('figcaption', mb_basename($value), $figcaptionOptions), $figureOptions);
				}
			} else {
				$out = $value;
			}
		} else {
			$out = false;
		}

		// EVENT BcUpload.afterFileLink
		$event = $this->dispatchEvent('afterFileLink', [
			'data' => $this->request->data,
			'fieldName' => $fieldName,
			'out' => $out
		], ['class' => 'BcUpload', 'plugin' => '']);
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true)? $event->data['out'] : $event->result;
		}

		return $out;
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
	public function uploadImage($fieldName, $fileName, $options = [])
	{
		$options = array_merge([
			'imgsize' => 'medium', // 画像サイズ
			'escape' => false, // エスケープ
			'mobile' => false, // モバイル
			'alt' => '', // alt属性
			'width' => '', // 横幅
			'height' => '', // 高さ
			'noimage' => '', // 画像がなかった場合に表示する画像
			'tmp' => false,
			'force' => false,
			'output' => '', // 出力タイプ tag ,url を指定、未指定(or false)の場合は、tagで出力(互換性のため)
			'limited' => false,  // 公開制限フォルダを利用する場合にフォルダ名を設定する
			'link' => true, // 大きいサイズの画像へのリンク有無
			'img' => null,
			'class' => ''
		], $options);

		$this->setEntity($fieldName);
		$field = $this->field();

		try {
			$settings = $this->getBcUploadSetting();
		} catch (BcException $e) {
			throw $e;
		}

		// EVENT BcUpload.beforeUploadImage
		$event = $this->dispatchEvent('beforeUploadImage', [
			'formId' => $this->__id,
			'settings' => $settings,
			'fieldName' => $fieldName,
			'options' => $options
		], ['class' => 'BcUpload', 'plugin' => '']);
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true)? $event->data['options'] : $event->result;
			$settings = $event->data['settings'];
		}

		$this->setBcUploadSetting($settings);

		$imgOptions = [
			'alt' => $options['alt'],
			'width' => $options['width'],
			'height' => $options['height'],
			'class' => $options['class']
		];
		if (empty($imgOptions['class'])) {
			unset($imgOptions['class']);
		}
		if ($imgOptions['width'] === '') {
			unset($imgOptions['width']);
		}
		if ($imgOptions['height'] === '') {
			unset($imgOptions['height']);
		}
		$linkOptions = [
			'rel' => 'colorbox',
			'escape' => $options['escape']
		];
		if (!empty($options['link']) && is_array($options['link'])) {
			$linkOptions = array_merge($linkOptions, $options['link']);
		}
		if (empty($linkOptions['class'])) {
			unset($linkOptions['class']);
		}

		$sessionKey = $this->value($fieldName . '_tmp');
		if ($sessionKey) {
			$fileName = $sessionKey;
			$options['tmp'] = true;
		}

		if ($options['noimage']) {
			if (!$fileName) {
				$fileName = $options['noimage'];
			}
		} else {
			if (!$fileName) {
				return '';
			}
		}

		if (strpos($fieldName, '.') === false) {
			trigger_error(__d('baser', 'フィールド名は、 ModelName.field_name で指定してください。'), E_USER_WARNING);
			return false;
		}

		$fileUrl = $this->getBasePath($settings);
		$fileUrlInTheme = $this->getBasePath($settings, true);
		$Model = $this->getUploadModel();
		$saveDir = $Model->getSaveDir(false, $options['limited']);
		$saveDirInTheme = $Model->getSaveDir(true, $options['limited']);

		if (isset($settings['fields'][$field]['imagecopy'])) {
			$copySettings = $settings['fields'][$field]['imagecopy'];
		} else {
			$copySettings = "";
		}

		if (!$options['imgsize']) {
			$options['imgsize'] = 'default';
		}
		if ($options['tmp']) {
			$options['link'] = false;
			$fileUrl = '/uploads/tmp/';
			if ($options['imgsize']) {
				$fileUrl .= $options['imgsize'] . '/';
			}
		}

		if ($fileName == $options['noimage']) {
			$mostSizeUrl = $fileName;
		} elseif ($options['tmp']) {
			$mostSizeUrl = $fileUrl . str_replace(['.', '/'], ['_', '_'], $fileName);
		} elseif(is_array($fileName)) {
			return '';
		} else {
			$check = false;
			$maxSizeExists = false;
			$mostSizeExists = false;

			if ($copySettings && ($options['imgsize'] != 'default')) {

				foreach($copySettings as $key => $copySetting) {

					if ($key == $options['imgsize']) {
						$check = true;
					}

					if (isset($copySetting['mobile'])) {
						if ($copySetting['mobile'] != $options['mobile']) {
							continue;
						}
					} else {
						if ($options['mobile'] != preg_match('/^mobile_/', $key) && $options['imgsize'] != 'mobile_thumb') {
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
					$file = str_replace('/', DS, $subdir) . $imgPrefix . $basename . $imgSuffix . '.' . $ext;

					$fileExists = false;
					if (file_exists($saveDir . $file)) {
						$fileExists = true;
					} elseif (file_exists($saveDirInTheme . $file)) {
						$fileExists = true;
						$fileUrl = $fileUrlInTheme;
					}

					if ($fileExists || $options['force']) {
						if ($check && !$mostSizeExists) {
							$mostSizeUrl = $fileUrl . $subdir . $imgPrefix . $basename . $imgSuffix . '.' . $ext . '?' . mt_rand();
							$mostSizeExists = true;
						} elseif (!$mostSizeExists && !$maxSizeExists) {
							$maxSizeUrl = $fileUrl . $subdir . $imgPrefix . $basename . $imgSuffix . '.' . $ext . '?' . mt_rand();
							$maxSizeExists = true;
						}
					}
				}
			}

			if (!isset($mostSizeUrl)) {
				$mostSizeUrl = $fileUrl . $fileName . '?' . mt_rand();
			}
			if (!isset($maxSizeUrl)) {
				$maxSizeUrl = $fileUrl . $fileName . '?' . mt_rand();
			}
		}

		$output = $options['output'];
		$link = $options['link'];
		$noimage = $options['noimage'];
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
		unset($options['class']);

		switch($output) {
			case 'url' :
				$out = $mostSizeUrl;
				break;
			case 'tag' :
				$out = $this->Html->image($mostSizeUrl, array_merge($options, $imgOptions));
				break;
			default :
				if ($link && !($noimage == $fileName)) {
					$out = $this->Html->link($this->Html->image($mostSizeUrl, $imgOptions), $maxSizeUrl, array_merge($options, $linkOptions));
				} else {
					$out = $this->Html->image($mostSizeUrl, array_merge($options, $imgOptions));
				}
		}

		// EVENT BcUpload.afterUploadImage
		$event = $this->dispatchEvent('afterUploadImage', [
			'data' => $this->request->data,
			'fieldName' => $fieldName,
			'out' => $out
		], ['class' => 'BcUpload', 'plugin' => '']);
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true)? $event->data['out'] : $event->result;
		}
		return $out;
	}

	/**
	 * アップロード先のベースパスを取得
	 *
	 * @param string $fieldName 格納されているDBのフィールド名、ex) BlogPost.eye_catch
	 * @param bool $isTheme テーマ内の初期データのパスとするかどうか
	 * @return string パス
	 */
	public function getBasePath($settings = null, $isTheme = false)
	{
		if (!$settings) {
			try {
				$settings = $this->getBcUploadSetting();
			} catch (BcException $e) {
				throw $e;
			}
		}
		$siteConfig = Configure::read('BcSite');
		if (!$isTheme || empty($siteConfig['theme'])) {
			return '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';
		} else {
			$siteConfig = Configure::read('BcSite');
			return '/theme/' . $siteConfig['theme'] . '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';
		}
	}

	/**
	 * アップロードの設定を取得する
	 *
	 * @param string $modelName
	 * @return array
	 */
	protected function getBcUploadSetting()
	{
		$Model = $this->getUploadModel();
		return $Model->getSettings();
	}

	protected function setBcUploadSetting($settings)
	{
		$Model = $this->getUploadModel();
		$Model->setSettings($settings);
	}

	protected function getUploadModel()
	{
		$modelName = $this->model();
		$Model = ClassRegistry::init($modelName);
		if (empty($Model->Behaviors->BcUpload)) {
			throw new BcException(__d('baser', 'BcUploadHelper を利用するには、モデルで BcUploadBehavior の利用設定が必要です。'));
		}
		return $Model;
	}

}
