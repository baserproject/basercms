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
 * [ADMIN] アクセス制限設定一覧
 *
 * @var BcAppView $this
 */
$this->BcListTable->setColumnNumber(6);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
  <div class="bca-action-table-listup">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('ListTool.batch', ['type' => 'select', 'options' => ['publish' => __d('baser', '有効'), 'unpublish' => __d('baser', '無効'), 'del' => __d('baser', '削除')], 'empty' => __d('baser', '一括処理'), 'data-bca-select-size' => 'lg']) ?>
      <?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn', 'data-bca-btn-size' => 'lg']) ?>
    <?php endif ?>
  </div>
</div>


<table class="list-table sort-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th  bca-table-listup__thead-th--select"><?php // 一括選択 ?>
      <?php if ($this->BcBaser->isAdminUser()): ?>
        <?php echo $this->BcAdminForm->control('ListTool.checkall', ['type' => 'checkbox', 'label' => __d('baser', '一括選択')]) ?>
      <?php endif; ?>
      <?php if (!$this->request->getQuery('sortmode')): ?>
        <?php
          $this->BcBaser->link('<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>' . __d('baser', '並び替え'), ['sortmode' => 1, $currentUserGroup->id], ['escape' => false])
          ?>
      <?php else: ?>
        <?php
          $this->BcBaser->link('<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>' . __d('baser', 'ノーマル'), ['sortmode' => 0, $currentUserGroup->id], ['escape' => false])
        ?>
      <?php endif ?>
    </th>
    <th class="bca-table-listup__thead-th">No</th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'ルール名') ?><br><?php echo __d('baser', 'URL設定') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクセス') ?></th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', '登録日') ?><br><?php echo __d('baser', '更新日') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if (!empty($permissions)): ?>
    <?php foreach($permissions as $permission): ?>
      <?php $this->BcBaser->element('Permissions/index_row', ['data' => $permission]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
          class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
