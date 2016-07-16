<?php

/**
 * メールインストーラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * データベース初期化
 */
	$this->Plugin->initDb('plugin', 'Mail', array('dbDataPattern'	=> $dbDataPattern));

/**
 * メッセージテーブル構築
 */
	App::uses('Message', 'Mail.Model');
	$Message = new Message();
	$Message->reconstructionAll();

/**
 * 必要フォルダ初期化
 */
	$filesPath = WWW_ROOT.'files';
	$savePath = $filesPath.DS.'mail';
	$limitedPath = $savePath . DS . 'limited';
	
	if(is_writable($filesPath) && !is_dir($savePath)){
		mkdir($savePath);
	}
	if(!is_writable($savePath)){
		chmod($savePath, 0777);
	}
	if(is_writable($savePath) && !is_dir($limitedPath)){
		mkdir($limitedPath);
	}
	if(!is_writable($limitedPath)){
		chmod($limitedPath, 0777);
	}
	if(is_writable($limitedPath)){
		$File = new File($limitedPath . DS . '.htaccess');
		$htaccess = "Order allow,deny\nDeny from all";
		$File->write($htaccess);
		$File->close();
	}