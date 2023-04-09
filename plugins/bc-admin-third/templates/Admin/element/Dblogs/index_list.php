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
 * @var \Cake\ORM\ResultSet $dblogs
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcListTable->setColumnNumber(4);
?>

<div class="bca-data-list__top">
  <?php if ($this->BcBaser->isAdminUser() && $dblogs->count()): ?>
    <div class="submit clear bca-update-log__delete">
      <?php echo $this->BcAdminForm->postLink(__d('baser_core', 'ログを全て削除'), ['action' => 'delete_all'], [
          'class' => 'bca-btn bca-loading',
          'data-bca-btn-type' => 'delete',
          'confirm' => __d('baser_core', '最近の動きのログを削除します。いいですか？')
      ]) ?>
    </div>
  <?php endif ?>
  <div class="bca-data-list__sub">
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>

<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
    <tr>
      <th class="list-tool bca-table-listup__thead-th  bca-table-listup__thead-th--select">
        <?php echo $this->Paginator->sort('id',
          [
            'asc' => '<i class="bca-icon--asc" title="' . __d('baser_core', 'No') . '"></i>' . __d('baser_core', 'No'),
            'desc' => '<i class="bca-icon--desc" title="' . __d('baser_core', 'No') . '"></i>' . __d('baser_core', 'No')
          ],
          ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
      </th>
      <th class="bca-table-listup__thead-th">
        <?php echo $this->Paginator->sort('message',
          [
            'asc' => '<i class="bca-icon--asc" title="' . __d('baser_core', '内容') . '"></i>' . __d('baser_core', '内容'),
            'desc' => '<i class="bca-icon--desc" title="' . __d('baser_core', '内容') . '"></i>' . __d('baser_core', '内容')
          ],
          ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
      </th>
      <th class="bca-table-listup__thead-th">
        <?php echo $this->Paginator->sort('message',
          [
            'asc' => '<i class="bca-icon--asc" title="' . __d('baser_core', 'ユーザー') . '"></i>' . __d('baser_core', 'ユーザー'),
            'desc' => '<i class="bca-icon--desc" title="' . __d('baser_core', 'ユーザー') . '"></i>' . __d('baser_core', 'ユーザー')
          ],
          ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
      </th>
      <th class="bca-table-listup__thead-th">
        <?php echo $this->Paginator->sort('message',
          [
            'asc' => '<i class="bca-icon--asc" title="' . __d('baser_core', '操作日時') . '"></i>' . __d('baser_core', '操作日時'),
            'desc' => '<i class="bca-icon--desc" title="' . __d('baser_core', '操作日時') . '"></i>' . __d('baser_core', '操作日時')
          ],
          ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
      </th>
    </tr>
  </thead>
  <tbody>
    <?php if ($dblogs->count()): ?>
      <?php foreach ($dblogs as $dblog): ?>
        <?php $this->BcBaser->element('Dblogs/index_row', ['dblog' => $dblog]) ?>
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

<div class="bca-data-list__bottom">
  <div class="bca-data-list__sub">
    <?php $this->BcBaser->element('pagination') ?>
    <?php $this->BcBaser->element('list_num') ?>
  </div>
</div>
