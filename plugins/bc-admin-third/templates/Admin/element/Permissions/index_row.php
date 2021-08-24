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
use BaserCore\Utility\BcUtil;
use BaserCore\View\BcAdminAppView;
/**
 * [ADMIN] アクセス制限設定一覧　行
 *
 * @var BcAdminAppView $this
 */
?>


<?php if (!$data->status): ?>
  <?php $class = ' class="disablerow unpublish sortable"'; ?>
<?php else: ?>
  <?php $class = ' class="publish sortable"'; ?>
<?php endif; ?>
<tr<?php echo $class; ?>>
  <td class="row-tools bca-table-listup__tbody-td ">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('ListTool.batch_targets.' . $data->id, ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data->id, 'escape' => false]) ?>
    <?php endif ?>
    <?php if ($this->request->getQuery('sortmode')): ?>
      <span class="sort-handle"><i class="bca-btn-icon-text"
                                  data-bca-btn-type="draggable"></i><?php echo __d('baser', 'ドラッグ可能') ?></span>
      <?php echo $this->BcAdminForm->control('Sort.id' . $data->id, ['type' => 'hidden', 'class' => 'id', 'value' => $data->id]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $data->no; ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php $this->BcBaser->link($data->name, ['action' => 'edit', $currentUserGroup->id, $data->id], ['escape' => true]) ?>
    <br>
    <?php echo $data->url; ?>
  </td>
  <td
    class="bca-table-listup__tbody-td"><?php echo $this->BcText->arrayValue($data->auth, [0 => '×', 1 => '〇']) ?></td>
  <?php echo $this->BcListTable->dispatchShowRow($data) ?>
  <td class="bca-table-listup__tbody-td">
    <?php echo $this->BcTime->format($data->created, 'yyyy-MM-dd'); ?><br/>
    <?php echo $this->BcTime->format($data->modified, 'yyyy-MM-dd'); ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php if ($data->status): ?>
    <?= $this->BcForm->postLink(
      '',
      ['action' => 'unpublish', $data->id],
      ['block' => true,
        'title' => __d('baser', '無効'),
        'class' => 'btn-unpublish bca-btn-icon',
        'data-bca-btn-type' => 'unpublish',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?php else: ?>
    <?= $this->BcForm->postLink(
      '',
      ['action' => 'publish', $data->id],
      ['block' => true,
        'title' => __d('baser', '有効'),
        'class' => 'btn-publish bca-btn-icon',
        'data-bca-btn-type' => 'publish',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?php endif; ?>
    <?php $this->BcBaser->link('', ['action' => 'edit', $currentUserGroup->id, $data->id], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
    <?= $this->BcForm->postLink(
      '',
      ['action' => 'copy', $data->id],
      ['block' => true,
        'confirm' => __d('baser', "{0} を複製してもいいですか？", $data->name),
        'title' => __d('baser', '複製'),
        'class' => 'btn-copy bca-btn-icon',
        'data-bca-btn-type' => 'copy',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?= $this->BcForm->postLink(
      '',
      ['action' => 'delete', $data->id],
      ['block' => true,
        'confirm' => __d('baser', "{0} を本当に削除してもいいですか？", $data->name),
        'title' => __d('baser', '削除'),
        'class' => 'btn-delete bca-btn-icon',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'lg']
    ) ?>
  </td>
</tr>
