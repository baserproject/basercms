<?php
/* SVN FILE: $Id$ */
/**
 * ブログ設定
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * システムナビ
 */
	$config['BcApp.adminNavi.blog'] = array(
			'name'		=> 'ブログプラグイン',
			'contents'	=> array(
				array('name' => 'ブログ一覧',		'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'index')),
				array('name' => 'ブログ登録',		'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'add')),
				array('name' => 'タグ一覧',	'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index')),
				array('name' => 'タグ登録',	'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'add')),
		)
	);
	$BlogContent = ClassRegistry::init('Blog.BlogContent');
	$blogContents = $BlogContent->find('all', array('recursive' => -1));
	foreach($blogContents as $blogContent) {
		$blogContent = $blogContent['BlogContent'];
		$config['BcApp.adminNavi.blog']['contents'] = array_merge($config['BcApp.adminNavi.blog']['contents'], array(
			array('name' => '['.$blogContent['title'].'] 公開ページ',		'url' => '/'.$blogContent['name'].'/index'),
			array('name' => '['.$blogContent['title'].'] 記事一覧',		'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', $blogContent['id'])),
			array('name' => '['.$blogContent['title'].'] 記事登録',		'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'add', $blogContent['id'])),
			array('name' => '['.$blogContent['title'].'] カテゴリ一覧',	'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'index', $blogContent['id'])),
			array('name' => '['.$blogContent['title'].'] カテゴリ登録',	'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'add', $blogContent['id'])),
			array('name' => '['.$blogContent['title'].'] コメント一覧',	'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_comments', 'action' => 'index', $blogContent['id'])),
			array('name' => '['.$blogContent['title'].'] 設定',			'url' => array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'edit', $blogContent['id'])),
		));
	}

