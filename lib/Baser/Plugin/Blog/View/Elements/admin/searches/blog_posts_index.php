<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 一覧　検索ボックス
 */
$this->BlogCategories = $this->BcForm->getControlSource('BlogPost.blog_category_id', array('blogContentId' => $blogContent['BlogContent']['id']));
$this->BlogTags = $this->BcForm->getControlSource('BlogPost.blog_tag_id');
$users = $this->BcForm->getControlSource("BlogPost.user_id");
?>

<?php echo $this->BcForm->create('BlogPost', array('url' => array('action' => 'index', $blogContent['BlogContent']['id']))) ?>
<p>
	<span><?php echo $this->BcForm->label('BlogPost.name', 'タイトル') ?> <?php echo $this->BcForm->input('BlogPost.name', array('type' => 'text', 'size' => '30')) ?></span>
	<?php if ($this->BlogCategories): ?>
		<span><?php echo $this->BcForm->label('BlogPost.blog_category_id', 'カテゴリー') ?> <?php echo $this->BcForm->input('BlogPost.blog_category_id', array('type' => 'select', 'options' => $this->BlogCategories, 'escape' => true, 'empty' => '指定なし')) ?></span>　
	<?php endif ?>
	<?php if ($blogContent['BlogContent']['tag_use'] && $this->BlogTags): ?>
		<span><?php echo $this->BcForm->label('BlogPost.blog_tag_id', 'タグ') ?> <?php echo $this->BcForm->input('BlogPost.blog_tag_id', array('type' => 'select', 'options' => $this->BlogTags, 'escape' => true, 'empty' => '指定なし')) ?></span>　
	<?php endif ?>
	<span><?php echo $this->BcForm->label('BlogPost.status', '公開設定') ?> <?php echo $this->BcForm->input('BlogPost.status', array('type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => '指定なし')) ?></span>　
	<span><?php echo $this->BcForm->label('BlogPost.user_id', '作成者') ?> <?php echo $this->BcForm->input('BlogPost.user_id', array('type' => 'select', 'options' => $users, 'empty' => '指定なし')) ?></span>　
	<?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="button">
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>