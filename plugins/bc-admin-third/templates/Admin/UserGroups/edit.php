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
 * UserGroups Add
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\UserGroup $userGroup
 * @var bool $editable
 * @var bool $deletable
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', 'ユーザーグループ編集'));
$this->BcAdmin->setHelp('user_groups_form');
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add'],
  'title' => __d('baser_core', '新規追加'),
]);
?>


<?= $this->BcAdminForm->create($userGroup, ['novalidate' => true, 'id' => 'UserGroupAdminEditForm']) ?>

<?php $this->BcBaser->element('UserGroups/form') ?>

<div class="submit bc-align-center section bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'),
      ['admin' => true, 'controller' => 'UserGroups', 'action' => 'index'],
      [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]
    ) ?>
    <?php if(\Cake\Core\Configure::read('BcApp.adminGroupId') !== (int)$userGroup->id): ?>
    <?php $this->BcBaser->link(__d('baser_core', 'アクセスルール設定'), [
      'controller' => 'PermissionGroups',
      'action' => 'index',
      $userGroup->id
    ], [
      'class' => 'bca-btn bca-actions__item'
    ]) ?>
    <?php endif ?>
    <?= $this->BcAdminForm->button(
      __d('baser_core', '保存'),
      ['div' => false,
        'class' => 'button bca-btn bca-actions__item bca-loading',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave']
    ) ?>
  </div>
  <div class="bca-actions__sub">
    <?php if ($userGroup->name != 'admins'): ?>
      <?= $this->BcAdminForm->postLink(
        __d('baser_core', '削除'),
        ['action' => 'delete', $userGroup->id],
        ['block' => true,
          'confirm' => __d('baser_core', "{0} を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセスルールは全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。", $userGroup->name),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm']
      ) ?>
    <?php endif; ?>
  </div>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
