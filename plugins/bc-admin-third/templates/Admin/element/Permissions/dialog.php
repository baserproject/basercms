<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\View\BcAdminAppView;
use Cake\Routing\Router;

/**
 * [ADMIN] アクセス制限管理（ポップアップ）
 * @var BcAdminAppView $this
 * @var array $permissionMethodList
 * @var array $permissionAuthList
 * @checked
 * @noTodo
 */
$this->BcBaser->js('admin/permissions/dialog.bundle');
?>


<div id="PermissionDialog" title="アクセス制限登録" hidden>

  <?= $this->BcAdminForm->create(null, [
    'novalidate' => true,
    'id' => 'PermissionAjaxAddForm',
  ]); ?>

  <dl>
    <dt><?php echo $this->BcAdminForm->label('user_group_id', __d('baser', 'ユーザーグループ')) ?></dt>
    <dd class="col-input">
      <?php echo $this->BcAdminForm->control('user_group_id', ['type' => 'select', 'options' => $this->BcAdminForm->getControlSource('BaserCore.Permissions.user_group_id')]) ?>
    </dd>

    <dt><?php echo $this->BcAdminForm->label('name', __d('baser', 'ルール名')) ?></dt>
    <dd><?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 50, 'class' => 'required', 'value' => $this->fetch('title')]) ?></dd>

    <dt><?php echo $this->BcAdminForm->label('url', __d('baser', 'URL設定')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('url', ['type' => 'text', 'size' => 50, 'class' => 'required', 'value' => Router::url()]) ?>
    </dd>

    <dt><?php echo $this->BcAdminForm->label('method', __d('baser', '権限')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('method', ['type' => 'select', 'options' => $permissionMethodList]) ?>
      <?php echo $this->BcAdminForm->error('method') ?>
    </dd>

    <dt><?php echo $this->BcAdminForm->label('auth', __d('baser', 'アクセス')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('auth', ['type' => 'radio', 'options' => $permissionAuthList, 'value' => 0]) ?>
      <?php echo $this->BcAdminForm->error('auth') ?>
    </dd>
  </dl>
  <?php echo $this->BcAdminForm->end() ?>
</div>
