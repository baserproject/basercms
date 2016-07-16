<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.Config
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */

$config['BcContents']['items']['MultiBlog'] = [
	'MultiBlogContent'	=> [
		'multiple'	=> true,
		'preview'	=> true,
		'title' => 'マルチブログ',
		'icon'	=> 'admin/icon_multi_blog.png',
		'routes' => [
			'manage' => [
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_posts',
				'action'	=> 'index'
			],
			'add'	=> [
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_contents',
				'action'	=> 'add'
			],
			'edit'	=> [
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_contents',
				'action'	=> 'edit'
			],
			'delete' => [
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_contents',
				'action'	=> 'delete'
			],
			'view' => [
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog',
				'action'	=> 'index'
			],
			'copy'	=> [
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_contents',
				'action'	=> 'copy'
			]
		]
	]
];
