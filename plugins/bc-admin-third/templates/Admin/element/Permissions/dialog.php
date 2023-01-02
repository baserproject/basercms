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

use BaserCore\View\BcAdminAppView;
use Cake\Routing\Router;

/**
 * [ADMIN] アクセスルール登録（ポップアップ）
 * @var BcAdminAppView $this
 * @var array $permissionMethodList
 * @var array $permissionAuthList
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcBaser->js('admin/permissions/dialog.bundle');
?>


<div id="PermissionDialog" title="アクセスルール登録" hidden>

  <?= $this->BcAdminForm->create(null, [
    'novalidate' => true,
    'id' => 'PermissionAjaxAddForm',
  ]); ?>

  <dl>
    <dt><?php echo $this->BcAdminForm->label('user_group_id', __d('baser', 'ユーザーグループ')) ?></dt>
    <dd class="col-input">
      <?php echo $this->BcAdminForm->control('user_group_id', [
        'type' => 'select',
        'options' => $this->BcAdminForm->getControlSource('BaserCore.Permissions.user_group_id'),
        'id' => 'permission-user-group-id'
      ]) ?>
    </dd>

    <dt><?php echo $this->BcAdminForm->label('permission_group_id', __d('baser', 'ルールグループ')) ?></dt>
    <dd class="col-input">
      <?php echo $this->BcAdminForm->control('permission_group_id', [
        'type' => 'select',
        'options' => $this->BcAdminForm->getControlSource('BaserCore.Permissions.permission_group_id', ['type' => 'Admin']),
        'id' => 'permission-permission-group-id'
      ]) ?>
    </dd>

    <dt><?php echo $this->BcAdminForm->label('name', __d('baser', 'ルール名')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('name', [
        'type' => 'text',
        'size' => 50,
        'class' => 'required',
        'value' => $this->fetch('title'),
        'id' => 'permission-name'
      ]) ?>
    </dd>

    <dt><?php echo $this->BcAdminForm->label('url', __d('baser', 'URL設定')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('url', [
        'type' => 'text',
        'size' => 50,
        'class' => 'required',
        'value' => Router::url(),
        'id' => 'permission-url'
      ]) ?>
    </dd>

    <dt><?php echo $this->BcAdminForm->label('method', __d('baser', '権限')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('method', [
        'type' => 'select',
        'options' => $permissionMethodList,
        'id' => 'permission-method'
      ]) ?>
      <?php echo $this->BcAdminForm->error('method') ?>
    </dd>

    <dt><?php echo $this->BcAdminForm->label('auth', __d('baser', 'アクセス')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('auth', [
        'type' => 'radio',
        'options' => $permissionAuthList,
        'value' => 0,
        'id' => 'permission-auth'
      ]) ?>
      <?php echo $this->BcAdminForm->error('auth') ?>
    </dd>
  </dl>
  <?php echo $this->BcAdminForm->end() ?>
</div>
