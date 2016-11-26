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
$config['BcApp.adminNavi'] = [
	'Plugin' => [
		'menus' => [
			'Mail' => ['title' => 'メール設定', 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_configs', 'action' => 'form']],
		]
	]
];
$MailContent = ClassRegistry::init('Mail.MailContent');
$mailContents = $MailContent->find('all', ['recursive' => 0]);
foreach ($mailContents as $mailContent) {
	$mail = $mailContent['MailContent'];
	$content = $mailContent['Content'];
	$config['BcApp.adminNavi.Contents.' . 'MailContent' . $mail['id']] = [
		'siteId' => $content['site_id'],
		'title' => $content['title'],
		'type' => 'mail-content',
		'menus' => [
			'MailMessages' . $mail['id'] => ['title' => '受信メール一覧', 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_messages', 'action' => 'index', $mail['id']]],
			'MailFields' . $mail['id'] => ['title' => 'フィールド', 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $mail['id']]],
			'MailContents' . $mail['id'] => ['title' => '設定', 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'edit', $mail['id']]],
			'Mail' . $mail['id'] => ['title' => '公開ページ', 'url' => $content['url']],
		]
	];
}
$config['BcContents']['items']['Mail'] = [
	'MailContent'	=> [
		'title' => 'メールフォーム',
		'multiple'	=> true,
		'preview'	=> true,
		'icon'	=> 'admin/icon_mail.png',
		'routes' => [
			'manage'	=> [
				'admin' => true,
				'plugin'	=> 'mail',
				'controller'=> 'mail_fields',
				'action'	=> 'index'
			],
			'add'	=> [
				'admin' => true,
				'plugin'	=> 'mail',
				'controller'=> 'mail_contents',
				'action'	=> 'ajax_add'
			],
			'edit'	=> [
				'admin' => true,
				'plugin'	=> 'mail',
				'controller'=> 'mail_contents',
				'action'	=> 'edit'
			],
			'delete' => [
				'admin' => true,
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
				'admin' => true,
				'plugin'	=> 'mail',
				'controller'=> 'mail_contents',
				'action'	=> 'ajax_copy'
			]
		]
	]	
];

/**
 * ショートコード
 */
$config['BcShortCode']['Mail'] = [
	'Mail.getForm'	
];