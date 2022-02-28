<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('Imageresizer', 'Vendor');

/**
 * Class UploadsController
 *
 * アップロードコントローラー
 *
 * @package Baser.Controller
 */
class UploadsController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Uploads';

	/**
	 * モデル
	 * @var array
	 */
	public $uses = [];

	/**
	 * セッションに保存した一時ファイルを出力する
	 * @param string $name
	 * @return void
	 */
	public function tmp()
	{
		$this->output(func_get_args(), func_num_args());
	}

	public function smartphone_tmp()
	{
		$this->output(func_get_args(), func_num_args());
	}

	protected function output($args, $funcNum)
	{
		$size = '';
		if ($funcNum > 1) {
			$size = $args[0];
			$name = $args[1];
		} else {
			$name = $args[0];
		}
		$sessioName = str_replace(['.', '/'], ['_', '_'], $name);
		$sessionData = $this->Session->read('Upload.' . $sessioName);

		Configure::write('debug', 0);
		$type = $sessionData['type'];
		$ext = decodeContent($type, $name);
		if (!$ext) {
			$this->notFound();
		}

		$fileInfo = [];
		if (isset($sessionData['imagecopy'][$size])) {
			$fileInfo = $sessionData['imagecopy'][$size];
		} elseif (!empty($sessionData['imageresize'])) {
			$fileInfo = $sessionData['imageresize'];
		} else {
			$size = '';
		}

		if (!$size) {
			$data = base64_decode($this->Session->read('Upload.' . $sessioName . '.data'));
		} else {

			if (is_dir(TMP . 'uploads')) {
				mkdir(TMP . 'uploads');
				chmod(TMP . 'uploads', 0777);
			}

			$path = TMP . 'uploads' . DS . $name;
			$file = new File($path, true);
			$file->write(base64_decode($this->Session->read('Upload.' . $sessioName . '.data')), 'wb');
			$file->close();

			$thumb = false;

			if (!empty($fileInfo['thumb'])) {
				$thumb = $fileInfo['thumb'];
			}
			$imageresizer = new Imageresizer(APP . 'tmp');
			$imageresizer->resize($path, $path, $fileInfo['width'], $fileInfo['height'], $thumb);
			$data = file_get_contents($path);
			unlink($path);
		}
		if ($ext !== 'gif' && $ext !== 'jpg' && $ext !== 'png') {
			Header("Content-disposition: attachment; filename=" . $name);
		}
		Header("Content-type: " . $type . "; name=" . $name);
		echo $data;
		exit();
	}
}
