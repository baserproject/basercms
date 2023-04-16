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
 * [ADMIN] エディタテンプレート一覧　テーブル
 *
 * @var BcAppView $this
 * @var \Cake\ORM\ResultSet $editorTemplates
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
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'テンプレート名') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '説明文') ?></th>

    <?php echo $this->BcListTable->dispatchShowHead() ?>

    <th class="bca-table-listup__thead-th">
      <?php echo __d('baser_core', '登録日') ?><br>
      <?php echo __d('baser_core', '更新日') ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if (!empty($editorTemplates)): ?>
    <?php foreach($editorTemplates as $editorTemplate): ?>
      <?php $this->BcBaser->element('EditorTemplates/index_row', ['editorTemplate' => $editorTemplate]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td">
        <p class="no-data"><?php echo __d('baser_core', 'データが見つかりませんでした。') ?></p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
