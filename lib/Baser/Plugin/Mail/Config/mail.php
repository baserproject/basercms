<?php
/* SVN FILE: $Id$ */
/**
 * メール設定
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * システムナビ
 */
	$config['BcApp.adminNavi.mail'] = array(
			'name'		=> 'メールプラグイン',
			'contents'	=> array(
				array('name' => 'メールフォーム一覧',		'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'index')),
				array('name' => 'メールフォーム登録',		'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'add')),
				array('name' => 'メールフォーム基本設定',	'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_configs', 'action' => 'form')),
		)
	);
	$MailContent = ClassRegistry::init('Mail.MailContent');
	$mailContents = $MailContent->find('all', array('recursive' => -1));
	foreach($mailContents as $mailContent) {
		$mailContent = $mailContent['MailContent'];
		$config['BcApp.adminNavi.mail']['contents'] = array_merge($config['BcApp.adminNavi.mail']['contents'], array(
			array('name' => '['.$mailContent['title'].'] 公開ページ',		'url' => '/'.$mailContent['name'].'/index'),
			array('name' => '['.$mailContent['title'].'] フィールド一覧',	'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $mailContent['id'])),
			array('name' => '['.$mailContent['title'].'] フィールド登録',	'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'add', $mailContent['id'])),
			array('name' => '['.$mailContent['title'].'] 受信メール一覧',	'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_messages', 'action' => 'index', $mailContent['id'])),
			array('name' => '['.$mailContent['title'].'] 設定',			'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'edit', $mailContent['id'])),
		));
	}

