<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Uploader.View
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

$uploaderCategories = $this->BcForm->getControlSource("Uploader.UploaderFile.uploader_category_id");
if (!isset($listId)) {
	$listId = $this->getVar('listId');
}
?>


<div class="file-filter submit bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('BlogPost.blog_tag_id', __d('baser', '名称'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('Filter.name', ['type' => 'text', 'id' => 'FilterName' . $listId, 'style' => 'width:160px']) ?>
	</span>
	<?php if (!empty($uploaderCategories)): ?>
		<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('BlogPost.blog_tag_id', __d('baser', 'カテゴリ'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('Filter.uploader_category_id', ['type' => 'select', 'options' => $uploaderCategories, 'empty' => __d('baser', '指定なし'), 'id' => 'FilterUploaderCategoryId' . $listId]) ?>
	</span>
	<?php endif ?>
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('BlogPost.blog_tag_id', __d('baser', 'タイプ'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('Filter.uploader_type', ['type' => 'radio', 'options' => ['all' => __d('baser', '指定なし'), 'img' => __d('baser', '画像'), 'etc' => __d('baser', '画像以外')], 'id' => 'FilterUploaderType' . $listId]) ?>
	</span>
	<span class="button bca-search__btns">
		<span
			class="bca-search__btns-item"><?php echo $this->BcForm->submit(__d('baser', '検索'), ['id' => 'BtnFilter' . $listId, 'div' => false, 'class' => 'button filter-control bca-btn', 'data-bca-btn-type' => 'search']) ?></span>
	</span>
</div>
