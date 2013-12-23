<?php

/* SVN FILE: $Id$ */
/**
 * BlogBaserヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */

/**
 * BlogBaserヘルパー
 *
 * @package baser.plugins.blog.views.helpers
 *
 */
class BlogBaserHelper extends AppHelper {

/**
 * ヘルパー
 * @var array
 */
	public $helpers = array('Blog.Blog');

/**
 * ブログ記事一覧出力
 * ページ編集画面等で利用する事ができる。
 * 利用例: <?php $this->BcBaser->blogPosts('news', 3) ?>
 * ビュー: app/webroot/theme/{テーマ名}/blog/{コンテンツテンプレート名}/posts.php
 *
 * @param int $contentsName
 * @param int $num
 * @param array $options
 * @param mixid $mobile '' / boolean
 * @return void
 * @access public
 */
	public function blogPosts($contentsName, $num = 5, $options = array()) {
		$options = array_merge(array(
			'category' => null,
			'tag' => null,
			'year' => null,
			'month' => null,
			'day' => null,
			'id' => null,
			'keyword' => null,
			'template' => null,
			'direction' => null,
			'page' => null,
			'sort' => null
		), $options);

		$BlogContent = ClassRegistry::init('Blog.BlogContent');
		$id = $BlogContent->field('id', array('BlogContent.name' => $contentsName));
		$url = array('admin' => false, 'plugin' => 'blog', 'controller' => 'blog', 'action' => 'posts');

		$settings = Configure::read('BcAgent');
		foreach ($settings as $key => $setting) {
			if (isset($options[$key])) {
				$agentOn = $options[$key];
				unset($options[$key]);
			} else {
				$agentOn = (Configure::read('BcRequest.agent') == $key);
			}
			if ($agentOn) {
				$url['prefix'] = $setting['prefix'];
				break;
			}
		}

		echo $this->requestAction($url, array('return', 'pass' => array($id, $num), 'named' => $options));
	}

/**
 * カテゴリー別記事一覧ページ判定
 *
 * @return boolean 
 */
	public function isBlogCategory() {
		return $this->Blog->isCategory();
	}

/**
 * タグ別記事一覧ページ判定
 * @return boolean
 */
	public function isBlogTag() {
		return $this->Blog->isTag();
	}

/**
 * 日別記事一覧ページ判定
 * @return boolean
 */
	public function isBlogDate() {
		return $this->Blog->isDate();
	}

/**
 * 月別記事一覧ページ判定
 * @return boolean 
 */
	public function isBlogMonth() {
		return $this->Blog->isMonth();
	}

/**
 * 年別記事一覧ページ判定
 * @return boolean
 */
	public function isBlogYear() {
		return $this->Blog->isYear();
	}

/**
 * 個別ページ判定
 * @return boolean
 */
	public function isBlogSingle() {
		return $this->Blog->isSingle();
	}

/**
 * インデックスページ判定
 * @return boolean
 */
	public function isBlogHome() {
		return $this->Blog->isHome();
	}

}
