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
<?php /* フォーム開始タグ */ ?>
<?php if(!$freezed): ?>
<?php echo $mailform->create('Message', array('url' => $prefix.'/'.$mailContent['MailContent']['name'].'/confirm')) ?>
<?php else: ?>
<?php echo $mailform->create('Message', array('url' => $prefix.'/'.$mailContent['MailContent']['name'].'/submit')) ?>
<?php endif; ?>
<?php /* フォーム本体 */ ?>

<table cellpadding="0" cellspacing="0" class="row-table-01">
	<?php $bcBaser->element('mail_input', array('blockStart' => 1)) ?>
</table>

<?php if(!$freezed && $mailContent['MailContent']['auth_captcha']): ?>
<div class="auth-captcha clearfix">
	<?php $bcBaser->img($prefix.'/'.$mailContent['MailContent']['name'] . '/captcha', array('alt' => '認証画像', 'class' => 'auth-captcha-image')) ?>
	<?php echo $mailform->text('Message.auth_captcha') ?><br />
	&nbsp;画像の文字を入力してください<br clear="all" />
	<?php echo $mailform->error('Message.auth_captcha', '入力された文字が間違っています。入力をやり直してください。') ?>
</div>
<?php endif ?>

<?php /* 送信ボタン */ ?>
<div class="submit">
<?php if($this->action=='index'): ?>
	<input name="resetdata" value="　取り消す　" type="reset" class="btn-gray button" />
<?php endif; ?>
<?php if($freezed): ?>
	<?php echo $mailform->submit('　書き直す　', array('div' => false, 'class' => 'btn-red button', 'id' => 'MessageBack', 'name' => 'data[mode][back]'))  ?>
	<?php echo $mailform->submit('　送信する　', array('div' => false, 'class' => 'btn-red button', 'id' => 'MessageSubmit', 'name' => 'data[mode][submit]'))  ?>
<?php elseif($this->action != 'submit'): ?>
	<?php echo $mailform->submit('　入力内容を確認する　', array('div' => false, 'class' => 'btn-orange button', 'id' => 'MessageConfirm'))  ?>
<?php endif; ?>
</div>

<?php echo $mailform->end() ?>