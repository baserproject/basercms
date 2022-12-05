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
 * [ADMIN] ウィジェットエリア一覧 行
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcWidgetArea\Model\Entity\WidgetArea $widgetArea
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<tr>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $widgetArea->id, [
        'type' => 'checkbox',
        'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'value' => $widgetArea->id,
        'escape' => false
      ]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $widgetArea->id; ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php $this->BcBaser->link($widgetArea->name, ['action' => 'edit', $widgetArea->id], ['escape' => true]); ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $widgetArea->count; ?></td>

  <?php echo $this->BcListTable->dispatchShowRow($widgetArea) ?>

  <td class="bca-table-listup__tbody-td">
    <?php echo $this->BcTime->format($widgetArea->created); ?>
    <br>
    <?php echo $this->BcTime->format($widgetArea->modified); ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php $this->BcBaser->link('', ['action' => 'edit', $widgetArea->id], [
      'title' => __d('baser', '編集'),
      'class' => 'bca-btn-icon',
      'data-bca-btn-type' => 'edit',
      'data-bca-btn-size' => 'lg'
    ]); ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'delete', $widgetArea->id], [
      'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $widgetArea->name),
      'title' => __d('baser', '削除'),
      'class' => 'btn-delete bca-btn-icon',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'lg'
    ]) ?>
  </td>
</tr>
