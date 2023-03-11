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
 * @var \BaserCore\Model\Entity\PasswordRequest $passwordRequest
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', 'パスワードのリセット'));
?>

<h2 class="bs-contents-title"><?php echo $this->BcBaser->getContentsTitle() ?></h2>

<div class="section">
  <p><?php echo __d('baser_core', 'パスワードを忘れた方は、登録されているメールアドレスを送信してください。<br />パスワードの再発行URLをメールでお知らせします。') ?></p>

  <?= $this->BcAdminForm->create($passwordRequest, ['novalidate' => true]) ?>
  <div class="submit">
    <p>
      <?php echo $this->BcAdminForm->control('email', ['type' => 'text', 'size' => '50', 'maxlength' => 255, 'placeholder' => 'yourname@example.com']) ?>
    </p>

    <?= $this->BcAdminForm->button(
      __d('baser_core', '送信'),
      ['div' => false,
        'class' => 'bs-button',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave']
    ) ?>
    <?php echo $this->BcAdminForm->error('email') ?>
  </div>
  <?= $this->BcAdminForm->end() ?>
</div>
<script>
  (function () {
    document.getElementById('email').focus();
  })();
</script>
