<?php
/* SVN FILE: $Id$ */
/**
 * アップロードコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * アップロードコントローラー
 * @package baser.controllers
 */
class UploadsController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Uploads';
/**
 * モデル
 * @var array
 * @access public
 */
	var $uses = array();
/**
 * セッションに保存した一時ファイルを出力する
 * @param string $name
 * @return void
 * @access public
 */
	function tmp() {

		$size = '';
		$args = func_get_args();
		if(func_num_args() > 1) {
			$size = $args[0];
			$name = $args[1];
		} else {
			$name = $args[0];
		}
		$sessioName = str_replace('.','_',$name);
		$sessionData = $this->Session->read('Upload.'.$sessioName);

		Configure::write('debug',0);
		$type = $sessionData['type'];
		$ext = decodeContent($type,$name);
		if(!$ext) {
			$this->notFound();
		}

		$fileInfo = array();
		if(isset($sessionData['imagecopy'][$size])) {
			$fileInfo = $sessionData['imagecopy'][$size];
		} elseif(isset($sessionData['imageresize'])) {
			$fileInfo = $sessionData['imageresize'];
		} else {
			$size = '';
		}

		if(!$size) {
			$data = $this->Session->read('Upload.'.$sessioName.'.data');
		} else {

			if(is_dir(TMP.'uploads')) {
				mkdir(TMP.'uploads');
				chmod(TMP.'uploads',0777);
			}

			$path = TMP.'uploads'.DS.$name;
			$file = new File($path, true);
			$file->write($this->Session->read('Upload.'.$sessioName.'.data'), 'wb');
			$file->close();

			$thumb = false;

			if(!empty($fileInfo['thumb'])){
				$thumb = $fileInfo['thumb'];
			}
			App::import('Vendor','Imageresizer');
			$imageresizer = new Imageresizer(APP.'tmp');
			$imageresizer->resize($path, $path, $fileInfo['width'], $fileInfo['height'], $thumb);
			$data = file_get_contents($path);
			unlink($path);

		}

		if($ext != 'gif' && $ext != 'jpg' && $ext != 'png') {
			Header("Content-disposition: attachment; filename=".$name);
		}
		Header("Content-type: ".$type."; name=".$name);
		echo $data;
		$this->Session->delete('Upload.'.$sessioName);
		exit();

	}
	
}
