<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事管理メニュー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>ブログ管理メニュー</h2>
	<ul>
		<li><?php $baser->link('公開ページ確認',array('admin'=>false,'plugin'=>$blogContent['BlogContent']['name'],'controller'=>$blogContent['BlogContent']['name'],'action'=>'index'),array('target'=>'_blank')) ?></li>
		<li><?php $baser->link('コメント一覧',array('plugin'=>false,'controller'=>'blog_comments','action'=>'index',$blogContent['BlogContent']['id'])) ?></li>
		<li><?php $baser->link('記事一覧',array('controller'=>'blog_posts','action'=>'index',$blogContent['BlogContent']['id'])) ?></li>
<?php if(isset($newCatAddable) && $newCatAddable): ?>
		<li><?php $baser->link('新規記事を登録',array('controller'=>'blog_posts','action'=>'add',$blogContent['BlogContent']['id'])) ?></li>
<?php endif ?>
		<li><?php $baser->link('ブログ基本設定',array('plugin'=>false,'controller'=>'blog_contents','action'=>'edit',$blogContent['BlogContent']['id'])) ?></li>
	</ul>
</div>
