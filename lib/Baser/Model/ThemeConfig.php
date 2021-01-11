<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('Imageresizer', 'Vendor');

/**
 * Class ThemeConfig
 *
 * テーマ設定モデル
 *
 * @package Baser.Model
 */
class ThemeConfig extends AppModel
{

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * ThemeConfig constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'logo' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
			'main_image_1' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
			'main_image_2' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
			'main_image_3' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
			'main_image_4' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]],
			'main_image_5' => [['rule' => ['fileExt', 'gif,jpg,jpeg,jpe,jfif,png'], 'message' => __d('baser', '許可されていないファイルです。')]]
		];
	}

	/**
	 * 画像を保存する
	 *
	 * @param array $data
	 * @return array
	 */
	public function saveImage($data)
	{
		// TODO インストール時にfilesの書き込み権限チェック＆フォルダ作成

		$saveDir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
		$images = ['logo', 'main_image_1', 'main_image_2', 'main_image_3', 'main_image_4', 'main_image_5'];
		$thumbSuffix = '_thumb';
		$old = $this->findExpanded();

		foreach($images as $image) {
			if (!empty($data['ThemeConfig'][$image]['tmp_name'])) {
				@unlink($saveDir . $old[$image]);
				$pathinfo = pathinfo($old[$image]);
				@unlink($saveDir . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension']);
				$fileName = $data['ThemeConfig'][$image]['name'];
				$ext = pathinfo($fileName, PATHINFO_EXTENSION);
				$filePath = $saveDir . $image . '.' . $ext;
				$thumbPath = $saveDir . $image . $thumbSuffix . '.' . $ext;
				move_uploaded_file($data['ThemeConfig'][$image]['tmp_name'], $filePath);
				$Imageresizer = new Imageresizer();
				$Imageresizer->resize($filePath, $thumbPath, 320, 320);
				$data['ThemeConfig'][$image] = $image . '.' . $ext;
			} else {
				unset($data['ThemeConfig'][$image]);
			}
		}

		return $data;
	}

	/**
	 * 画像を削除する
	 *
	 * @param array $data
	 */
	public function deleteImage($data)
	{
		$saveDir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
		$images = ['logo', 'main_image_1', 'main_image_2', 'main_image_3', 'main_image_4', 'main_image_5'];
		$thumbSuffix = '_thumb';
		$old = $this->findExpanded();
		foreach($images as $image) {
			if (!empty($data['ThemeConfig'][$image . '_delete'])) {
				@unlink($saveDir . $old[$image]);
				$pathinfo = pathinfo($old[$image]);
				@unlink($saveDir . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension']);
				$data['ThemeConfig'][$image] = '';
			}
		}

		return $data;
	}

	/**
	 * テーマカラー設定を保存する
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function updateColorConfig($data)
	{

		$configPath = getViewPath() . 'css' . DS . 'config.css';
		if (!file_exists($configPath)) {
			return false;
		}
		$File = new File($configPath);
		$config = $File->read();
		$settings = [
			'MAIN' => 'color_main',
			'SUB' => 'color_sub',
			'LINK' => 'color_link',
			'HOVER' => 'color_hover'
		];
		$settingExists = false;
		foreach($settings as $key => $setting) {
			if (empty($data['ThemeConfig'][$setting])) {
				$config = preg_replace("/\n.+?" . $key . ".+?\n/", "\n", $config);
			} else {
				$config = str_replace($key, '#' . $data['ThemeConfig'][$setting], $config);
				$settingExists = true;
			}
		}
		$File = new File(WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css', true, 0666);
		$File->write($config);
		$File->close();
		if (!$settingExists) {
			unlink($configPath);
		}
		return true;

	}

}
