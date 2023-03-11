<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * Permission Index Search
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var int $userGroupId
 * @checked
 * @unitTest
 * @noTodo
 */
?>


<?php echo $this->BcAdminForm->create(null, [
  'novalidate' => true,
  'type' => 'get',
  'url' => ['action' => 'index', $userGroupId]
]) ?>

<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('permission_group_type', __d('baser_core', 'タイプ'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('permission_group_type', [
      'type' => 'radio',
      'hiddenField' => false,
      'options' => $this->BcAdminForm->getControlSource('Permissions.permission_group_type'),
      'empty' => __d('baser_core', '指定なし')
    ]) ?>
  </span>
  <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('permission_group_id', __d('baser_core', 'アクセスルールグループ'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('permission_group_id', [
      'type' => 'select',
      'options' => $this->BcAdminForm->getControlSource('Permissions.permission_group_id'),
      'empty' => __d('baser_core', '指定なし')
    ]) ?>
	</span>
</p>
<div class="button bca-search__btns">
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '検索'), ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-loading', 'data-bca-btn-type' => 'search']) ?>
  </div>
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser_core', 'クリア'), ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?>
  </div>
</div>

<?php echo $this->Form->end() ?>
