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
$config['BcApp.adminNavi'] = [
	'Plugin' => [
		'menus' => [
			'Blog' => ['title' => 'ブログ設定', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index']],
		]
	]
];

$BlogContent = ClassRegistry::init('Blog.BlogContent');
$blogContents = $BlogContent->find('all', ['recursive' => 0]);
foreach ($blogContents as $blogContent) {
	$blog = $blogContent['BlogContent'];
	$content = $blogContent['Content'];
	$config['BcApp.adminNavi.Contents.' . 'BlogContent' . $blog['id']] = [
		'siteId' => $content['site_id'],
		'title' => $content['title'],
		'type' => 'blog-content',
		'menus' => [
			'BlogPostsAdd' . $blog['id'] => ['title' => '記事登録', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'add', $blog['id']]],
			'BlogPosts' . $blog['id'] => ['title' => '記事一覧', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', $blog['id']]],
			'BlogCategories' . $blog['id'] => ['title' => 'カテゴリ一覧', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'index', $blog['id']]],
			'BlogComments' . $blog['id'] => ['title' => 'コメント一覧', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_comments', 'action' => 'index', $blog['id']]],
			'BlogContentsEdit' . $blog['id'] => ['title' => '設定', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'edit', $blog['id']]],
			'Blog' . $blog['id'] => ['title' => '公開ページ', 'url' => $content['url']],
		]
	];
}
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