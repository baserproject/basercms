<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
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
			<?php echo $this->BcForm->input($userModel . '.name', ['type' => 'text', 'size' => 16, 'tabindex' => 1, 'autofocus' => true, 'placeholder' => __d('baser', 'アカウント名')]) ?>
		</div>
		<div class="float-left login-input">
			<?php echo $this->BcForm->input($userModel . '.password', ['type' => 'password', 'size' => 16, 'tabindex' => 2, 'placeholder' => __d('baser', 'パスワード')]) ?>
		</div>
		<div class="float-left submit">
			<?php echo $this->BcForm->submit(__d('baser', 'ログイン'), ['div' => false, 'class' => 'btn-red button', 'id' => 'BtnLogin', 'tabindex' => 4]) ?>
		</div>
		<div class="clear login-etc">
			<?php echo $this->BcForm->input($userModel . '.saved', ['type' => 'checkbox', 'label' => __d('baser', 'ログイン状態を保存する'), 'tabindex' => 3]) ?>
			　
			<?php if ($currentPrefix == 'front'): ?>
				<?php $this->BcBaser->link(__d('baser', 'パスワードを忘れた場合はこちら'), ['action' => 'send_activate_url'], ['rel' => 'popup']) ?>
			<?php else: ?>
				<?php $this->BcBaser->link(__d('baser', 'パスワードを忘れた場合はこちら'), ['action' => 'send_activate_url', $this->request->params['prefix'] => true], ['rel' => 'popup']) ?>
			<?php endif ?>
		</div>
		<?php echo $this->BcForm->end() ?>
	</div>

</div>
