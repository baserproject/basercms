<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事 一覧　検索ボックス
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$blogCategories = $formEx->getControlSource('BlogPost.blog_category_id',array('blogContentId'=>$blogContent['BlogContent']['id']));
$blogTags = $formEx->getControlSource('BlogPost.blog_tag_id');
$users = $formEx->getControlSource("BlogPost.user_id");
?>

<?php echo $formEx->create('BlogPost',array('url'=>array('action'=>'index',$blogContent['BlogContent']['id']))) ?>
<p>
	<span><?php echo $formEx->label('BlogPost.name', 'タイトル') ?> <?php echo $formEx->input('BlogPost.name', array('type' => 'text', 'size' => '30')) ?></span>
	<?php if($blogCategories): ?>
	<span><?php echo $formEx->label('BlogPost.blog_category_id', 'カテゴリ') ?> <?php echo $formEx->input('BlogPost.blog_category_id', array('type' => 'select', 'options' => $blogCategories, 'escape'=>false, 'empty' => '指定なし')) ?></span>　
	<?php endif ?>
	<?php if($blogContent['BlogContent']['tag_use'] && $blogTags): ?>
	<span><?php echo $formEx->label('BlogPost.blog_tag_id', 'タグ') ?> <?php echo $formEx->input('BlogPost.blog_tag_id', array('type' => 'select', 'options' => $blogTags, 'escape' => false, 'empty' => '指定なし')) ?></span>　
	<?php endif ?>
	<span><?php echo $formEx->label('BlogPost.status', '公開設定') ?> <?php echo $formEx->input('BlogPost.status', array('type' => 'select', 'options' => $textEx->booleanMarkList(), 'empty' => '指定なし')) ?></span>　
	<span><?php echo $formEx->label('BlogPost.user_id', '作成者') ?> <?php echo $formEx->input('BlogPost.user_id', array('type' => 'select', 'options' => $users, 'empty' => '指定なし')) ?></span>　
</p>
<div class="button">
	<?php $baser->link($baser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $baser->link($baser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>