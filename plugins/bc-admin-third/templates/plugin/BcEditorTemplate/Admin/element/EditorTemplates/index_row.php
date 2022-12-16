<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] エディタテンプレート一覧　行
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcEditorTemplate\Model\Entity\EditorTemplate $editorTemplate
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<tr<?php $this->BcListTable->rowClass(true, $editorTemplate) ?>>
  <td class="bca-table-listup__tbody-td"><?php echo $editorTemplate->id ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php if ($editorTemplate->image): ?>
      <?php $this->BcBaser->img('/files/editor/' . $editorTemplate->image, [
        'url' => ['action' => 'edit', $editorTemplate->id],
        'alt' => $editorTemplate->name,
        'title' => $editorTemplate->name,
        'style' => 'float:left;margin-right:10px;height:36px'
      ]) ?>
    <?php endif ?>
    <?php $this->BcBaser->link($editorTemplate->name, ['action' => 'edit', $editorTemplate->id], ['escape' => true]) ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo h($editorTemplate->description) ?></td>

  <?php echo $this->BcListTable->dispatchShowRow($editorTemplate) ?>

  <td class="bca-table-listup__tbody-td" style="white-space:nowrap">
    <?php echo $this->BcTime->format($editorTemplate->created, 'yyyy-MM-dd') ?>
    <br>
    <?php echo $this->BcTime->format($editorTemplate->modified, 'yyyy-MM-dd') ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php $this->BcBaser->link('',
      ['action' => 'edit', $editorTemplate->id],
      ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']
    ) ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'delete', $editorTemplate->id], [
      'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $editorTemplate->name),
      'title' => __d('baser', '削除'),
      'class' => 'btn-delete bca-btn-icon',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'lg'
    ]) ?>
  </td>
</tr>
