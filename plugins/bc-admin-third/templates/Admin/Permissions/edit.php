<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

use BaserCore\View\{AppView as AppViewAlias};
use BaserCore\Model\Entity\Permission;

/**
 * Permissions Add
 * @var AppViewAlias $this
 * @var Permission $permission
 * @var int $userGroupId
 * @var string $userGroupTitle
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setHelp('permissions_form');
$this->BcAdmin->setTitle(sprintf(__d('baser_core', '%s｜アクセスルール編集'), $userGroupTitle));
?>


<?= $this->BcAdminForm->create($permission, ['novalidate' => true]) ?>

<?php $this->BcBaser->element('Permissions/form') ?>

<div class="submit section bca-actions">
  <div class="bca-actions__main">
    <?php if ($this->getRequest()->getParam('pass.2')): ?>
      <?php $this->BcBaser->link(__d('baser_core', 'アクセスグループ編集に戻る'), [
        'controller' => 'PermissionGroups',
        'action' => 'edit',
        $userGroupId,
        $this->getRequest()->getParam('pass.2')
      ], [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]) ?>
    <?php endif ?>
    <?php $this->BcBaser->link(__d('baser_core', '一覧に戻る'), [
      'action' => 'index',
      $userGroupId
    ], [
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
    <?= $this->BcAdminForm->button(
      __d('baser_core', '保存'),
      ['div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave']
    ) ?>
  </div>
  <div class="bca-actions__sub">
      <?= $this->BcAdminForm->postLink(
        __d('baser_core', '削除'),
        ['action' => 'delete', $permission->id],
        ['block' => true,
          'confirm' => __d('baser_core', '{0} を本当に削除してもいいですか？', $permission->name),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm'
        ]
      ) ?>
  </div>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
