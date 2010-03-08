<?php
/* SVN FILE: $Id$ */
/**
 * [モバイル] メールフィールド
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
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
<?php $iteration = 0; ?>
<?php if(!isset($blockEnd))$blockEnd = 0 ?>
<?php foreach($mailFields as $key => $record): ?>
	<?php $iteration++; ?>
	<?php if($record['MailField']['use_field']): ?>

		<?php if($blockStart && $iteration >= $blockStart): ?>
		
			<?php if(!$blockEnd || $iteration <= $blockEnd): ?> 
		
				<?php $next_key=$key+1 ?>
				<?php /* 項目名 */ ?>
				<?php if($group_field != $record['MailField']['group_field']  || (!$group_field && !$record['MailField']['group_field'])): ?>
				<?php $description=$record['MailField']['description'] ?>
					<br /><br /><span style="color:#8ABE08">◆</span><?php echo $form->label("Message." . $record['MailField']['field_name'] . "", $record['MailField']['head']) ?>
					<?php if($record['MailField']['not_empty']): ?><font color="#FF0000">*</font><?php endif; ?><br />
				<?php endif; ?>
				
				<?php /* 入力欄 */ ?>
				<?php if(!$freezed || $mailform->value("Message." . $record['MailField']['field_name'])): ?>
				<font size="1"><?php echo $record['MailField']['before_attachment'] ?></font>
				<?php endif; ?>
				<?php if(!$record['MailField']['no_send'] || !$freezed): ?>
				<?php echo $mailform->control($record['MailField']['type'], "Message." . $record['MailField']['field_name'] . "", $mailfield->getOptions($record), $mailfield->getAttributes($record)) ?>
				<?php endif; ?>
				<?php if(!$freezed): ?><font size="1"><?php echo $record['MailField']['attention'] ?></font><?php endif; ?> 
				<?php if(!$freezed || $mailform->value("Message." . $record['MailField']['field_name'])): ?>
				<font size="1"><?php echo $record['MailField']['after_attachment'] ?></font>
				<?php endif; ?>
					<?php if(!$record['MailField']['group_valid']): ?>
						<?php if($form->error("Message." . $record['MailField']['field_name'] . "_format", "check")): ?>
							<font color="#FF0000"><?php echo $form->error("Message." . $record['MailField']['field_name'] . "_format", ">> 形式が不正です",array('wrap' => false)) ?></font>
						<?php else: ?>
							<font color="#FF0000"><?php echo $form->error("Message." . $record['MailField']['field_name'] . "", ">> 必須項目です",array('wrap' => false)) ?></font>
						<?php endif; ?>
					<?php endif; ?>
				
				<?php /* 説明欄 */ ?>
				<?php if (($array->last($mailFields,$record)) ||
						  ($record['MailField']['group_field'] != $mailFields[$next_key]['MailField']['group_field']) ||
						  (!$record['MailField']['group_field'] && !$mailFields[$next_key]['MailField']['group_field']) || 
						  ($record['MailField']['group_field'] != $mailFields[$next_key]['MailField']['group_field'] && $array->first($mailFields,$record))): ?>
						
					<?php if($record['MailField']['group_valid']): ?>
						<?php if($form->error("Message." . $record['MailField']['group_field'] . "_format", "check")): ?>
							<font color="#FF0000"><?php echo $form->error("Message." . $record['MailField']['group_field'] . "_format", ">> 形式が不正です",array('wrap' => false)) ?></font>
						<?php else: ?>
							<font color="#FF0000"><?php echo $form->error("Message." . $record['MailField']['group_field'] . "", ">> 必須項目です",array('wrap' => false)) ?></font>
						<?php endif; ?>
						<font color="#FF0000"><?php echo $form->error("Message." . $record['MailField']['group_field'] . "_not_same", ">> 入力データが一致していません",array('wrap' => false)) ?></font>
						<font color="#FF0000"><?php echo $form->error("Message." . $record['MailField']['group_field'] . "_not_complate", ">> 入力データが不完全です",array('wrap' => false)) ?></font>
					<?php endif; ?>
					<?php if(!$freezed): ?><br /><font size="1"><?php echo $description ?></font><?php endif; ?>
				<?php endif; ?>
			
				<?php $group_field=$record['MailField']['group_field'] ?>
			
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>