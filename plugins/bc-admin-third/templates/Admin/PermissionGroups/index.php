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
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\UserGroup $currentUserGroup
 * @var int $userGroupId
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->addAdminMainBodyHeaderLinks(['url' => [
  'action' => 'add',
  $userGroupId,
  '?' => ['prefix' => $this->getRequest()->getData('list_type')]
], 'title' => __d('baser_core', '新規追加')]);
$this->BcAdmin->setTitle(sprintf(__d('baser_core', 'アクセスルールグループ一覧')));
$this->BcBaser->js('admin/permission_groups/index.bundle', false, [
  'id' => 'AdminPermissionGroupsIndexScript',
  'defer' => true
]);
?>


<?php $this->BcBaser->element('PermissionGroups/index_view_setting') ?>

<section id="DataList">
  <?php $this->BcBaser->element('PermissionGroups/index_list') ?>
</section>

<div class="submit section bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->postLink(__d('baser_core', 'ルールを再構築する'), [
      'action' => 'rebuild_by_user_group',
      $userGroupId
    ], [
      'confirm' => __d('baser_core', 'このユーザーグループのアクセスルールを再構築します。これまでの変更内容はリセットされます。よろしいですか？'),
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
    ]) ?>

    <?php echo $this->BcHtml->link(__d('baser_core', 'アクセスルール一覧に移動'), [
      'controller' => 'Permissions',
      'action' => 'index',
      $userGroupId,
      '?' => ['permission_group_type' => $this->getRequest()->getData('list_type')]
    ], [
      'class' => 'bca-btn bca-actions__item',
    ]) ?>
  </div>
</div>
