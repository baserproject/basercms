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
 * @var array $currentUserGroup
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setHelp('permissions_form');
$this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜新規アクセスルール登録'), $currentUserGroup->title));
?>


<?= $this->BcAdminForm->create($permission, ['novalidate' => true]) ?>

<?php $this->BcBaser->element('Permissions/form') ?>

<div class="submit section bca-actions">
  <div class="bca-actions__main">
    <?php if ($this->getRequest()->getParam('pass.1')): ?>
      <?php echo $this->BcHtml->link(__d('baser', 'アクセスグループ編集に戻る'), [
        'controller' => 'PermissionGroups',
        'action' => 'edit',
        $currentUserGroup->id,
        $this->getRequest()->getParam('pass.1')
      ], [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]) ?>
    <?php endif ?>
    <?php echo $this->BcHtml->link(__d('baser', '一覧に戻る'), [
      'action' => 'index',
      $currentUserGroup->id
    ], [
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
    <?= $this->BcAdminForm->button(
      __d('baser', '保存'),
      ['div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave']
    ) ?>
  </div>
</div>

<?= $this->BcAdminForm->end() ?>
