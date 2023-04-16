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
 * アクセスルールグループフォーム
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $userGroupTitle
 * @var int $userGroupId
 * @var \BaserCore\Model\Entity\PermissionGroup $entity
 * @var array $permissionMethodList
 * @var array $permissionAuthList
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('user_group_id', __d('baser_core', 'ユーザーグループ')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo h($userGroupTitle) ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('type', __d('baser_core', 'タイプ')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo h($entity->type) ?>
        <?php echo $this->BcAdminForm->control('type', ['type' => 'hidden']) ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser_core', 'ルールグループ名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', ['type' => 'text','size' => 60]) ?>
      </td>
    </tr>

<?php if($this->getRequest()->getParam('action') === 'edit'): ?>
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser_core', 'ルール')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">

        <?php if ($entity->permissions): ?>
          <table style="margin-bottom:10px;">
          <?php foreach($entity->permissions as $key => $permission): ?>
            <tr>
              <td style="padding-right:20px;">
              <?php echo $this->BcAdminForm->control('permissions.' . $key . '.id', ['type' => 'hidden']) ?>
              <?php echo $this->BcAdminForm->control('permissions.' . $key . '.name', ['type' => 'hidden']) ?>
              <?php echo $this->BcAdminForm->control('permissions.' . $key . '.user_group_id', ['type' => 'hidden']) ?>
              <span style="display: inline-block; vertical-align: middle;"><?php echo h($permission->name) ?></span>
              </td>
              <td>
              <?php echo $this->BcAdminForm->control('permissions.' . $key . '.method', ['type' => 'select', 'options' => $permissionMethodList]) ?>
              &nbsp;&nbsp;&nbsp;&nbsp;
              <?php echo $this->BcAdminForm->control('permissions.' . $key . '.auth', ['type' => 'radio', 'options' => $permissionAuthList]) ?>
              <?php echo $this->BcAdminForm->control('permissions.' . $key . '.status', ['type' => 'checkbox', 'label' => __d('baser_core', '有効')]) ?>
              <?php $this->BcBaser->link(__d('baser_core', '詳細'), [
                'controller' => 'Permissions',
                'action' => 'edit',
                $userGroupId,
                $permission->id,
                $entity->id
              ], [
                'div' => false,
                'class' => 'button bca-btn bca-actions__item',
                'data-bca-btn-size' => 'sm',
                'data-bca-btn-width' => 'sm',
                'id' => 'BtnSave'
              ]) ?>
              </td>
            </tr>
          <?php endforeach ?>
          </table>
        <?php endif ?>

        <?php echo $this->BcHtml->link(__d('baser_core', '新規追加'), [
          'controller' => 'Permissions',
          'action' => 'add',
          $userGroupId,
          $entity->id
        ], [
          'class' => 'button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'add',
          'data-bca-btn-size' => 'sm',
        ]) ?>
      </td>
    </tr>
<?php endif ?>

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
