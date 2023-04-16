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
 * @var int $userGroupId
 * @var string $userGroupTitle
 * @var \Cake\ORM\ResultSet $permissionGroups
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(__d('baser_core', '%s｜アクセスルール一覧'), $userGroupTitle));
$this->BcAdmin->setHelp('permissions_index');
$this->BcAdmin->setSearch('permissions_index');
$this->BcBaser->js('admin/permissions/index.bundle', false, [
  'id' => 'AdminPermissionsIndexScript',
  'defer' => true,
  'data-userGroupId' => $userGroupId,
  'data-permissionGroups' => json_encode($permissionGroups)
]);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $userGroupId],
  'title' => __d('baser_core', '新規追加'),
]);
?>


<section id="DataList">
  <?php $this->BcBaser->element('Permissions/index_list') ?>
</section>

<?= $this->fetch('postLink') ?>


<div class="submit section bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcHtml->link(__d('baser_core', 'アクセスルールグループ一覧に戻る'), [
      'controller' => 'PermissionGroups',
      'action' => 'index',
      $userGroupId
    ], [
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
</div>
