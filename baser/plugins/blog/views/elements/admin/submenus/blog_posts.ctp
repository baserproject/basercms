<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログ記事管理メニュー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
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
<h2>記事管理メニュー</h2>
<ul>
<?php if($this->params['controller'] == 'blog_posts' && $this->action == 'admin_edit'): ?>
<!--<li><?php // TODO 未実装 $baser->link('記事プレビュー',array('controller'=>'blog','action'=>'preview',$blogContent['BlogContent']['id'],$form->value('BlogPost.id')),array('target'=>'_blank')) ?></li>-->
<?php endif; ?>
<li><?php $baser->link('コメント一覧',array('plugin'=>false,'controller'=>'blog_comments','action'=>'index',$blogContent['BlogContent']['id'])) ?></li>
<li><?php $baser->link('記事一覧',array('controller'=>'blog_posts','action'=>'index',$blogContent['BlogContent']['id'])) ?></li>
<li><?php $baser->link('新規記事を登録',array('controller'=>'blog_posts','action'=>'add',$blogContent['BlogContent']['id'])) ?></li>
</ul>
</div>
