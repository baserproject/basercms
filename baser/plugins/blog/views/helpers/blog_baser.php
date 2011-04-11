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
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
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
 * @package			baser.plugins.blog.views.helpers
 *
 */
class BlogBaserHelper extends AppHelper {
/**
 * ブログ記事一覧出力
 *
 * ページ編集画面等で利用する事ができる。
 * 利用例: <?php $baser->blogPosts('news', 3) ?>
 * ビュー: app/webroot/themed/{テーマ名}/blog/{コンテンツテンプレート名}/post_list.ctp
 * 注意事項: Webページで利用する場合、サーバーキャッシュは削除されないので手動で削除が必要
 * 
 * @param	int		$contentsName
 * @param	mixid	$mobile			'' / boolean
 * @return	void
 * @access	public
 */
	function blogPosts ($contentsName, $num = 5, $mobile = '') {

		$BlogContent = ClassRegistry::init('Blog.BlogContent');
		$id = $BlogContent->field('id', array('BlogContent.name'=>$contentsName));
		$url = array('plugin'=>'blog','controller'=>'blog','action'=>'posts');
		if($mobile === ''){
			$mobile = Configure::read('Mobile.on');
		}
		if($mobile){
			$url['prefix'] = 'mobile';
		}
		echo $this->requestAction($url, array('pass' => array($id, $num)));

	}

}
?>