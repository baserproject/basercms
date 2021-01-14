<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 一覧　検索ボックス
 */
$this->BlogCategories = $this->BcForm->getControlSource('BlogPost.blog_category_id', ['blogContentId' => $blogContent['BlogContent']['id']]);
$this->BlogTags = $this->BcForm->getControlSource('BlogPost.blog_tag_id');
$users = $this->BcForm->getControlSource("BlogPost.user_id");
?>

<?php echo $this->BcForm->create('BlogPost', ['url' => ['action' => 'index', $blogContent['BlogContent']['id']]]) ?>
<p>
	<span><?php echo $this->BcForm->label('BlogPost.name', __d('baser', 'タイトル')) ?><?php echo $this->BcForm->input('BlogPost.name', ['type' => 'text', 'size' => '30']) ?></span>
	<?php if ($this->BlogCategories): ?>
		<span><?php echo $this->BcForm->label('BlogPost.blog_category_id', __d('baser', 'カテゴリー')) ?><?php echo $this->BcForm->input('BlogPost.blog_category_id', ['type' => 'select', 'options' => $this->BlogCategories, 'escape' => true, 'empty' => __d('baser', '指定なし')]) ?></span>　
	<?php endif ?>
	<?php if ($blogContent['BlogContent']['tag_use'] && $this->BlogTags): ?>
		<span><?php echo $this->BcForm->label('BlogPost.blog_tag_id', __d('baser', 'タグ')) ?><?php echo $this->BcForm->input('BlogPost.blog_tag_id', ['type' => 'select', 'options' => $this->BlogTags, 'escape' => true, 'empty' => __d('baser', '指定なし')]) ?></span>　
	<?php endif ?>
	<span><?php echo $this->BcForm->label('BlogPost.status', __d('baser', '公開設定')) ?><?php echo $this->BcForm->input('BlogPost.status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定なし')]) ?></span>　
	<span><?php echo $this->BcForm->label('BlogPost.user_id', __d('baser', '作成者')) ?><?php echo $this->BcForm->input('BlogPost.user_id', ['type' => 'select', 'options' => $users, 'empty' => __d('baser', '指定なし')]) ?></span>　
	<?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="submit">
	<?php echo $this->BcForm->button(__d('baser', '検索'), ['class' => 'button', 'id' => 'BtnSearchSubmit']) ?>
	<?php echo $this->BcForm->button(__d('baser', 'クリア'), ['class' => 'button', 'id' => 'BtnSearchClear']) ?>
</div>
<?php echo $this->Form->end() ?>
