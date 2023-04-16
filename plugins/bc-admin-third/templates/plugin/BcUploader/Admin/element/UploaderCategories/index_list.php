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
 * @var \Cake\ORM\ResultSet $uploaderCategories
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcListTable->setColumnNumber(5);
$this->BcBaser->js('BcUploader.admin/uploader_categories/index.bundle');
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
        'class' => 'bca-btn',
        'data-bca-btn-size' => 'lg'
      ]) ?>
    </div>
  <?php endif ?>
</div>

<table class="list-table sort-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select" title="<?php echo __d('baser_core', '一括選択') ?>">
      <?php if ($this->BcBaser->isAdminUser()): ?>
        <div>
          <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser_core', '一括選択')]) ?>
        </div>
      <?php endif ?>
    </th>
    <th style="white-space: nowrap" class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('id', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'No'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'No')], [
        'escape' => false,
        'class' => 'btn-direction bca-table-listup__a'
      ]) ?>
    </th>
    <th style="white-space: nowrap" class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('name', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'カテゴリ名'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'カテゴリ名')], [
        'escape' => false,
        'class' => 'btn-direction bca-table-listup__a'
      ]) ?>
    </th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th style="white-space: nowrap" class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('created', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '登録日'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '登録日')], [
        'escape' => false, 'class' =>
        'btn-direction bca-table-listup__a'
      ]) ?>
      <br/>
      <?php echo $this->Paginator->sort('modified', [
        'asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '更新日'),
        'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '更新日')], [
        'escape' => false,
        'class' => 'btn-direction bca-table-listup__a'
      ]) ?>
    </th>
    <th class="bca-table-listup__thead-th">アクション</th>
  </tr>
  </thead>
  <tbody>
  <?php if ($uploaderCategories->count()): ?>
    <?php foreach($uploaderCategories as $uploaderCategory): ?>
      <?php $this->BcBaser->element('UploaderCategories/index_row', ['uploaderCategory' => $uploaderCategory]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>">
        <p class="no-data"><?php echo __d('baser_core', 'データが見つかりませんでした。') ?></p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
