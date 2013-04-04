<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] メールフォーム本体
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
?>
<?php /* フォーム開始タグ */ ?>
<?php if(!$freezed): ?>
<?php echo $mailform->create(null, array('controller' => $mailContent['MailContent']['name'], 'action' => 'confirm')) ?>
<?php else: ?>
<?php echo $mailform->create(null, array('controller' => $mailContent['MailContent']['name'], 'action' => 'submit')) ?>
<?php endif; ?>
<?php /* フォーム本体 */ ?>
<?php echo $bcBaser->element('mail_input',array('blockStart'=>1)) ?>

<br />
<br />
<?php /* 送信ボタン */ ?>
<?php if($freezed): ?>
<center>
	<?php echo $mailform->hidden('Message.mode', array('value' => 'Submit')) ?>
	<?php echo $mailform->submit('　送信する　', array('class' => 'button'))  ?>
</center>
<?php elseif($this->action != 'submit'): ?>
<center>
	<?php echo $mailform->hidden('Message.mode', array('value' => 'Confirm')) ?>
	<?php echo $mailform->submit('　入力内容を確認する　', array("class"=>"button"))  ?>
</center>
<?php endif; ?>

<?php echo $mailform->end() ?>