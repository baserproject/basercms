<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] メールフォーム本体
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
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
<?php echo $baser->element('mail_input',array('blockStart'=>1)) ?>

<br />
<br />
<?php /* 送信ボタン */ ?>
<?php if($freezed): ?>
<center>
	<?php echo $mailform->submit('　送信する　', array("class"=>"btn-red button")) ?>
</center>
<?php elseif($this->action != 'submit'): ?>
<center>
	<?php echo $mailform->submit('　入力内容を確認する　', array("class"=>"btn-orange button"))  ?>
</center>
<?php endif; ?>

<?php echo $mailform->end() ?>