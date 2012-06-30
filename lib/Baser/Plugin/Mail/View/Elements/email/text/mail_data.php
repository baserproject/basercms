<?php
/* SVN FILE: $Id$ */
/**
 * [EMAIL] メール送信データ
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$group_field = null;
foreach($mailFields as $field) {
	$field = $field['MailField'];
	if( $field['use_field'] && isset($message[$field['field_name']]) &&  ($group_field != $field['group_field']  || (!$group_field && !$field['group_field']))):
?>


◇◆ <?php echo $field['head'] ?> 
----------------------------------------
<?php
	endif;
	if(!empty($field['before_attachment']) && isset($message[$field['field_name']])){
		echo " ".$field['before_attachment'];
	}
	if(isset($message[$field['field_name']]) && !$field['no_send'] && $field['use_field']){
		echo $maildata->control($field['type'],$message[$field['field_name']],$mailfield->getOptions($field));
	}
	if(!empty($field['after_attachment']) && isset($message[$field['field_name']])){
		echo " ".$field['after_attachment'];
	}
	$group_field = $field['group_field'];
}
?>