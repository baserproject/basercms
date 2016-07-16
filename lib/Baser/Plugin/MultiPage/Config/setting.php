<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiPage.Config
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */

$config['BcContents']['items']['MultiPage'] = [
	'MultiPage'	=> [
		'multiple'	=> true,
		'preview'	=> true,
		'title' => 'マルチページ',
		'icon'	=> 'admin/icon_multi_page.png',
		'routes' => [
			'add'	=> [
				'plugin'	=> 'multi_page',
				'controller'=> 'multi_pages',
				'action'	=> 'add'
			],
			'edit'	=> [
				'plugin'	=> 'multi_page',
				'controller'=> 'multi_pages',
				'action'	=> 'edit'
			],
			'delete' => [
				'plugin'	=> 'multi_page',
				'controller'=> 'multi_pages',
				'action'	=> 'delete'
			],
			'view' => [
				'plugin'	=> 'multi_page',
				'controller'=> 'multi_pages',
				'action'	=> 'view'
			],
			'copy'	=> [
				'plugin'	=> 'multi_page',
				'controller'=> 'multi_pages',
				'action'	=> 'copy'
			]
		]
	]
];
