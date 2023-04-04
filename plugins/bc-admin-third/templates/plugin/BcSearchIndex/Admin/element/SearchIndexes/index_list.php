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

use BaserCore\View\BcAdminAppView;

/**
 * [ADMIN] 検索インデックス一覧　テーブル
 *
 * @var BcAdminAppView $this
 * @var \Cake\ORM\ResultSet $searchIndexes
 */

$this->BcListTable->setColumnNumber(9);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
  <?php if ($this->BcBaser->isAdminUser()): ?>
    <div>
      <?php echo $this->BcAdminForm->control('batch', ['type' => 'select', 'options' => ['delete' => __d('baser_core', '削除')], 'empty' => __d('baser_core', '一括処理')]) ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn']) ?>
    </div>
  <?php endif ?>
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>

<!-- list -->
<table class="list-table sort-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select">
      <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => __d('baser_core', '一括選択')]) ?>
    </th>
    <th class="bca-table-listup__thead-th">No</th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'タイプ') ?><br><?php echo __d('baser_core', 'タイトル') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'コンテンツ内容') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '公開状態') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '公開期間') ?></th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '登録日') ?><br><?php echo __d('baser_core', '更新日') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '優先度') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if ($searchIndexes->count()): ?>
    <?php $count = 0; ?>
    <?php foreach($searchIndexes as $searchIndex): ?>
      <?php $this->BcBaser->element('SearchIndexes/index_row', ['searchIndex' => $searchIndex, 'count' => $count]) ?>
      <?php $count++; ?>
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
