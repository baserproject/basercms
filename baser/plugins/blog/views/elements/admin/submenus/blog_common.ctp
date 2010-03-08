<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログ共通メニュー
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
<h2>ブログ共通メニュー</h2>
<ul>

<?php if($this->params['controller'] == 'blog_posts' || $this->params['controller'] == 'blog_categories' || $this->params['controller'] == 'blog_comments'): ?>
<li><?php echo $html->link('公開ページ確認',array('admin'=>false,'plugin'=>$blogContent['BlogContent']['name'],'controller'=>$blogContent['BlogContent']['name'],'action'=>'index'),array('target'=>'_blank')) ?></li>
<li><?php echo $html->link($blogContent['BlogContent']['title'].'基本設定',array('plugin'=>false,'controller'=>'blog_contents','action'=>'edit',$blogContent['BlogContent']['id'])) ?></li>
<?php endif ?>
<li><?php echo $html->link('ブログ一覧',array('controller'=>'blog_contents','action'=>'index')) ?></li>
<li><?php echo $html->link('新規ブログを登録',array('controller'=>'blog_contents','action'=>'add')) ?></li>
<!--<li><?php echo $html->link('ブログプラグイン基本設定',array('controller'=>'blog_configs','action'=>'form')) ?></li>-->
</ul>
</div>