<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログカテゴリ管理メニュー
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
<h2>カテゴリ管理メニュー</h2>
<ul>
	<?php if($this->params['controller'] == 'blog_categories' && $this->action=='admin_index'): ?>
        <!--<li><?php echo $html->link('カテゴリプレビュー',array('admin'=>false,'controller'=>'blog','action'=>'preview',$blogContent['BlogContent']['id'],'category',$form->value('BlogCategory.id')),array('target'=>'_blank')) ?></li>-->
	<?php elseif($this->params['controller'] == 'blog_categories' && $this->action != 'admin_add'): ?>
        <li><?php echo $html->link('カテゴリプレビュー',array('controller'=>'blog','action'=>'preview',$blogContent['BlogContent']['id'],'category',$form->value('BlogCategory.id')),array('target'=>'_blank')) ?></li>
    <?php endif; ?>
    <li><?php echo $html->link('カテゴリ一覧',array('controller'=>'blog_categories','action'=>'index',$blogContent['BlogContent']['id'])) ?></li>
    <li><?php echo $html->link('新規カテゴリを登録',array('controller'=>'blog_categories','action'=>'add',$blogContent['BlogContent']['id'])) ?></li>
</ul>
</div>