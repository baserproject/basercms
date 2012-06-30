<?php
/* SVN FILE: $Id$ */
/**
 * BlogBaserヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
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

		$_options = array(
			'category'	=> null,
			'tag'		=> null,
			'year'		=> null,
			'month'		=> null,
			'day'		=> null,
			'id'		=> null,
			'keyword'	=> null,
			'template'	=> null
		);
		$options = am($_options, $options);

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

		echo $this->requestAction($url, array('return', 'pass' => array($id, $num, $templates), 'named' => $options));

	}
/**
 * ブログのトップページ判定
 * @return boolean 
 */
	function isBlogHome() {
		if(empty($this->params['plugin']) || empty($this->params['controller']) || empty($this->params['action'])) {
			return false;
		}
		if($this->params['plugin'] == 'blog' && $this->params['controller'] == 'blog' && $this->params['action'] == 'index') {
			return true;
		}
		return false;
	}
}
?>