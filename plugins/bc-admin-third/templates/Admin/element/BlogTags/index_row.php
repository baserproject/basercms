<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログタグ一覧　行
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogTag $blogTag
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<tr>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $blogTag->id, [
        'type' => 'checkbox',
        'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'value' => $blogTag->id,
        'escape' => false
      ]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $blogTag->id ?></td>

  <td
    class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($blogTag->name, ['action' => 'edit', $blogTag->id], ['escape' => true]) ?></td>

  <?php echo $this->BcListTable->dispatchShowRow($blogTag) ?>

  <td class="bca-table-listup__tbody-td"><?php echo $this->BcTime->format($blogTag->created, 'yyyy-MM-dd'); ?>
    <br/>
    <?php echo $this->BcTime->format($blogTag->modified, 'yyyy-MM-dd'); ?></td>
  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php $this->BcBaser->link('',
      ['action' => 'edit', $blogTag->id],
      ['title' => __d('baser', '編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']
    ) ?>
    <?= $this->BcAdminForm->postLink('', ['action' => 'delete', $blogTag->id], [
      'confirm' => __d('baser', "このデータを本当に削除してもいいですか？\nこのタグに関連する記事は削除されません。"),
      'title' => __d('baser', '削除'),
      'class' => 'btn-delete bca-btn-icon',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'lg',
    ]) ?>
  </td>
</tr>
