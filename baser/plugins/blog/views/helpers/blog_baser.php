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
	var $helpers = array('Blog.Blog');
/**
 * コンストラクタ 
 */
	function __construct() {
		
		parent::__construct();
		$View = ClassRegistry::getObject('View');
		$helpers = $View->_loadHelpers($View->loaded, $this->helpers);
		$this->Blog = $helpers['Blog'];
		
	}
/**
 * ブログ記事一覧出力
 * ページ編集画面等で利用する事ができる。
 * 利用例: <?php $bcBaser->blogPosts('news', 3) ?>
 * ビュー: app/webroot/themed/{テーマ名}/blog/{コンテンツテンプレート名}/posts.php
 *
 * @param int $contentsName
 * @param int $num
 * @param array $options
 * @param mixid $mobile '' / boolean
 * @return void
 * @access public
 */
	function blogPosts ($contentsName, $num = 5, $options = array()) {

		$options = array_merge(array(
			'category'	=> null,
			'tag'		=> null,
			'year'		=> null,
			'month'		=> null,
			'day'		=> null,
			'id'		=> null,
			'keyword'	=> null,
			'template'	=> null,
			'direction' => null,
			'page'		=> null,
			'sort'		=> null
		), $options);

		$BlogContent = ClassRegistry::init('Blog.BlogContent');
		$id = $BlogContent->field('id', array('BlogContent.name'=>$contentsName));
		$url = array('plugin'=>'blog','controller'=>'blog','action'=>'posts');

		$settings = Configure::read('BcAgent');
		foreach($settings as $key => $setting) {
			if(isset($options[$key])) {
				$agentOn = $options[$key];
				unset($options[$key]);
			} else {
				$agentOn = (Configure::read('BcRequest.agent') == $key);
			}
			if($agentOn){
				$url['prefix'] = $setting['prefix'];
				break;
			}
		}
		if(isset($options['templates'])) {
			$templates = $options['templates'];
		} else {
			$templates = 'posts';
		}
		unset ($options['templates']);
		
		$View =& ClassRegistry::getObject('View');
		ClassRegistry::removeObject('View');
		echo $this->requestAction($url, array('return', 'pass' => array($id, $num), 'named' => $options));
		ClassRegistry::removeObject('View');
	 	ClassRegistry::addObject('View', $View);

	}
/**
 * カテゴリー別記事一覧ページ判定
 * @return boolean
 */
	function isBlogCategory() {
		return $this->Blog->isCategory();
	}
/**
 * タグ別記事一覧ページ判定
 * @return boolean
 */
	function isBlogTag() {
		return $this->Blog->isTag();
	}
/**
 * 日別記事一覧ページ判定
 * @return boolean
 */
	function isBlogDate() {
		return $this->Blog->isDate();
	}
/**
 * 月別記事一覧ページ判定
 * @return boolean 
 */
	function isBlogMonth() {
		return $this->Blog->isMonth();
	}
/**
 * 年別記事一覧ページ判定
 * @return boolean
 */
	function isBlogYear() {
		return $this->Blog->isYear();
	}
/**
 * 個別ページ判定
 * @return boolean
 */
	function isBlogSingle() {
		return $this->Blog->isSingle();
	}
/**
 * インデックスページ判定
 * @return boolean
 */
	function isBlogHome() {
		return $this->Blog->isHome();
	}
	
}