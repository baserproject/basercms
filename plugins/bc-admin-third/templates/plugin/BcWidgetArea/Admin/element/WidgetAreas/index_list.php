<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ウィジェットエリア一覧 テーブル
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \Cake\ORM\ResultSet $widgetAreas
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcListTable->setColumnNumber(6);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
  <?php if ($this->BcBaser->isAdminUser()): ?>
    <div class="bca-action-table-listup">
      <?php echo $this->BcAdminForm->control('batch', [
        'type' => 'select',
        'options' => ['delete' => __d('baser_core', '削除')],
        'empty' => __d('baser_core', '一括処理'),
        'data-bca-select-size' => 'lg'
      ]) ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '適用'), [
        'id' => 'BtnApplyBatch',
        'disabled' => 'disabled',
        'class' => 'bca-btn', 'data-bca-btn-size' => 'lg'
      ]) ?>
    </div>
  <?php endif ?>
  <div class="bca-data-list__sub">
    <!-- list-num -->
    <?php $this->BcBaser->element('list_num') ?>
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>


<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr class="">
    <th class="list-tool bca-table-listup__thead-th  bca-table-listup__thead-th--select">
      <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => __d('baser_core', '一括選択')]) ?>
    </th>
    <th class="bca-table-listup__thead-th">No</th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'ウィジェットエリア名') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '登録ウィジェット数') ?></th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th">
      <?php echo __d('baser_core', '登録日') ?><br><?php echo __d('baser_core', '更新日') ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php echo __d('baser_core', 'アクション') ?>
    </th>
  </tr>
  </thead>
  <tbody class="bca-table-listup__tbody">
  <?php if ($widgetAreas->count()): ?>
    <?php foreach($widgetAreas as $widgetArea): ?>
      <?php $this->BcBaser->element('WidgetAreas/index_row', ['widgetArea' => $widgetArea]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>">
        <p class="no-data">
          <?php echo __d('baser_core', 'データが見つかりませんでした。') ?>
        </p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
