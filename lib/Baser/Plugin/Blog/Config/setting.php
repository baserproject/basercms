<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * システムナビ
 */
$config['BcApp.adminNavigation'] = [
	'Plugins' => [
		'menus' => [
			'BlogTags' => ['title' => __d('baser', 'ブログタグ設定'), 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index']],
		]
	]];
/* @var BlogContent $BlogContent */
$BlogContent = ClassRegistry::init('Blog.BlogContent');
$blogContents = $BlogContent->find('all', [
	'conditions' => ['Content.deleted' => 0],
	'recursive' => 0,
	'order' =>  'Content.lft',
]);
foreach($blogContents as $blogContent) {
	$blog = $blogContent['BlogContent'];
	$content = $blogContent['Content'];
	$menus = function($blog) {
		$menus = [];
		$route = [
			'admin' => true, 'plugin' => 'blog', 'action' => 'index', $blog['id']
		];
		$menus['BlogPosts' . $blog['id']] = [
			'title' => __d('baser', '記事'),
			'url' => array_merge($route, ['controller' => 'blog_posts']),
			'currentRegex' => '{/blog/blog_posts/[^/]+?/' . $blog['id'] . '($|/)}s'
		];
		$menus['BlogCategories' . $blog['id']] = [
			'title' => __d('baser', 'カテゴリ'),
			'url' => array_merge($route, ['controller' => 'blog_categories']),
			'currentRegex' => '{/blog/blog_categories/[^/]+?/' . $blog['id'] . '($|/)}s'
		];
		if ($blog['tag_use']) {
			$menus['BlogTags' . $blog['id']] = [
				'title' => __d('baser', 'タグ'),
				'url' => array_merge($route, ['controller' => 'blog_tags']),
				'currentRegex' => '{/blog/blog_tags/[^/]+?/}s'
			];
		}
		if ($blog['comment_use']) {
			$menus['BlogComments' . $blog['id']] = [
				'title' => __d('baser', 'コメント'),
				'url' => array_merge($route, ['controller' => 'blog_comments'])
			];
		}
		$menus['BlogContentsEdit' . $blog['id']] = [
			'title' => __d('baser', '設定'),
			'url' => array_merge($route, ['controller' => 'blog_contents', 'action' => 'edit'])
		];
		return $menus;
	};
	$config['BcApp.adminNavigation.Contents.' . 'BlogContent' . $blog['id']] = [
		'siteId' => $content['site_id'],
		'title' => $content['title'],
		'type' => 'blog-content',
		'icon' => 'bca-icon--blog',
		'menus' => $menus($blog)
	];
}
// @deprecated 5.0.0 since 4.2.0 BcApp.adminNavigation の形式に変更
$config['BcApp.adminNavi.blog'] = [
	'name' => __d('baser', 'ブログプラグイン'),
	'contents' => [
		['name' => __d('baser', 'タグ一覧'), 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index']],
		['name' => __d('baser', 'タグ登録'), 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'add']],
	]
];

$config['BcContents']['items']['Blog'] = [
	'BlogContent' => [
		'title' => __d('baser', 'ブログ'),
		'multiple' => true,
		'preview' => true,
		'icon' => 'bca-icon--blog',
		'routes' => [
			'manage' => [
				'admin' => true,
				'plugin' => 'blog',
				'controller' => 'blog_posts',
				'action' => 'index'
			],
			'add' => [
				'admin' => true,
				'plugin' => 'blog',
				'controller' => 'blog_contents',
				'action' => 'ajax_add'
			],
			'edit' => [
				'admin' => true,
				'plugin' => 'blog',
				'controller' => 'blog_contents',
				'action' => 'edit'
			],
			'delete' => [
				'admin' => true,
				'plugin' => 'blog',
				'controller' => 'blog_contents',
				'action' => 'delete'
			],
			'view' => [
				'plugin' => 'blog',
				'controller' => 'blog',
				'action' => 'index'
			],
			'copy' => [
				'admin' => true,
				'plugin' => 'blog',
				'controller' => 'blog_contents',
				'action' => 'ajax_copy'
			],
			'dblclick' => [
				'admin' => true,
				'plugin' => 'blog',
				'controller' => 'blog_posts',
				'action' => 'index'
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
