<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] パスワードリセット画面
 */
$userModel = Configure::read('BcAuthPrefix.' . $currentPrefix . '.userModel');
if (!$userModel) {
	$userModel = 'User';
}
?>


<div class="section">
	<p><?php echo __d('baser', 'パスワードを忘れた方は、登録されているメールアドレスを送信してください。<br />新しいパスワードをメールでお知らせします。') ?></p>
	<?php if ($currentPrefix == 'front'): ?>
		<?php echo $this->BcForm->create($userModel, ['url' => ['action' => 'reset_password']]) ?>
	<?php else: ?>
		<?php echo $this->BcForm->create($userModel, ['url' => ['action' => 'reset_password', $this->request->params['prefix'] => true]]) ?>
	<?php endif ?>
	<div class="submit">
		<?php echo $this->BcForm->input($userModel . '.email', ['type' => 'text', 'size' => 60]) ?>
		<?php echo $this->BcForm->submit(__d('baser', '送信'), ['div' => false, 'class' => 'btn-red button']) ?>
	</div>
	<?php echo $this->BcForm->end() ?>
</div>
