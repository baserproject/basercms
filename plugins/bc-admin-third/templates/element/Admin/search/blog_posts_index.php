<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
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
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('BlogPost.name', __d('baser', 'タイトル'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('BlogPost.name', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => '30']) ?></span>
	<?php if ($this->BlogCategories): ?>
		<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('BlogPost.blog_category_id', __d('baser', 'カテゴリー'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('BlogPost.blog_category_id', ['type' => 'select', 'options' => $this->BlogCategories, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
	<?php endif ?>
	<?php if ($blogContent['BlogContent']['tag_use'] && $this->BlogTags): ?>
		<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('BlogPost.blog_tag_id', __d('baser', 'タグ'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('BlogPost.blog_tag_id', ['type' => 'select', 'options' => $this->BlogTags, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
	<?php endif ?>
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('BlogPost.status', __d('baser', '公開状態'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('BlogPost.status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定なし')]) ?>
	</span>
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('BlogPost.user_id', __d('baser', '作成者'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('BlogPost.user_id', ['type' => 'select', 'options' => $users, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
	<?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="button bca-search__btns">
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', '検索'), "javascript:void(0)", ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-btn-lg', 'data-bca-btn-size' => "lg"]) ?></div>
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', 'クリア'), "javascript:void(0)", ['id' => 'BtnSearchClear', 'class' => 'bca-btn']) ?></div>
</div>
<?php echo $this->Form->end() ?>
