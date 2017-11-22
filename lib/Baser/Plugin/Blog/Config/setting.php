<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * システムナビ
 */
$config['BcApp.adminNavi.blog'] = [
	'name' => 'ブログプラグイン',
	'contents' => [
		['name' => 'タグ一覧', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index']],
		['name' => 'タグ登録', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'add']],
	]
];
$config['BcContents']['items']['Blog'] = [
	'BlogContent'	=> [
		'title' => 'ブログ',
		'multiple'	=> true,
		'preview'	=> true,
		'icon'	=> 'admin/icon_blog.png',
		'routes' => [
			'manage'	=> [
				'admin' => true,
				'plugin'	=> 'blog',
				'controller'=> 'blog_posts',
				'action'	=> 'index'
			],
			'add'	=> [
				'admin' => true,
				'plugin'	=> 'blog',
				'controller'=> 'blog_contents',
				'action'	=> 'ajax_add'
			],
			'edit'	=> [
				'admin' => true,
				'plugin'	=> 'blog',
				'controller'=> 'blog_contents',
				'action'	=> 'edit'
			],
			'delete' => [
				'admin' => true,
				'plugin'	=> 'blog',
				'controller'=> 'blog_contents',
				'action'	=> 'delete'
			],
			'view' => [
				'plugin'	=> 'blog',
				'controller'=> 'blog',
				'action'	=> 'index'
			],
			'copy'	=> [
				'admin' => true,
				'plugin'	=> 'blog',
				'controller'=> 'blog_contents',
				'action'	=> 'ajax_copy'
			],
			'dblclick'	=> [
				'admin' => true,
				'plugin'	=> 'blog',
				'controller'=> 'blog_posts',
				'action'	=> 'index'
			],
		]
	]
];

$config['Blog'] = [
	// ブログアイキャッチサイズの初期値
	'eye_catch_size_thumb_width' => 600,
	'eye_catch_size_thumb_height' => 600,
	'eye_catch_size_mobile_thumb_width' => 150,
	'eye_catch_size_mobile_thumb_height' => 150,
];
