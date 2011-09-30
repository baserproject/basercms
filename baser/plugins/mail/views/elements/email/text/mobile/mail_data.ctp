<?php
/* SVN FILE: $Id$ */
/**
 * [EMAIL] MOBILE メール送信データ
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php $group_field=null ?>
<?php foreach($mailFields as $fields): ?>
<?php if($group_field != $fields['MailField']['group_field']  || (!$group_field && !$fields['MailField']['group_field'])): ?>


◇◆ <?php echo $fields['MailField']['head'] ?> 
----------------------------
<?php endif ?>
<?php if(!empty($fields['MailField']['before_attachment']) && !empty($message[$fields['MailField']['field_name']])): ?>
<?php echo " ".$fields['MailField']['before_attachment'] ?>
<?php endif; ?>
<?php if(!empty($message[$fields['MailField']['field_name']]) && !$fields['MailField']['no_send']): ?>
<?php echo $maildata->control($fields['MailField']['type'],$message[$fields['MailField']['field_name']],$mailfield->getOptions($fields)) ?>
<?php endif; ?>
<?php if(!empty($fields['MailField']['after_attachment']) && !empty($message[$fields['MailField']['field_name']])): ?>
<?php echo " ".$fields['MailField']['after_attachment'] ?>
<?php endif; ?>
<?php $group_field=$fields['MailField']['group_field'] ?>
<?php endforeach; ?>