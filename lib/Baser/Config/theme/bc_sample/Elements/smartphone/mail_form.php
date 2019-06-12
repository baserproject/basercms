<?php
/**
 * メールフォーム（スマホ用）
 * 呼出箇所：メールフォーム入力ページ、メールフォーム入力内容確認ページ
 */
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
	<?php echo $this->Mailform->create('MailMessage', array('url' => $this->BcBaser->getContentsUrl(null, false, null, false) . 'submit')) ?>
<?php endif; ?>

<?php echo $this->Mailform->hidden('MailMessage.mode') ?>

<section>
	<!-- /Elements/mail_input.php -->
	<?php $this->BcBaser->element('mail_input', array('blockStart' => 1)) ?>
</section>

<?php if ($mailContent['MailContent']['auth_captcha']): ?>
	<?php if (!$freezed): ?>
		<div class="auth-captcha clearfix">
			<?php echo $this->Mailform->authCaptcha('MailMessage.auth_captcha') ?>
			<br>
			&nbsp;<?php echo __('画像の文字を入力してください') ?><br clear="all">
			<?php echo $this->Mailform->error('MailMessage.auth_captcha', __('入力された文字が間違っています。入力をやり直してください。')) ?>
		</div>
	<?php else: ?>
		<?php echo $this->Mailform->hidden('MailMessage.auth_captcha') ?>
		<?php echo $this->Mailform->hidden('MailMessage.captcha_id') ?>
	<?php endif ?>
<?php endif ?>

<div class="submit">
	<?php if ($this->action == 'index'): ?>
		<input name="resetdata" value="　取り消す　" type="reset" class="btn-gray button">
	<?php endif; ?>
	<?php if ($freezed): ?>
		<?php echo $this->Mailform->submit('　' . __('書き直す') . '　', array('div' => false, 'class' => 'btn-red button form-submit', 'id' => 'BtnMessageBack')) ?>
		<?php echo $this->Mailform->submit('　' . __('送信する') . '　', array('div' => false, 'class' => 'btn-red button form-submit', 'id' => 'BtnMessageSubmit')) ?>
	<?php else: ?>
		<?php echo $this->Mailform->submit('　' . __('入力内容を確認する') . '　', array('div' => false, 'class' => 'btn-orange button form-submit', 'id' => 'BtnMessageConfirm')) ?>
	<?php endif; ?>
</div>

<?php echo $this->Mailform->end() ?>

