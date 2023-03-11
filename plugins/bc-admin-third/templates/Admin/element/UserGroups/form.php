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
use Cake\Core\Configure;

/**
 * UserGroups Form
 * @var \BaserCore\View\BcAdminAppView $this
 * @var UserGroup $userGroup
 * @checked
 * @unitTest
 * @noTodo
 */

$this->BcBaser->js('admin/user_groups/form.bundle', false, [
  'id' => 'AdminUserGroupsFormScript',
  'defer' => true,
  'data-isAdmin' => $userGroup->isAdmin()
]);
$authPrefixes = $this->BcAdminForm->getControlSource('BaserCore.UserGroups.auth_prefix');
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <?php if ($this->request->getParam('action') == 'edit'): ?>
      <tr>
        <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('id', 'No') ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $userGroup->id; ?>
          <?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>
        </td>
      </tr>
    <?php endif; ?>
    <tr>
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser_core', 'ユーザーグループ名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span></th>
      <td class="col-input bca-form-table__input">
        <?php if ($userGroup->name == 'admins' && $this->request->getParam('action') == 'edit'): ?>
          <?php echo $userGroup->name; ?>
          <?php echo $this->BcAdminForm->control('name', ['type' => 'hidden']) ?>
        <?php else: ?>
          <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 20, 'maxlength' => 255, 'autofocus' => true]) ?>
        <?php endif ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <ul>
            <li><?php echo __d('baser_core', '重複しない識別名称を半角のみで入力してください。') ?></li>
            <li><?php echo __d('baser_core', 'admins の場合は変更できません。') ?></li>
          </ul>
        </div>
        <?php echo $this->BcAdminForm->error('name') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('title', __d('baser_core', '表示名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('title', ['type' => 'text', 'size' => 20, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext"><?php echo __d('baser_core', '日本語が入力できますのでわかりやすい名称を入力します。') ?></div>
        <?php echo $this->BcAdminForm->error('title') ?>
      </td>
    </tr>
    <tr>
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('use_admin_globalmenu', __d('baser_core', 'その他')) ?></th>
      <td class="col-input bca-form-table__input">
        <span
          style="white-space: nowrap"><?php echo $this->BcAdminForm->control('use_move_contents', ['type' => 'checkbox', 'label' => __d('baser_core', 'コンテンツのドラッグ＆ドロップ移動機能を利用する')]) ?></span>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <span><?php echo __d('baser_core', 'コンテンツ一覧のツリー構造において、ドラッグ＆ドロップでコンテンツの移動を許可するかどうかを設定します。') ?></span>
        </div>
        <?php echo $this->BcAdminForm->error('use_move_contents') ?>
      </td>
    </tr>
    <?php if (count($authPrefixes) > 1): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('auth_prefix', __d('baser_core', '認証プレフィックス設定')) ?>
          &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <table>
            <?php foreach($authPrefixes as $prefix => $label): ?>
              <tr>
                <td>
                  <?php echo $this->BcAdminForm->control('auth_prefix[]', [
                    'type' => 'checkbox',
                    'label' => $label,
                    'value' => $prefix,
                    'checked' => $userGroup->isAuthPrefixAvailabled($prefix),
                    'hiddenField' => false,
                    'id' => 'auth_prefix_' . $prefix
                  ]) ?>
                </td>
                <?php if(Configure::read('BcApp.adminGroupId') !== (int)$userGroup->id): ?>
                <td>
                  <?php $settings = $userGroup->getAuthPrefixSettingsArray() ?>
                  <?php echo $this->BcAdminForm->control('auth_prefix_settings.' . $prefix . '.type', [
                    'type' => 'radio',
                    'value' => $userGroup->getAuthPrefixSetting($prefix, 'type'),
                    'options' => [
                      '1' => __d('baser_core', 'フルアクセス'),
                      '2' => __d('baser_core', '限定アクセス')
                  ]]) ?>
                </td>
                <?php endif ?>
              </tr>
            <?php endforeach ?>
          </table>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('auth_prefix') ?>
          <div class="bca-helptext">
            <?php echo __d('baser_core', '認証プレフィックスの設定を指定します。<br>
                システム管理グループの場合、管理システムの設定は変更できず、管理システム以外を有効にしたとしてもフルアクセスが前提となります。<br><br>
                なお、限定アクセスの場合、詳細な設定はアクセスルール設定より行います。') ?>
          </div>
        </td>
      </tr>
    <?php endif ?>
    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

