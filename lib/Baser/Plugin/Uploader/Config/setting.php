<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.Config
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */

 
/**
 * システムナビ
 */
	$config['BcApp.adminNavi.uploader'] = array(
			'name'		=> 'アップローダープラグイン',
			'contents'	=> array(
				array('name' => 'アップロードファイル一覧', 
					'url' => array('admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index')),
				array('name' => 'カテゴリ一覧', 
					'url' => array('admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'index')),
				array('name' => 'カテゴリ新規登録', 
					'url' => array('admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'add')),
				array('name' => '基本設定', 
					'url' => array('admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_configs', 'action' => 'index')),
		),
	);
	$config['Uploader'] = array(
		// システム管理者グループ以外のユーザーがアップロード可能なファイル（拡張子をカンマ区切りで指定する）
		'allowedExt' => 'gif,jpg,png,pdf,zip,doc,docx,xls,xlsx,ppt,pptx'
	);
?>