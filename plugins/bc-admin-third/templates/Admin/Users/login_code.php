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

use BaserCore\View\AppView;

$this->BcAdmin->setTitle(__d('baser_core', '認証コード入力'));
?>


<div id="Login" class="bca-login">
  <div id="LoginInner">
    <?php $this->BcBaser->flash() ?>

    <h1 class="bca-login__title">
      <?php echo $this->BcBaser->getImg('admin/logo_large.png', ['alt' => $this->BcBaser->getContentsTitle(), 'class' => 'bca-login__logo']) ?>
    </h1>
    <?= $this->BcAdminForm->create() ?>
    <div class="login-input bca-login-form-item">
      <?php echo $this->BcAdminForm->label('email', __d('baser_core', '認証コード')) ?>
      <?= $this->BcAdminForm->control('code', ['type' => 'text', 'autofocus' => true]) ?>
    </div>
    <div class="submit bca-login-form-btn-group">
      <?= $this->BcAdminForm->button(__d('baser_core', 'ログイン'), [
        'type' => 'submit',
        'div' => false,
        'class' => 'bca-btn--login bca-btn',
        'data-bca-btn-type' => 'login',
        'id' => 'BtnLogin',
      ]); ?>
      <div class="bca-login-code-resend">
        <?= $this->BcAdminForm->button(__d('baser_core', '認証コード再送付'), [
          'type' => 'submit',
          'name' => 'resend',
          'value' => 1,
          'div' => false,
          'class' => 'bca-btn',
        ]); ?>
      </div>
    </div>
    <?= $this->BcAdminForm->end() ?>
  </div>
</div>
