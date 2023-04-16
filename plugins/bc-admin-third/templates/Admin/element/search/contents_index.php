<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * コンテンツ一覧
 *
 * @var BaserCore\View\BcAdminAppView $this
 * @var array $folders
 * @var array $authorList
 * @var array $typeList
 * @var \BaserCore\Form\ContentsSearchForm $contentsSearch
 * @checked
 * @unitTest
 * @noTodo
 */
?>


<?= $this->BcAdminForm->create($contentsSearch, ['type' => 'get', 'id' => 'ContentIndexForm'], ) ?>
<?= $this->BcAdminForm->control('open', ['type' => 'hidden', 'value' => true]) ?>
<?= $this->BcAdminForm->control('list_type', ['type' => 'hidden']) ?>

<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('folder_id', __d('baser_core', 'フォルダ'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('folder_id', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser_core', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('name', __d('baser_core', '名称'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 20]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('type', __d('baser_core', 'タイプ'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('type', ['type' => 'select', 'options' => $typeList, 'empty' => __d('baser_core', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('status', __d('baser_core', '公開状態'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('status', ['type' => 'select', 'options' => ['unpublish' => "―", 'publish' => "○"], 'empty' => __d('baser_core', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('author_id', __d('baser_core', '作成者'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('author_id', ['type' => 'select', 'options' => $authorList, 'empty' => __d('baser_core', '指定なし')]) ?>
	</span>
  <?= $this->BcSearchBox->dispatchShowField(); ?>
</p>
<div class="button bca-search__btns">
  <div  class="bca-search__btns-item">
    <?= $this->BcAdminForm->button(__d('baser_core', '検索'), [
      'id' => 'BtnSearchSubmit',
      'class' => 'bca-btn bca-loading',
      'data-bca-btn-type' => 'search'
    ]) ?>
  </div>
  <div class="bca-search__btns-item">
    <?php $this->BcBaser->link(__d('baser_core', 'クリア'), "javascript:void(0)", [
      'id' => 'BtnSearchClear',
      'class' => 'bca-btn',
      'data-bca-btn-type' => 'clear'
    ]) ?></div>
</div>
<?= $this->BcAdminForm->end() ?>
