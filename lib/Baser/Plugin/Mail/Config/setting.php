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
		array('name' => '基本設定', 'url' => array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_configs', 'action' => 'form')),
	)
);
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