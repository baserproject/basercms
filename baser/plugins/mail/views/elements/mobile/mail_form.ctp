<?php
/* SVN FILE: $Id$ */
/**
 * [モバイル] メールフォーム本体
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
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
    <?php echo $form->create(null,array('plugin'=>'aaa','controller'=>$mailContent['MailContent']['name'],'action'=>'confirm')) ?>
<?php else: ?>
    <?php echo $form->create(null,array('plugin'=>'aaa','controller'=>$mailContent['MailContent']['name'],'action'=>'submit')) ?>
<?php endif; ?>


<?php /* フォーム本体 */ ?>
<?php echo $baser->element('mail_input',array('blockStart'=>1)) ?>

<br /><br />
<?php /* 送信ボタン */ ?>
<?php if($freezed): ?>
    <center><?php echo $form->end(array('label'=>'　送信する　','div'=>false, "class"=>"btn-red button"))  ?></center>
<?php elseif($this->action != 'submit'): ?>
    <center><?php echo $form->end(array('label'=>'　入力内容を確認する　','div'=>false, "class"=>"btn-orange button"))  ?></center>
<?php endif; ?>