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

$config['BcContents']['items']['MultiBlog'] = [
	'MultiBlogContent'	=> [
		'multiple'	=> true,
		'preview'	=> true,
		'title' => 'マルチブログ',
		'icon'	=> 'admin/icon_multi_blog.png',
		'routes' => [
			'manage' => [
				'admin' => true,
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_posts',
				'action'	=> 'index'
			],
			'add'	=> [
				'admin' => true,
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_contents',
				'action'	=> 'add'
			],
			'edit'	=> [
				'admin' => true,
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_contents',
				'action'	=> 'edit'
			],
			'delete' => [
				'admin' => true,
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
				'admin' => true,
				'plugin'	=> 'multi_blog',
				'controller'=> 'multi_blog_contents',
				'action'	=> 'copy'
			]
		]
	]
];
