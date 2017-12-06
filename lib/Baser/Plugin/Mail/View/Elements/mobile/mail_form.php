<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [MOBILE] メールフォーム本体
 */
?>


<?php /* フォーム開始タグ */ ?>
<?php if (!$freezed): ?>
	<?php echo $this->Mailform->create(null, ['url' => $this->BcBaser->getContentsUrl() . 'confirm']) ?>
<?php else: ?>
	<?php echo $this->Mailform->create(null, ['url' => $this->BcBaser->getContentsUrl() . 'submit']) ?>
<?php endif; ?>

<?php $this->Mailform->unlockField('MailMessage.mode') ?>

<?php /* フォーム本体 */ ?>
<?php echo $this->BcBaser->element('mail_input', ['blockStart' => 1]) ?>

<br />
<br />
<?php /* 送信ボタン */ ?>
<?php if ($freezed): ?>
	<center>
		<?php echo $this->Mailform->hidden('MailMessage.mode', ['value' => 'Submit']) ?>
		<?php echo $this->Mailform->submit('　送信する　', ['class' => 'button']) ?>
	</center>
	<?php else: ?>
	<center>
		<?php echo $this->Mailform->hidden('MailMessage.mode', ['value' => 'Confirm']) ?>
		<?php echo $this->Mailform->submit('　入力内容を確認する　', ["class" => "button"]) ?>
	</center>
<?php endif; ?>

<?php echo $this->Mailform->end() ?>