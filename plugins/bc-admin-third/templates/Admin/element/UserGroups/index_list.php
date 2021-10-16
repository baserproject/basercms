<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\View\AppView;

/**
 * user groups index list
 * @var AppView $this
 */

$this->BcListTable->setColumnNumber(5);
?>

<script type="text/javascript">
  $(function () {
    $('.tag a').css({'text-decoration': 'none'})
  });
</script>

<div class="bca-data-list__top">
  <div class="bca-data-list__sub">
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>

<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('id',
        [
          'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', 'No'),
          'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', 'No')
        ],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('name',
        [
          'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', 'ユーザーグループ名'),
          'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', 'ユーザーグループ名')
        ],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('title',
        [
          'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '表示名'),
          'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', '表示名')
        ],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('created',
        [
          'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '登録日'),
          'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '登録日')
        ],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?><br/>
      <?php echo $this->Paginator->sort('modified',
        [
          'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '更新日'),
          'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '更新日')
        ],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if (!empty($userGroups)): ?>
    <?php foreach($userGroups as $userGroup): ?>
      <?php $this->BcBaser->element('UserGroups/index_row', ['userGroup' => $userGroup]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
          class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p>
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
