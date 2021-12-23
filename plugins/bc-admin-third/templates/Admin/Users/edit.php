<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\View\{AppView as AppViewAlias};
use BaserCore\Model\Entity\User;

/**
 * Users Edit
 * @var AppViewAlias $this
 * @var User $user
 * @var bool $editable
 * @var bool $deletable
 */
$this->BcAdmin->setTitle(__d('baser', 'ユーザー編集'));
$this->BcAdmin->setHelp('users_form');
?>


<?= $this->BcAdminForm->create($user, ['novalidate' => true]) ?>

<?php $this->BcBaser->element('Users/form') ?>

<div class="submit section bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcHtml->link(__d('baser', '一覧に戻る'),
      ['admin' => true, 'controller' => 'users', 'action' => 'index'],
      [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]
    ) ?>
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
  <?php if ($this->BcAdminUser->isDeletable($user->id)): ?>
    <div class="bca-actions__sub">
      <?= $this->BcAdminForm->postLink(
        __d('baser', '削除'),
        ['action' => 'delete', $user->id],
        ['block' => true,
          'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $user->name),
          'class' => 'submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm']
      ) ?>
    </div>
  <?php endif ?>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
