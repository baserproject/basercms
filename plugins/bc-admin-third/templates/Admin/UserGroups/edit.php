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

use BaserCore\View\{AppView as AppViewAlias};
use BaserCore\Model\Entity\UserGroup;

/**
 * UserGroups Add
 * @var AppViewAlias $this
 * @var UserGroup $userGroup
 * @var bool $editable
 * @var bool $deletable
 */
?>


<?= $this->BcAdminForm->create($userGroup, ['novalidate' => true]) ?>

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
          'confirm' => __d('baser', "{0} を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。", $userGroup->name),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm']
      ) ?>
    <?php endif; ?>
  </div>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
