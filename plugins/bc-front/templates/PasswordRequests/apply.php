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
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\User $user
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', 'パスワードのリセット'));
?>

<h2 class="bs-contents-title"><?php echo $this->BcBaser->getContentsTitle() ?></h2>

<div class="section">
  <p><?php echo __d('baser_core', '新しいパスワードを入力してください。') ?></p>

  <?= $this->BcForm->create($user, ['novalidate' => true]) ?>
  <div class="submit">
    <p>
      <?php echo $this->BcForm->control('password_1', [
        'type' => 'password',
        'size' => '50',
        'maxlength' => 255,
        'placeholder' => __d('baser_core', 'パスワード'),
        'class' => 'bs-textbox__input'
      ]) ?>
      <?php echo $this->BcForm->control('password_2', [
        'type' => 'password',
        'size' => '50',
        'maxlength' => 255,
        'placeholder' => __d('baser_core', '再入力'),
        'class' => 'bs-textbox__input'
      ]) ?>
      <?php echo $this->BcForm->error('password') ?>
    </p>

    <?= $this->BcForm->button(
      __d('baser_core', '保存'),
      ['div' => false,
        'class' => 'bs-button',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave']
    ) ?>
    <?php echo $this->BcForm->error('email') ?>
  </div>
  <?= $this->BcForm->end() ?>
</div>
<script>
  (function () {
    document.getElementById('email').focus();
  })();
</script>
