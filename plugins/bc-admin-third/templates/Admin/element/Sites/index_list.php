<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\View\BcAdminAppView;

/**
 * サブサイト一覧
 *
 * @var BcAdminAppView $this
 * @var array $deviceList
 * @var array $langList
 * @var array $siteList
 * @checked
 * @unitTest
 * @noTodo
 */

$this->BcListTable->setColumnNumber(8);
?>


<div class="bca-data-list__top">
  <div class="bca-data-list__sub">
    <!-- pagination -->
    <?php $this->BcBaser->element('pagination') ?>
  </div>
</div>

<!-- ListTable -->
<table class="list-table sort-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
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
      echo $this->Paginator->sort('display_name',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'サイト名'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'サイト名')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('name',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '識別名称'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '識別名称')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
      <br>
      <?php
      echo $this->Paginator->sort('alias',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'エイリアス'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'エイリアス')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('status',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '公開状態'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '公開状態')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php echo $this->Paginator->sort('device',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'デバイス'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'デバイス')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
      <br>
      <?php
      echo $this->Paginator->sort('lang',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', '言語'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', '言語')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
    </th>
    <th class="bca-table-listup__thead-th">
      <?php
      echo $this->Paginator->sort('main_site_id',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'メインサイト'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'メインサイト')],
        ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
      );
      ?>
      <br>
      <?php
      echo $this->Paginator->sort('theme',
        ['asc' => '<i class="bca-icon--asc"></i>' . __d('baser_core', 'テーマ'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser_core', 'テーマ')],
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
    <th class="list-tool bca-table-listup__thead-th"><?php echo __d('baser_core', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody class="bca-table-listup__tbody">
  <?php if (!$sites->isEmpty()): ?>
    <?php foreach($sites as $key => $site): ?>
      <?php $this->BcBaser->element('Sites/index_row', [
        'site' => $site,
        'count' => ($key + 1),
        'langs' => $langList,
        'devices' => $deviceList,
        'siteList' => $siteList
      ]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td"><p
          class="no-data"><?php echo __d('baser_core', 'データがありません。') ?></p></td>
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
