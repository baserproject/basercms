<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			https://basercms.net/license/index.html
 */

/**
 * [ADMIN] パスワードリセット画面
 */
$userModel = Configure::read('BcAuthPrefix.' . $currentPrefix . '.userModel');
if (!$userModel) {
	$userModel = 'User';
}
$form_attr = ['action' => 'send_activate_url'];
if ($currentPrefix !== 'front') {
	$form_attr[$this->request->params['prefix']] = true;
}
?>
<div class="section">
	<p>
		<?= __d('baser', 'パスワードを忘れた方は、登録されているメールアドレスを送信してください。')?>
		<?= __d('baser', 'パスワードの再設定手順をメールでお知らせします。')?>
	</p>
	<?= $this->BcForm->create($userModel, ['url' => $form_attr]) ?>
	<div class="submit">
		<?= $this->BcForm->input(
			$userModel . '.email',
			['type' => 'text', 'size' => 34])
		?>
		<?= $this->BcForm->submit(
			__d('baser', '送信'),
			['div' => false, 'class' => 'btn-red button bca-btn', 'data-bca-btn-status' => 'warning'])
		?>
	</div>
	<?= $this->BcForm->end() ?>
</div>
