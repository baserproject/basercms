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

/**
 * [ADMIN] アクセス制限管理（ポップアップ）
 */
use Cake\Routing\Router;
$methodList = $this->BcAdminPermission->getMethodList();
?>


<div id="PermissionDialog" title="アクセス制限登録" style="display:none">

  <?= $this->BcAdminForm->create(null, [
    'novalidate' => true, 
    'method' => 'POST', 
    'id' => 'PermissionAjaxAddForm',
    'url' => ['plugin' => 'BaserCore', 'controller' => 'permissions', 'action' => 'ajax_add'],
  ]); ?>
  
  
  <dl>
    <dt><?php echo $this->BcForm->label('user_group_id', __d('baser', 'ユーザーグループ')) ?></dt>
    <dd class="col-input">
      <?php echo $this->BcAdminForm->control('user_group_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('Permissions.user_group_id')]) ?>
    </dd>
    
    <dt><?php echo $this->BcForm->label('name', __d('baser', 'ルール名')) ?></dt>
    <dd><?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 50, 'class' => 'required', 'value' => $this->fetch('title')]) ?></dd>
    
    <dt><?php echo $this->BcForm->label('url', __d('baser', 'URL設定')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('url', ['type' => 'text', 'size' => 50, 'class' => 'required', 'value' => Router::url()]) ?>
    </dd>
    
    <dt><?php echo $this->BcForm->label('url', __d('baser', 'メソッド')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('method', ['type' => 'select', 'options' => $methodList]) ?>
      <?php echo $this->BcForm->error('method') ?>
    </dd>
  </dl>
  <?php echo $this->BcAdminForm->end() ?>
</div>

