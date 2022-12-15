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
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
$uploaderCategories = $this->BcAdminForm->getControlSource('BcUploader.UploaderFiles.uploader_category_id');
if (!isset($listId)) {
  $listId = $this->get('listId');
}
?>


<?php echo $this->BcAdminForm->create(null, ['novalidate' => true, 'type' => 'get', 'url' => ['action' => 'index']]) ?>
<div class="file-filter submit bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('name', __d('baser', '名称'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('name', [
      'type' => 'text',
      'id' => 'FilterName' . $listId,
      'style' => 'width:160px'
    ]) ?>
	</span>
  <?php if (!empty($uploaderCategories)): ?>
    <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('uploader_category_id', __d('baser', 'カテゴリ'), [
		  'class' => 'bca-search__input-item-label'
    ]) ?>
    <?php echo $this->BcAdminForm->control('uploader_category_id', [
      'type' => 'select',
      'options' => $uploaderCategories,
      'empty' => __d('baser', '指定なし'),
      'id' => 'FilterUploaderCategoryId' . $listId
    ]) ?>
	</span>
  <?php endif ?>
  <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('uploader_type', __d('baser', 'タイプ'), [
		  'class' => 'bca-search__input-item-label'
    ]) ?>
    <?php echo $this->BcAdminForm->control('uploader_type', [
      'type' => 'radio',
      'options' => [
        'all' => __d('baser', '指定なし'),
        'img' => __d('baser', '画像'),
        'etc' => __d('baser', '画像以外')
      ],
      'id' => 'FilterUploaderType' . $listId
    ]) ?>
	</span>
  <span class="button bca-search__btns">
		<span class="bca-search__btns-item">
		  <?php if($listId): ?>
		  <?php echo $this->BcAdminForm->button(__d('baser', '検索'), ['id' => 'BtnFilter', 'class' => 'bca-btn bca-loading', 'data-bca-btn-type' => 'search']) ?>
		  <?php else: ?>
      <?php echo $this->BcAdminForm->button(__d('baser', '検索'), ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-loading', 'data-bca-btn-type' => 'search']) ?>
      <?php endif ?>
    </span>
	</span>
</div>
<?php echo $this->Form->end() ?>
