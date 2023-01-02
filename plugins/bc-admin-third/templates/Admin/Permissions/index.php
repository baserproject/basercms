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

use BaserCore\Model\Entity\UserGroup;
use BaserCore\View\BcAdminAppView;

/**
 * [ADMIN] アクセスルール一覧
 *
 * @var BcAdminAppView $this
 * @var UserGroup $currentUserGroup
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜アクセスルール一覧'), $currentUserGroup->title));
$this->BcAdmin->setHelp('permissions_index');
$this->BcAdmin->setSearch('permissions_index');
$this->BcBaser->js('admin/permissions/index.bundle', false, [
  'id' => 'AdminPermissionsIndexScript',
  'data-userGroupId' => $currentUserGroup->id
]);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $currentUserGroup->id],
  'title' => __d('baser', '新規追加'),
]);
?>


<section id="DataList">
  <?php $this->BcBaser->element('Permissions/index_list') ?>
</section>

<?= $this->fetch('postLink') ?>


<div class="submit section bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcHtml->link(__d('baser', 'アクセスルールグループ一覧に戻る'), [
      'controller' => 'PermissionGroups',
      'action' => 'index',
      $currentUserGroup->id
    ], [
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
</div>
