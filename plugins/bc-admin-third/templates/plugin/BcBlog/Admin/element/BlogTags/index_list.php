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
 * [ADMIN] ブログタグ一覧　テーブル
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \Cake\ORM\ResultSet $blogTags
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js('BcBlog.admin/blog_tags/index.bundle', false);
$this->BcListTable->setColumnNumber(5);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
  <?php if ($this->BcBaser->isAdminUser()): ?>
    <div class="bca-action-table-listup">
      <?php echo $this->BcAdminForm->control('batch', ['type' => 'select', 'options' => ['delete' => __d('baser_core', '削除')], 'empty' => __d('baser_core', '一括処理'), 'data-bca-select-size' => 'lg']) ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn', 'data-bca-btn-size' => 'lg']) ?>
    </div>
  <?php endif ?>
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>


<!-- list -->
<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select" title="<?php echo __d('baser_core', '一括選択') ?>">
      <?php if ($this->BcBaser->isAdminUser()): ?>
        <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser_core', '一括選択')]) ?>
      <?php endif ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('id',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'No'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'No')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('name',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'ブログタグ名'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'ブログタグ名')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>

    <?php echo $this->BcListTable->dispatchShowHead() ?>

    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('created',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '登録日'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '登録日')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
      <br/>
      <?php
      echo $this->Paginator->sort('modified',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '更新日'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '更新日')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="list-tool bca-table-listup__thead-th">
      <?php echo __d('baser_core', 'アクション') ?>
    </th>
  </tr>
  </thead>
  <tbody class="bca-table-listup__tbody">
  <?php if (!empty($blogTags)): ?>
    <?php foreach($blogTags as $blogTag): ?>
      <?php $this->BcBaser->element('BlogTags/index_row', ['blogTag' => $blogTag]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
          class="no-data"><?php echo __d('baser_core', 'データが見つかりませんでした。') ?></p></td>
    </tr>
  <?php endif; ?>
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
