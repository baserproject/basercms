<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 受信メール一覧　テーブル
 * @var \BcMail\View\MailAdminAppView $this
 * @var \Cake\ORM\ResultSet $mailMessages
 * @var \BcMail\Model\Entity\MailContent $mailContent
 */
$this->BcListTable->setColumnNumber(6);
$this->BcBaser->js('BcMail.admin/mail_messages/index.bundle', false, [
  'id' => 'AdminMailMessagesScript',
  'data-mailContentId' => $mailContent->id
]);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
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
      'class' => 'bca-btn',
      'data-bca-btn-size' => 'lg'
    ]) ?>
  </div>
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>

<!-- list -->
<table class="list-table sort-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select"><?php // 一括選択 ?>
      <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => __d('baser_core', '一括選択')]) ?>
    </th>
    <th class="bca-table-listup__thead-th" style="white-space: nowrap"><?php // id ?>
      <?php echo $this->Paginator->sort('id', [
          'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'No'),
          'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'No')
        ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a'
      ]) ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php // 受信日時 ?>
      <?php echo $this->Paginator->sort('name', [
          'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '受信日時'),
          'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '受信日時')
        ], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a'
      ]) ?>
    </th>
    <th class="bca-table-listup__thead-th" style="white-space: nowrap">受信内容</th>
    <th class="bca-table-listup__thead-th" style="white-space: nowrap">添付</th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody class="bca-table-listup__tbody">
  <?php if ($mailMessages->count()): ?>
    <?php $count = 0; ?>
    <?php foreach($mailMessages as $mailMessage): ?>
      <?php $this->BcBaser->element('MailMessages/index_row', ['mailMessage' => $mailMessage, 'count' => $count]) ?>
      <?php $count++; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="row-tools bca-table-listup__tbody-td">
        <p class="no-data">データが見つかりませんでした。</p>
      </td>
    </tr>
  <?php endif ?>
  </tbody>
</table>

<div class="bca-data-list__bottom">
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
    <!-- list-num -->
    <?php $this->BcBaser->element('list_num') ?>
  </div>
</div>
