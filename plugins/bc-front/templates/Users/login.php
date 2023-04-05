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
 * ログインページ
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $isEnableLoginCredit
 * @var bool $savedEnable
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->setTitle(__d('baser_core', 'ログイン'));
?>


<div id="Login" class="bs-login">
  <div id="LoginInner">
    <?php $this->BcBaser->flash() ?>

    <h1 class="bs-login__title">
      <?php $this->BcBaser->contentsTitle() ?>
    </h1>
    <?= $this->BcAdminForm->create() ?>
    <div class="login-input bs-login-form-item">
      <?php echo $this->BcAdminForm->label('email', __d('baser_core', 'Eメール')) ?>
      <?= $this->BcAdminForm->control('email', [
        'type' => 'text',
        'tabindex' => 1,
        'autofocus' => true,
        'class' => 'bs-textbox__input',
      ]) ?>
    </div>
    <div class="login-input bs-login-form-item">
      <?php echo $this->BcAdminForm->label('password', __d('baser_core', 'パスワード')) ?>
      <?= $this->BcAdminForm->control('password', [
        'type' => 'password',
        'tabindex' => 2,
        'class' => 'bs-textbox__input',
      ]) ?>
    </div>
    <div class="submit bs-login-form-btn-group">
      <?= $this->BcAdminForm->button(__d('baser_core', 'ログイン'), [
        'type' => 'submit',
        'div' => false,
        'class' => 'bs-btn--login bs-btn',
        'data-bs-btn-type' => 'login',
        'id' => 'BtnLogin',
        'tabindex' => 4
      ]); ?>
    </div>
    <div class="clear login-etc bs-login-form-ctrl">
      <?php if ($savedEnable): ?>
        <div class="bs-login-form-checker">
          <?php echo $this->BcAdminForm->control('saved', [
            'type' => 'checkbox',
            'label' => __d('baser_core', 'ログイン状態を保存する'),
            'class' => 'bs-checkbox__input bs-login-form-checkbox ',
            'tabindex' => 3
          ]); ?>
        </div>
      <?php endif; ?>
      <div class="bs-login-forgot-pass">
        <?php $this->BcBaser->link(__d('baser_core', 'パスワードを忘れた場合はこちら'), ['controller' => 'password_requests', 'action' => 'entry', $this->request->getParam('prefix') => true]) ?>
      </div>
    </div>
    <?= $this->BcAdminForm->end() ?>
  </div>
</div>
