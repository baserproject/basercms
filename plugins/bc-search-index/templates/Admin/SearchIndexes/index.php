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
 * [ADMIN] 検索インデックス一覧
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcAdmin->setTitle(__d('baser', '検索インデックス一覧'));
$this->BcAdmin->setSearch('BcSearchIndex.search_indexes_index');
$this->BcAdmin->setHelp('BcSearchIndex.search_indexes_index');
$this->BcBaser->js([
  'BcSearchIndex.admin/search_indexes/index.bundle'
]);
echo $this->BcBaser->i18nScript([
  'reconstructSearchTitle' => __d('baser', '確認'),
  'reconstructSearchMessage' => __d('baser', '現在の検索インデックスを消去して、再構築します。本当にいいですか？'),
]);
?>


<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(['controller' => 'search_indices', 'action' => 'ajax_batch']) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
  <div id="flashMessage" class="notice-message"></div>
</div>
<div id="AjaxChangePriorityUrl" class="display-none"><?php $this->BcBaser->url(['action' => 'ajax_change_priority']) ?></div>
<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('SearchIndexes/index_list') ?></div>


<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php $this->BcBaser->link(__d('baser', '検索インデックス再構築'), ['controller' => 'search_indexes', 'action' => 'reconstruct'], [
      'id' => 'BtnReconstruct',
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
    ]) ?>
  </div>
</div>


