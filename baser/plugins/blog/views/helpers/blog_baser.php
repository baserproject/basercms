<?php
/* SVN FILE: $Id$ */
/**
 * BlogBaserヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views.helpers
 * @since			Baser v 0.1.0
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
 * 利用例: <?php $baser->blogPosts('news', 3) ?>
 * ビュー: app/webroot/themed/{テーマ名}/blog/{コンテンツテンプレート名}/posts.ctp
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
		if(isset($options['mobile'])) {
			$mobile = $options['mobile'];
			unset($options['mobile']);
		} else {
			$mobile = Configure::read('Mobile.on');
		}
		if($mobile){
			$url['prefix'] = 'mobile';
		}

		if(isset($options['templates'])) {
			$templates = $options['templates'];
		} else {
			$templates = 'posts';
		}
		unset ($options['templates']);

		echo $this->requestAction($url, array('return', 'pass' => array($id, $num, $templates), 'named' => $options));

	}

}
?>