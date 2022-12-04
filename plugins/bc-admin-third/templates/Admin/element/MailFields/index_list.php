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
 * [ADMIN] メールフィールド 一覧　テーブル
 * @var \BcMail\View\MailAdminAppView $this
 * @var \BcMail\Model\Entity\MailContent $mailContent
 * @var \Cake\ORM\ResultSet $mailFields
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js(['admin/mail_fields/index.bundle'], false, [
  'id' => 'AdminMailFieldsIndexScript',
  'data-mailContentId' => $mailContent->id
]);
$this->BcListTable->setColumnNumber(8);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
  <?php if ($this->BcBaser->isAdminUser()): ?>
    <div>
      <?php echo $this->BcAdminForm->control('batch', [
        'type' => 'select',
        'options' => ['publish' => __d('baser', '有効'), 'unpublish' => __d('baser', '無効'), 'delete' => __d('baser', '削除')],
        'empty' => __d('baser', '一括処理')
      ]) ?>
      <?php echo $this->BcAdminForm->button(__d('baser', '適用'), [
        'id' => 'BtnApplyBatch',
        'disabled' => 'disabled',
        'class' => 'bca-btn',
        'data-bca-btn-size' => 'lg'
      ]) ?>
    </div>
  <?php endif ?>

</div>

<table class="list-table sort-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select"><?php // 一括選択 ?>
      <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => __d('baser', '一括選択')]) ?>
      <?php if (!$this->request->getQuery('sortmode')): ?>
        <?php $this->BcBaser->link(
          '<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>' . __d('baser', '並び替え'),
          ['?' => ['sortmode' => 1], $mailContent->id],
          ['escape' => false]
        ) ?>
      <?php else: ?>
        <?php $this->BcBaser->link(
          '<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>' . __d('baser', 'ノーマル'),
          ['?' => ['sortmode' => 0], $mailContent->id],
          ['escape' => false]
        ) ?>
      <?php endif ?>
    </th>
    <th class="bca-table-listup__thead-th">No</th>
    <th class="bca-table-listup__thead-th">
      <?php echo __d('baser', 'フィールド名') ?><br><?php echo __d('baser', '項目名') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'タイプ') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'グループ名') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', '必須') ?></th>

    <?php echo $this->BcListTable->dispatchShowHead() ?>

    <th class="bca-table-listup__thead-th">
      <?php echo __d('baser', '登録日') ?><br>
      <?php echo __d('baser', '更新日') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if ($mailFields->count()): ?>
    <?php $count = 1; ?>
    <?php foreach($mailFields as $mailField): ?>
      <?php $this->BcBaser->element('MailFields/index_row', ['mailField' => $mailField, 'count' => $count]) ?>
      <?php $count++ ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>">
        <p class="no-data">
          <?php echo __d('baser', 'データが見つかりませんでした。') ?>
        </p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
