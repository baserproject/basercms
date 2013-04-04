<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事 一覧　検索ボックス
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$blogCategories = $bcForm->getControlSource('BlogPost.blog_category_id',array('blogContentId'=>$blogContent['BlogContent']['id']));
$blogTags = $bcForm->getControlSource('BlogPost.blog_tag_id');
$users = $bcForm->getControlSource("BlogPost.user_id");
?>

<?php echo $bcForm->create('BlogPost',array('url'=>array('action'=>'index',$blogContent['BlogContent']['id']))) ?>
<p>
	<span><?php echo $bcForm->label('BlogPost.name', 'タイトル') ?> <?php echo $bcForm->input('BlogPost.name', array('type' => 'text', 'size' => '30')) ?></span>
	<?php if($blogCategories): ?>
	<span><?php echo $bcForm->label('BlogPost.blog_category_id', 'カテゴリー') ?> <?php echo $bcForm->input('BlogPost.blog_category_id', array('type' => 'select', 'options' => $blogCategories, 'escape'=>false, 'empty' => '指定なし')) ?></span>　
	<?php endif ?>
	<?php if($blogContent['BlogContent']['tag_use'] && $blogTags): ?>
	<span><?php echo $bcForm->label('BlogPost.blog_tag_id', 'タグ') ?> <?php echo $bcForm->input('BlogPost.blog_tag_id', array('type' => 'select', 'options' => $blogTags, 'escape' => false, 'empty' => '指定なし')) ?></span>　
	<?php endif ?>
	<span><?php echo $bcForm->label('BlogPost.status', '公開設定') ?> <?php echo $bcForm->input('BlogPost.status', array('type' => 'select', 'options' => $bcText->booleanMarkList(), 'empty' => '指定なし')) ?></span>　
	<span><?php echo $bcForm->label('BlogPost.user_id', '作成者') ?> <?php echo $bcForm->input('BlogPost.user_id', array('type' => 'select', 'options' => $users, 'empty' => '指定なし')) ?></span>　
</p>
<div class="button">
	<?php $bcBaser->link($bcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $bcBaser->link($bcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>