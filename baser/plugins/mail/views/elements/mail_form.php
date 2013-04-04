<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] フォーム
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$prefix = '';
if(Configure::read('BcRequest.agent')) {
	$prefix = '/'.Configure::read('BcRequest.agentAlias');
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

<?php /* フォーム開始タグ */ ?>
<?php if(!$freezed): ?>
<?php echo $mailform->create('Message', array('url' => $prefix.'/'.$mailContent['MailContent']['name'].'/confirm')) ?>
<?php else: ?>
<?php echo $mailform->create('Message', array('url' => $prefix.'/'.$mailContent['MailContent']['name'].'/submit')) ?>
<?php endif; ?>
<?php /* フォーム本体 */ ?>

<?php echo $mailform->hidden('Message.mode') ?>

<table cellpadding="0" cellspacing="0" class="row-table-01">
	<?php $bcBaser->element('mail_input', array('blockStart' => 1)) ?>
</table>

<?php if($mailContent['MailContent']['auth_captcha']): ?>
	<?php if(!$freezed): ?>
<div class="auth-captcha clearfix">
	<?php $bcBaser->img($prefix.'/'.$mailContent['MailContent']['name'] . '/captcha', array('alt' => '認証画像', 'class' => 'auth-captcha-image')) ?>
	<?php echo $mailform->text('Message.auth_captcha') ?><br />
	&nbsp;画像の文字を入力してください<br clear="all" />
	<?php echo $mailform->error('Message.auth_captcha', '入力された文字が間違っています。入力をやり直してください。') ?>
</div>
	<?php else: ?>
<?php echo $mailform->hidden('Message.auth_captcha') ?>
	<?php endif ?>
<?php endif ?>

<?php /* 送信ボタン */ ?>
<div class="submit">
<?php if($this->action=='index'): ?>
	<input name="resetdata" value="　取り消す　" type="reset" class="btn-gray button" />
<?php endif; ?>
<?php if($freezed): ?>
	<?php echo $mailform->submit('　書き直す　', array('div' => false, 'class' => 'btn-red button form-submit', 'id' => 'BtnMessageBack'))  ?>
	<?php echo $mailform->submit('　送信する　', array('div' => false, 'class' => 'btn-red button form-submit', 'id' => 'BtnMessageSubmit'))  ?>
<?php elseif($this->action != 'submit'): ?>
	<?php echo $mailform->submit('　入力内容を確認する　', array('div' => false, 'class' => 'btn-orange button form-submit', 'id' => 'BtnMessageConfirm'))  ?>
<?php endif; ?>
</div>

<?php echo $mailform->end() ?>