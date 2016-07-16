<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.Config
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

$config['BcContents']['items']['SinglePage'] = [
	'SinglePage'	=> [
		'title'     => 'シングルページ',
        'preview'   => true,
		'icon'	=> 'admin/icon_single_page.png',
		'routes' => [
			'edit' => [
				'plugin'	=> 'single_page',
				'controller'=> 'single_page_configs',
				'action'	=> 'edit'
			],
			'delete' => [
				'plugin'	=> 'single_page',
				'controller'=> 'single_page_configs',
				'action'	=> 'delete'
			],
			'view' => [
				'plugin'	=> 'single_page',
				'controller'=> 'single_page',
				'action'	=> 'view'
			]
		]
	]
];
