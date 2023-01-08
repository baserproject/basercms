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

/**
 * login
 * @var AppView $this
 * @var string $isEnableLoginCredit
 * @var bool $savedEnable
 * @checked
 * @noTodo
 * @unitTest
 */

$this->BcAdmin->setTitle(__d('baser', 'ログイン'));
$this->BcBaser->js('admin/users/login.bundle', false, [
  'id' => 'AdminUsersLoginScript',
  'data-isEnableLoginCredit' => $isEnableLoginCredit
]);
?>


<div id="Login" class="bca-login">
  <div id="LoginInner">
    <?php $this->BcBaser->flash() ?>

    <h1 class="bca-login__title">
      <?php echo $this->BcBaser->getImg('admin/logo_large.png', ['alt' => $this->BcBaser->getContentsTitle(), 'class' => 'bca-login__logo']) ?>
    </h1>
    <?= $this->BcAdminForm->create() ?>
    <div class="login-input bca-login-form-item">
      <?php echo $this->BcAdminForm->label('email', __d('baser', 'Eメール')) ?>
      <?= $this->BcAdminForm->control('email', ['type' => 'text', 'tabindex' => 1, 'autofocus' => true]) ?>
    </div>
    <div class="login-input bca-login-form-item">
      <?php echo $this->BcAdminForm->label('password', __d('baser', 'パスワード')) ?>
      <?= $this->BcAdminForm->control('password', ['type' => 'password', 'tabindex' => 2]) ?>
    </div>
    <div class="submit bca-login-form-btn-group">
      <?= $this->BcAdminForm->button(__d('baser', 'ログイン'), [
        'type' => 'submit',
        'div' => false,
        'class' => 'bca-btn--login bca-btn',
        'data-bca-btn-type' => 'login',
        'id' => 'BtnLogin',
        'tabindex' => 4
      ]); ?>
    </div>
    <div class="clear login-etc bca-login-form-ctrl">
      <?php if ($savedEnable): ?>
        <div class="bca-login-form-checker">
          <?php echo $this->BcAdminForm->control('saved', [
            'type' => 'checkbox',
            'label' => __d('baser', 'ログイン状態を保存する'),
            'class' => 'bca-checkbox__input bca-login-form-checkbox ',
            'tabindex' => 3
          ]); ?>
        </div>
      <?php endif; ?>
      <div class="bca-login-forgot-pass">
        <?php $this->BcBaser->link(__d('baser', 'パスワードを忘れた場合はこちら'), ['controller' => 'password_requests', 'action' => 'entry', $this->request->getParam('prefix') => true]) ?>
      </div>
    </div>
    <?= $this->BcAdminForm->end() ?>
  </div>
</div>
