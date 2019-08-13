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
$config['BcApp.adminNavigation'] = [
	'Plugins' => [
		'menus' => [
			'BlogTags' => ['title' => 'ブログタグ設定', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index']],
		]
]];
$BlogContent = ClassRegistry::init('Blog.BlogContent');
$blogContents = $BlogContent->find('all', ['recursive' => 0]);
foreach ($blogContents as $blogContent) {
	$blog = $blogContent['BlogContent'];
	$content = $blogContent['Content'];
	$config['BcApp.adminNavigation.Contents.' . 'BlogContent' . $blog['id']] = [
		'siteId' => $content['site_id'],
		'title' => $content['title'],
		'type' => 'blog-content',
		'icon' => 'bca-icon--blog',
		'menus' => [
			'BlogPosts' . $blog['id'] => [
				'title' => '記事',
				'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', $blog['id']],
				'currentRegex' => '/\/blog\/blog_posts\/.+?\/' . $blog['id'] . '/s'
			],
			'BlogCategories' . $blog['id'] => [
				'title' => 'カテゴリ',
				'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'index', $blog['id']],
				'currentRegex' => '/\/blog\/blog_categories\/.+?\/' . $blog['id'] . '/s'
			],
			'BlogComments' . $blog['id'] => [
				'title' => 'コメント',
				'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_comments', 'action' => 'index', $blog['id']]
			],
			'BlogContentsEdit' . $blog['id'] => [
				'title' => '設定',
				'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'edit', $blog['id']]
			]
		]
	];
}
// @deprecated 5.0.0 since 4.2.0 BcApp.adminNavigation の形式に変更
$config['BcApp.adminNavi.blog'] = [
	'name' => __d('baser', 'ブログプラグイン'),
	'contents' => [
		['name' =>  __d('baser', 'タグ一覧'), 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index']],
		['name' =>  __d('baser', 'タグ登録'), 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'add']],
	]
];

$config['BcContents']['items']['Blog'] = [
	'BlogContent'	=> [
		'title' => __d('baser', 'ブログ'),
		'multiple'	=> true,
		'preview'	=> true,
		'icon'	=> 'bca-icon--blog',
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
