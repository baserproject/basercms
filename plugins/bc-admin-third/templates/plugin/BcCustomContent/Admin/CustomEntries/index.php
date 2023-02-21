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
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @var int $tableId
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', '{0}｜エントリー一覧', $customTable->title));
if(!$customTable->has_child) {
  $this->BcAdmin->setSearch('custom_entries_index');
}
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $tableId],
  'title' => __d('baser', '新規追加'),
]);
?>


<div id="DataList" class="bca-data-list">
  <?php $this->BcBaser->element('CustomEntries/index_list') ?>
</div>
