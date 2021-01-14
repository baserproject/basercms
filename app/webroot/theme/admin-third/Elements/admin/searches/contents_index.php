<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツ一覧
 *
 * @var BcAppView $this
 * @var array $folders
 * @var array $contentTypes
 * @var array $authors
 */
?>


<?php echo $this->BcForm->create('Content', ['url' => ['action' => 'index']]) ?>
<?php echo $this->BcForm->hidden('Content.open', ['value' => true]) ?>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('Content.folder_id', __d('baser', 'フォルダ'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('Content.folder_id', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('Content.name', __d('baser', '名称'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('Content.name', ['type' => 'text', 'size' => 20]) ?>
	</span>
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('Content.type', __d('baser', 'タイプ'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('Content.type', ['type' => 'select', 'options' => $contentTypes, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('Content.self_status', __d('baser', '公開状態'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('Content.self_status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定なし')]) ?>
	</span>
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('Content.author_id', __d('baser', '作成者'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('Content.author_id', ['type' => 'select', 'options' => $authors, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
	<?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="button bca-search__btns">
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', '検索'), "javascript:void(0)", ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn', 'data-bca-btn-type' => 'search']) ?></div>
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', 'クリア'), "javascript:void(0)", ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?></div>
</div>
<?php echo $this->BcForm->end() ?>
