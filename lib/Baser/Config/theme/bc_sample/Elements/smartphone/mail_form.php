<?php
/**
 * メールフォーム（スマホ用）
 * 呼出箇所：メールフォーム入力ページ、メールフォーム入力内容確認ページ
 */
$prefix = '';
if (Configure::read('BcRequest.agent')) {
	$prefix = '/' . Configure::read('BcRequest.agentAlias');
}
?>


<script type="text/javascript">
	$(function(){
		$(".form-submit").click(function(){
			var mode = $(this).attr('id').replace('BtnMessage', '');
			$("#MessageMode").val(mode);
			return true;
		});
	});
</script>

<?php if (!$freezed): ?>
	<?php echo $this->Mailform->create('Message', array('url' => $prefix . '/' . $this->request->params['Content']['url'] . '/confirm', 'type' => 'file')) ?>
<?php else: ?>
	<?php echo $this->Mailform->create('Message', array('url' => $prefix . '/' . $this->request->params['Content']['url'] . '/submit')) ?>
<?php endif; ?>

<?php echo $this->Mailform->hidden('Message.mode') ?>

<section>
	<!-- /Elements/mail_input.php -->
	<?php $this->BcBaser->element('mail_input', array('blockStart' => 1)) ?>
</section>

<?php if ($mailContent['MailContent']['auth_captcha']): ?>
	<?php if (!$freezed): ?>
		<div class="auth-captcha clearfix">
			<?php $captchaId = mt_rand(0, 99999999) ?>
			<?php $this->BcBaser->img($prefix . '/' . $this->request->params['Content']['url'] . '/captcha/' . $captchaId, array('alt' => '認証画像', 'class' => 'auth-captcha-image')) ?>
			<?php echo $this->Mailform->text('Message.auth_captcha') ?><br>
			&nbsp;画像の文字を入力してください<br clear="all">
			<?php echo $this->Mailform->error('Message.auth_captcha', '入力された文字が間違っています。入力をやり直してください。') ?>
			<?php echo $this->Mailform->input('MailMessage.captcha_id', ['type' => 'hidden', 'value' => $captchaId]) ?>
		</div>
	<?php else: ?>
		<?php echo $this->Mailform->hidden('Message.auth_captcha') ?>
	<?php endif ?>
<?php endif ?>

<div class="submit">
	<?php if ($this->action == 'index'): ?>
		<input name="resetdata" value="　取り消す　" type="reset" class="btn-gray button">
	<?php endif; ?>
	<?php if ($freezed): ?>
		<?php echo $this->Mailform->submit('　書き直す　', array('div' => false, 'class' => 'btn-red button form-submit', 'id' => 'BtnMessageBack')) ?>
		<?php echo $this->Mailform->submit('　送信する　', array('div' => false, 'class' => 'btn-red button form-submit', 'id' => 'BtnMessageSubmit')) ?>
	<?php elseif ($this->action != 'submit'): ?>
		<?php echo $this->Mailform->submit('　入力内容を確認する　', array('div' => false, 'class' => 'btn-orange button form-submit', 'id' => 'BtnMessageConfirm')) ?>
	<?php endif; ?>
</div>

<?php echo $this->Mailform->end() ?>

