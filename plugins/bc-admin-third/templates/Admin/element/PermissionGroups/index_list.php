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
 * ルールグループ一覧
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \Cake\ORM\ResultSet $entities
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcListTable->setColumnNumber(6);
$i = 1;
?>


<table class="list-table sort-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="bca-table-listup__thead-th">No</th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'ルールグループ名') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', '設定数') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', '有効') ?></th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th">
      <?php echo __d('baser', '登録日') ?><br><?php echo __d('baser', '更新日') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if ($entities->count()): ?>
    <?php foreach($entities as $entity): ?>
      <?php $this->BcBaser->element('PermissionGroups/index_row', ['entity' => $entity, 'count' => $i++]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>">
        <p class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
