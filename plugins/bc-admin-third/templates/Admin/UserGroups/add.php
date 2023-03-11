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
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', '新規ユーザーグループ登録'));
$this->BcAdmin->setHelp('user_groups_form');
?>

<?= $this->BcAdminForm->create($userGroup, ['novalidate' => true, 'id' => 'UserGroupAdminAddForm']) ?>

<?php $this->BcBaser->element('UserGroups/form') ?>

<div class="submit bc-align-center section bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'),
      ['admin' => true, 'controller' => 'UserGroups', 'action' => 'index'],
      [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]
    ) ?>&nbsp;
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
</div>

<?= $this->BcAdminForm->end() ?>
