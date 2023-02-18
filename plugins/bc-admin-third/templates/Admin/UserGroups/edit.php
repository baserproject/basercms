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
$this->BcAdmin->setTitle(__d('baser', 'ユーザーグループ編集'));
$this->BcAdmin->setHelp('user_groups_form');
?>


<?= $this->BcAdminForm->create($userGroup, ['novalidate' => true, 'id' => 'UserGroupAdminEditForm']) ?>

<?php $this->BcBaser->element('UserGroups/form') ?>

<div class="submit bc-align-center section bca-actions">
  <div class="bca-actions__main">
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
  <div class="bca-actions__sub">
    <?php if ($userGroup->name != 'admins'): ?>
      <?= $this->BcAdminForm->postLink(
        __d('baser', '削除'),
        ['action' => 'delete', $userGroup->id],
        ['block' => true,
          'confirm' => __d('baser', "{0} を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセスルールは全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。", $userGroup->name),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm']
      ) ?>
    <?php endif; ?>
  </div>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
