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
 * [ADMIN] アクセスルール一覧　行
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Permission $permission
 * @var int $userGroupId
 * @var int $count
 * @checked
 * @unitTest
 * @noTodo
 */
if(!$permission->permission_group_id) return;
$permissionGroupTypes = \Cake\Core\Configure::read('BcPermission.permissionGroupTypes');
$type = (isset($permissionGroupTypes[$permission->permission_group->type]))? $permissionGroupTypes[$permission->permission_group->type] : '';
?>


<?php if (!$permission->status): ?>
  <?php $class = ' class="disablerow unpublish sortable"'; ?>
<?php else: ?>
  <?php $class = ' class="publish sortable"'; ?>
<?php endif; ?>

<tr id="Row<?php echo $count ?>" <?php echo $class; ?>>
  <td class="row-tools bca-table-listup__tbody-td ">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $permission->id, ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser_core', 'チェックする') . '</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $permission->id, 'escape' => false]) ?>
    <?php endif ?>
    <?php if ($this->request->getQuery('sortmode')): ?>
      <span class="sort-handle">
        <i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>
        <?php echo __d('baser_core', 'ドラッグ可能') ?>
      </span>
      <?php echo $this->BcAdminForm->control('id' . $permission->id, ['type' => 'hidden', 'class' => 'id', 'value' => $permission->id]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $permission->no; ?></td>
  <td class="bca-table-listup__tbody-td" nowrap="nowrap"><?php echo $type ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo h($permission->permission_group->name) ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php $this->BcBaser->link($permission->name, ['action' => 'edit', $userGroupId, $permission->id], ['escape' => true]) ?>
    <br>
    <?php echo $permission->url; ?>
  </td>
  <td
    class="bca-table-listup__tbody-td"><?php echo $this->BcText->arrayValue($permission->auth, [0 => '×', 1 => '〇']) ?></td>
  <?php echo $this->BcListTable->dispatchShowRow($permission) ?>
  <td class="bca-table-listup__tbody-td" nowrap="nowrap">
    <?php echo $this->BcTime->format($permission->created, 'yyyy-MM-dd'); ?><br/>
    <?php echo $this->BcTime->format($permission->modified, 'yyyy-MM-dd'); ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php if ($permission->status): ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'unpublish', $permission->id],
      ['block' => true,
        'title' => __d('baser_core', '無効'),
        'class' => 'btn-unpublish bca-btn-icon bca-loading',
        'data-bca-btn-type' => 'unpublish',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?php else: ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'publish', $permission->id],
      ['block' => true,
        'title' => __d('baser_core', '有効'),
        'class' => 'btn-publish bca-btn-icon bca-loading',
        'data-bca-btn-type' => 'publish',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?php endif; ?>
    <?php $this->BcBaser->link('', [
      'action' => 'edit',
      $userGroupId,
      $permission->id
    ], [
      'title' => __d('baser_core', '編集'),
      'class' => ' bca-btn-icon',
      'data-bca-btn-type' => 'edit',
      'data-bca-btn-size' => 'lg'
    ]) ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'copy', $permission->id],
      ['block' => true,
        'confirm' => __d('baser_core', "{0} を複製してもいいですか？", $permission->name),
        'title' => __d('baser_core', '複製'),
        'class' => 'btn-copy bca-btn-icon bca-loading',
        'data-bca-btn-type' => 'copy',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'delete', $permission->id],
      [
        'confirm' => __d('baser_core', "{0} を本当に削除してもいいですか？", $permission->name),
        'title' => __d('baser_core', '削除'),
        'class' => 'btn-delete bca-btn-icon bca-loading',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'lg']
    ) ?>
  </td>
</tr>
