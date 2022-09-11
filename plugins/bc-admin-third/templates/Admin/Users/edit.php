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

use BaserCore\View\BcAdminAppView;
use BaserCore\Model\Entity\User;

/**
 * Users Edit
 * @var BcAdminAppView $this
 * @var User $user
 * @var bool $isDeletable
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
  <?php if ($isDeletable): ?>
    <div class="bca-actions__sub">
      <?= $this->BcAdminForm->postLink(
        __d('baser', '削除'),
        ['action' => 'delete', $user->id],
        ['block' => true,
          'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $user->name),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm']
      ) ?>
    </div>
  <?php endif ?>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
