<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * システムナビ
 */
$config['BcApp.adminNavi.mail'] = array(
	'name' => 'メールプラグイン',
	'contents' => array(
		array('name' => 'メールフォーム一覧', 'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'index')),
		array('name' => 'メールフォーム登録', 'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'add')),
		array('name' => 'メールプラグイン基本設定', 'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_configs', 'action' => 'form')),
	)
);
$MailContent = ClassRegistry::init('Mail.MailContent');
$mailContents = $MailContent->find('all', array('recursive' => -1));
foreach ($mailContents as $mailContent) {
	$mailContent = $mailContent['MailContent'];
	$config['BcApp.adminNavi.mail']['contents'] = array_merge($config['BcApp.adminNavi.mail']['contents'], array(
		array('name' => '[' . $mailContent['title'] . '] 公開ページ', 'url' => '/' . $mailContent['name'] . '/index'),
		array('name' => '[' . $mailContent['title'] . '] フィールド一覧', 'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $mailContent['id'])),
		array('name' => '[' . $mailContent['title'] . '] フィールド登録', 'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'add', $mailContent['id'])),
		array('name' => '[' . $mailContent['title'] . '] 受信メール一覧', 'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_messages', 'action' => 'index', $mailContent['id'])),
		array('name' => '[' . $mailContent['title'] . '] 設定', 'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'edit', $mailContent['id'])),
	));
}
$config['BcContents']['items']['Mail'] = [
	'MailContent'	=> [
		'title' => 'メールフォーム',
		'multiple'	=> true,
		'preview'	=> true,
		'icon'	=> 'admin/icon_mail.png',
		'routes' => [
			'add'	=> [
				'plugin'	=> 'mail',
				'controller'=> 'mail_contents',
				'action'	=> 'ajax_add'
			],
			'edit'	=> [
				'plugin'	=> 'mail',
				'controller'=> 'mail_contents',
				'action'	=> 'edit'
			],
			'delete' => [
				'plugin'	=> 'mail',
				'controller'=> 'mail_contents',
				'action'	=> 'delete'
			],
			'view' => [
				'plugin'	=> 'mail',
				'controller'=> 'mail',
				'action'	=> 'index'
			],
			'copy'	=> [
				'plugin'	=> 'mail',
				'controller'=> 'mail_contents',
				'action'	=> 'ajax_copy'
			]
		]
	]	
];