<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\Model\Entity\Permission;
use BaserCore\View\BcAdminAppView;

/**
 * @var BcAdminAppView $this
 * @var string $userGroupTitle
 * @var Permission $permission
 * @var array $permissionMethodList
 * @var array $permissionAuthList
 * @var \Cake\ORM\ResultSet $permissionGroups
 * @checked
 * @unitTest
 * @noTodo
 */

$this->BcBaser->js('admin/permissions/form.bundle', false);
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <?php if ($permission->id): ?>
      <tr>
        <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('id', 'No') ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $permission->id ?>
          <?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>
        </td>
      </tr>
    <?php endif ?>
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('user_group_id', __d('baser_core', 'ユーザーグループ')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo h($userGroupTitle) ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('user_group_id', __d('baser_core', 'アクセスルールグループ')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('permission_group_type', ['type' => 'radio', 'options' => $this->BcAdminForm->getControlSource('Permissions.permission_group_type')]) ?>
        <?php echo $this->BcAdminForm->control('permission_group_id', ['type' => 'select', 'options' => $this->BcAdminForm->getControlSource('Permissions.permission_group_id'), 'empty' => __d('baser_core', 'アクセスルールグループを選択してください。')] ) ?>
        <?php echo $this->BcAdminForm->control('permission_group', ['type' => 'hidden', 'value' => json_encode($permissionGroups->toArray())] ) ?>
        <?php echo $this->BcAdminForm->error('permission_group_id') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser_core', 'ルール名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true, 'placeholder' => __d('baser_core', 'ユーザー管理')]) ?>

        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext"><?php echo __d('baser_core', 'ルール名には日本語が利用できます。特定しやすいわかりやすい名称を入力してください。') ?></div>
        <?php echo $this->BcAdminForm->error('name') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser_core', 'URL設定')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('url', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true, 'placeholder' => '/baser/admin/baser-core/users/index']) ?>

        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <ul>
            <li><?php echo __d('baser_core', 'スラッシュから始まるURLを入力してください。') ?></li>
            <li><?php echo __d('baser_core', '特定のフォルダ配下に対しアクセスできないようにする場合などにはワイルドカード（*）を利用します。<br>（例）ユーザー管理内のURL全てアクセスさせない場合： <br />/baser/admin/baser-core/users/* ') ?></li>
          </ul>
        </div>
        <?php echo $this->BcAdminForm->error('url') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('method', __d('baser_core', '権限')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('method', ['type' => 'select', 'options' => $permissionMethodList]) ?>
        <?php echo $this->BcAdminForm->error('method') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('method', __d('baser_core', 'アクセス')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('auth', ['type' => 'radio', 'options' => $permissionAuthList]) ?>
        <?php echo $this->BcAdminForm->error('auth') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('status', __d('baser_core', '利用状態')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('status', ['type' => 'checkbox', 'label' => __d('baser_core', '有効')]) ?>
        <?php echo $this->BcAdminForm->error('status') ?>
      </td>
    </tr>
  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>
