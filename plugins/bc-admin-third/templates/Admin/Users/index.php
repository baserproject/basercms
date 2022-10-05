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
 * Users index
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */

$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add'],
  'title' => __d('baser', '新規追加'),
]);
$this->BcAdmin->setTitle(__d('baser', 'ユーザー一覧'));
$this->BcAdmin->setSearch('users_index');
$this->BcAdmin->setHelp('users_index');
$this->BcBaser->js('admin/users/index.bundle', false);
?>


<section id="DataList">
  <?php $this->BcBaser->element('Users/index_list') ?>
</section>
