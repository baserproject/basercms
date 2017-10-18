<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ログイン
 *
 * @var BcAppView $this
 */
echo $this->Flash->render('auth');
$userModel = Configure::read('BcAuthPrefix.' . $currentPrefix . '.userModel');
if (!$userModel) {
	$userModel = 'User';
}
list(, $userModel) = pluginSplit($userModel);
$userController = Inflector::tableize($userModel);
$this->BcBaser->css('admin/login', ['inline' => false]);
$this->BcBaser->js('admin/users/login', false);
?>


<div id="UserModel" style="display:none"><?php echo $userModel ?></div>
<div id="LoginCredit" style="display:none"><?php echo $this->BcBaser->siteConfig['login_credit'] ?></div>
<div id="Login">

	<div id="LoginInner">
		<?php $this->BcBaser->flash() ?>
		<h1><?php $this->BcBaser->contentsTitle() ?></h1>
		<div id="AlertMessage" class="message" style="display:none"></div>
		<?php echo $this->BcForm->create($userModel, ['url' => ['action' => 'login']]) ?>
		<div class="float-left login-input">
			<?php echo $this->BcForm->label($userModel . '.name', 'アカウント名') ?>
			<?php echo $this->BcForm->input($userModel . '.name', array('type' => 'text', 'size' => 16, 'tabindex' => 1, 'autofocus' => true)) ?>
		</div>
		<div class="float-left login-input">
			<?php echo $this->BcForm->label($userModel . '.password', 'パスワード') ?>
			<?php echo $this->BcForm->input($userModel . '.password', array('type' => 'password', 'size' => 16, 'tabindex' => 2)) ?>
		</div>
		<div class="float-left submit">
			<?php echo $this->BcForm->submit('ログイン', array('div' => false, 'class' => 'btn-red button', 'id' => 'BtnLogin', 'tabindex' => 4)) ?>
		</div>
		<div class="clear login-etc">
			<?php echo $this->BcForm->input($userModel . '.saved', array('type' => 'checkbox', 'label' => 'ログイン状態を保存する', 'tabindex' => 3)) ?>　
			<?php if ($currentPrefix == 'front'): ?>
				<?php $this->BcBaser->link('パスワードを忘れた場合はこちら', array('action' => 'reset_password'), array('rel' => 'popup')) ?>
			<?php else: ?>
				<?php $this->BcBaser->link('パスワードを忘れた場合はこちら', array('action' => 'reset_password', $this->request->params['prefix'] => true), array('rel' => 'popup')) ?>
			<?php endif ?>
		</div>
		<?php echo $this->BcForm->end() ?>
	</div>

</div>
