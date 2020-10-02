<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.4.0
 * @license			https://basercms.net/license/index.html
 */

/**
 * メールフォーム
 *
 * @var BcAppView $this
 * @var bool $freezed 確認画面かどうか
 * @var array $mailContent メールコンテンツデータ
 */
// ブラウザのヒストリーバック（戻るボタン）対応
$this->Mail->token();
?>


<script type="text/javascript">
	$(function(){
		$(".form-submit").click(function(){
			var mode = $(this).attr('id').replace('BtnMessage', '');
			$("#MailMessageMode").val(mode);
			return true;
		});
	});
</script>


<?php if (!$freezed): ?>
	<?php echo $this->Mailform->create('MailMessage', array('url' => $this->BcBaser->getContentsUrl(null, false, null, false) . 'confirm', 'type' => 'file')) ?>
<?php else: ?>
	<?php echo $this->Mailform->create('MailMessage', array('url' => $this->BcBaser->getContentsUrl(null, false, null, false)  . 'submit')) ?>
<?php endif; ?>

<?php $this->Mailform->unlockField('MailMessage.mode') ?>
<?php echo $this->Mailform->hidden('MailMessage.mode') ?>

<table class="bs-mail-form">
	<?php $this->BcBaser->element('mail_input', array('blockStart' => 1)) ?>
</table>

<?php if ($mailContent['MailContent']['auth_captcha']): ?>
	<?php if (!$freezed): ?>
		<div class="bs-mail-form__auth-captcha">
			<?php echo $this->Mailform->authCaptcha('MailMessage.auth_captcha') ?>
			<br />
			&nbsp;<?php echo __('画像の文字を入力してください') ?><br clear="all" />
			<?php echo $this->Mailform->error('MailMessage.auth_captcha', __('入力された文字が間違っています。入力をやり直してください。')) ?>
		</div>
	<?php else: ?>
		<?php echo $this->Mailform->hidden('MailMessage.auth_captcha') ?>
		<?php echo $this->Mailform->hidden('MailMessage.captcha_id') ?>
	<?php endif ?>
<?php endif ?>

<div class="bs-mail-form__submit">
	<?php if ($freezed): ?>
		<?php echo $this->Mailform->submit('　' . __('書き直す') . '　', ['div' => false, 'class' => 'button', 'id' => 'BtnMessageBack']) ?>
		<?php echo $this->Mailform->submit('　' . __('送信する') . '　', ['div' => false, 'class' => 'button', 'id' => 'BtnMessageSubmit']) ?>
	<?php else: ?>
		<input name="resetdata" value="　取り消す　" type="reset" class="button" />
		<?php echo $this->Mailform->submit('　' . __('入力内容を確認する') . '　', ['div' => false, 'class' => 'button', 'id' => 'BtnMessageConfirm']) ?>
	<?php endif; ?>
</div>

<?php echo $this->Mailform->end() ?>
