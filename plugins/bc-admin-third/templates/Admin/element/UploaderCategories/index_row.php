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
 * @var \BcUploader\Model\Entity\UploaderCategory $uploaderCategory
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<tr>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $uploaderCategory->id, [
        'type' => 'checkbox',
        'label' => '<span class="bca-visually-hidden">チェックする</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'value' => $uploaderCategory->id,
        'escape' => false
      ]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $uploaderCategory->id ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo h($uploaderCategory->name) ?></td>

  <?php echo $this->BcListTable->dispatchShowRow($uploaderCategory) ?>

  <td class="bca-table-listup__tbody-td">
    <?php echo $uploaderCategory->created ?><br/>
    <?php echo $uploaderCategory->modified ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php $this->BcBaser->link('', ['action' => 'edit', $uploaderCategory->id], [
      'title' => __d('baser', '編集'),
      'class' => 'bca-btn-icon',
      'data-bca-btn-type' => 'edit',
      'data-bca-btn-size' => 'lg'
    ]) ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'copy', $uploaderCategory->id], [
      'title' => __d('baser', 'コピー'),
      'class' => 'btn-copy bca-btn-icon',
      'data-bca-btn-type' => 'copy',
      'data-bca-btn-size' => 'lg'
    ]) ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'delete', $uploaderCategory->id], [
      'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $uploaderCategory->name),
      'title' => __d('baser', '削除'),
      'class' => 'btn-delete bca-btn-icon',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'lg'
    ]) ?>
  </td>
</tr>
