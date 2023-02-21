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
 * カスタムテーブル / テーブル
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \Cake\ORM\ResultSet $entities
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcListTable->setColumnNumber(5);
?>


<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select">No</th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', '識別名') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'タイトル') ?></th>

    <?php echo $this->BcListTable->dispatchShowHead() ?>

    <th class="bca-table-listup__thead-th">
      <?php echo __d('baser', '登録日') ?><br>
      <?php echo __d('baser', '更新日') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if ($entities->count()): ?>
    <?php foreach($entities as $entity): ?>
      <?php $this->BcBaser->element('CustomTables/index_row', ['entity' => $entity]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td">
        <p class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
