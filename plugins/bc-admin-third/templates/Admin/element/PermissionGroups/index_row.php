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
 * @var \BaserCore\Model\Entity\PermissionGroup $entity
 * @var int $userGroupId
 * @var int $count
 * @checked
 * @unitTest
 * @noTodo
 */
$class = '';
if (!$entity->status) {
  $class = ' class="unpublish"';
}
?>


<tr<?php echo $class ?>>
  <td class="bca-table-listup__tbody-td"><?php echo $count ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php $this->BcBaser->link($entity->name, ['action' => 'edit', $userGroupId, $entity->id], ['escape' => true]) ?>
  </td>
  <td class="bca-table-listup__tbody-td">
    <?php echo $entity->amount ?>
  </td>
  <td class="bca-table-listup__tbody-td">
    <?php echo $this->BcText->arrayValue($entity->status, [0 => '×', 1 => '〇']) ?>
  </td>
  <?php echo $this->BcListTable->dispatchShowRow($entity) ?>
  <td class="bca-table-listup__tbody-td">
    <?php echo $this->BcTime->format($entity->created); ?><br>
    <?php echo $this->BcTime->format($entity->modified); ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php $this->BcBaser->link('', ['action' => 'edit', $userGroupId, $entity->id], [
      'title' => __d('baser_core', '編集'),
      'class' => ' bca-btn-icon',
      'data-bca-btn-type' => 'edit',
      'data-bca-btn-size' => 'lg'
    ]) ?>
  </td>
</tr>
