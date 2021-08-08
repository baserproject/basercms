<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
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
<?= $this->BcAdminForm->create(null, ['url' => ['action' => 'index'], 'id' => 'ContentIndexForm'], ) ?>
<?= $this->BcAdminForm->control('Contents.open', ['type' => 'hidden', 'value' => true]) ?>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('Contents.folder_id', __d('baser', 'フォルダ'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('Contents.folder_id', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('Contents.name', __d('baser', '名称'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('Contents.name', ['type' => 'text', 'size' => 20]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('Contents.type', __d('baser', 'タイプ'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('Contents.type', ['type' => 'select', 'options' => $contentTypes, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('Contents.self_status', __d('baser', '公開状態'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('Contents.self_status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('Contents.author_id', __d('baser', '作成者'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('Contents.author_id', ['type' => 'select', 'options' => $authors, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <?= $this->BcSearchBox->dispatchShowField($this->request); ?>
</p>
<div class="button bca-search__btns">
  <div
    class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', '検索'), "javascript:void(0)", ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn', 'data-bca-btn-type' => 'search']) ?></div>
  <div
    class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', 'クリア'), "javascript:void(0)", ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?></div>
</div>
<?= $this->BcAdminForm->end() ?>
