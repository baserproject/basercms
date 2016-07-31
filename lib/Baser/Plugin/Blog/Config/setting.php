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
$config['BcApp.adminNavi.blog'] = array(
	'name' => 'ブログプラグイン',
	'contents' => array(
		array('name' => 'タグ一覧', 'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index')),
		array('name' => 'タグ登録', 'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'add')),
	)
);
$config['BcContents']['items']['Blog'] = [
	'BlogContent'	=> [
		'title' => 'ブログ',
		'multiple'	=> true,
		'preview'	=> true,
		'icon'	=> 'admin/icon_blog.png',
		'routes' => [
			'add'	=> [
				'plugin'	=> 'blog',
				'controller'=> 'blog_contents',
				'action'	=> 'ajax_add'
			],
			'edit'	=> [
				'plugin'	=> 'blog',
				'controller'=> 'blog_contents',
				'action'	=> 'edit'
			],
			'delete' => [
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
				'plugin'	=> 'blog',
				'controller'=> 'blog_contents',
				'action'	=> 'ajax_copy'
			]
		]
	]
];