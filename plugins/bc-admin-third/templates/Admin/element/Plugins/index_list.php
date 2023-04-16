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
 * Plugin Index
 *
 * @var AppView $this
 * @var array $plugins
 * @checked
 * @unitTest
 * @noTodo
 */

use BaserCore\View\AppView;

?>
<div class="bca-data-list__top">
  <?php if ($this->BcBaser->isAdminUser()): ?>
    <div>
      <?php echo $this->BcAdminForm->control('batch', [
        'type' => 'select',
        'options' => ['detach' => __d('baser_core', '無効')], 'empty' => __d('baser_core', '一括処理')
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
  <tr class="list-tool">
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select" title="<?php echo __d('baser_core', '一括選択') ?>">
      <?php if ($this->BcBaser->isAdminUser()): ?>
        <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser_core', '一括選択')]) ?>
      <?php endif ?>
      <?php if (!$this->request->getQuery('sortmode')): ?>
        <?php $this->BcBaser->link('<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>' . __d('baser_core', '並び替え'), ['?' => ['sortmode' => 1]], ['escape' => false]) ?>
      <?php else: ?>
        <?php $this->BcBaser->link('<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>' . __d('baser_core', 'ノーマル'), ['?' => ['sortmode' => 0]], ['escape' => false]) ?>
      <?php endif ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'プラグイン名') ?></th>
    <th class="bca-table-listup__thead-th" style="white-space: nowrap"><?php echo __d('baser_core', 'バージョン') ?></th>
    <?php if(!$this->request->getQuery('sortmode')): ?>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '説明') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '開発者') ?></th>
    <?php endif ?>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', '登録日') ?><br><?php echo __d('baser_core', '更新日') ?></th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'アクション') ?></th>
  </tr>
  </thead>
  <tbody>
  <?php if (!empty($plugins)): ?>
    <?php $count = 1 ?>
    <?php foreach($plugins as $plugin): ?>
      <?php $this->BcBaser->element('Plugins/index_row', ['plugin' => $plugin, 'count' => $count]) ?>
      <?php $count++ ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="6"><p class="no-data"><?php echo __d('baser_core', 'データが見つかりませんでした。') ?></p></td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
