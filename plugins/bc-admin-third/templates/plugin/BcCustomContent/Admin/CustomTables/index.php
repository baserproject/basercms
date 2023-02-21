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
 * カスタムテーブル一覧
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', 'テーブル一覧'));
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add'],
  'title' => __d('baser', '新規追加'),
]);
?>


<div id="DataList" class="bca-data-list">
  <?php $this->BcBaser->element('CustomTables/index_list') ?>
</div>
