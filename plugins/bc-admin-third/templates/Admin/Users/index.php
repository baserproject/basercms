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

use BaserCore\View\{AppView as AppViewAlias};

/**
 * Users index
 * @var AppViewAlias $this
 */

$this->BcAdmin->addAdminMainBodyHeaderLinks([
	'url' => ['action' => 'add'],
	'title' => __d('baser', '新規追加'),
]);
$this->BcAdmin->setTitle(__d('baser', 'ユーザー一覧'));
$this->BcAdmin->setSearch('users_index');
$this->BcAdmin->setHelp('users_index');
$this->BcBaser->js('admin/users/index.bundle');
?>


<section id="DataList">
    <?php $this->BcBaser->element('Admin/Users/index_list') ?>
</section>
