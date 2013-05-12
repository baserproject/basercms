<?php
/* SVN FILE: $Id: mail_input.ctp 250 2011-12-08 10:15:44Z arata $ */
/**
 * [MOBILE] メールフィールド
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
<?php $group_field=null ?>
<?php $iteration = 0; ?>
<?php if(!isset($blockEnd)) $blockEnd = 0 ?>
<?php foreach($mailFields as $key => $record): ?>
	<?php $iteration++; ?>
	<?php if($record['MailField']['use_field']): ?>
		<?php if($blockStart && $iteration >= $blockStart): ?>
			<?php if(!$blockEnd || $iteration <= $blockEnd): ?>
				<?php $next_key=$key+1 ?>
				<?php /* 項目名 */ ?>
				<?php if($group_field != $record['MailField']['group_field']  || (!$group_field && !$record['MailField']['group_field'])): ?>
					<?php $description=$record['MailField']['description'] ?>
<br />
<br />
<span style="color:#8ABE08">■</span> <?php echo $mailform->label("Message." . $record['MailField']['field_name'] . "", $record['MailField']['head']) ?>
					<?php if($record['MailField']['not_empty']): ?>
<font color="#FF0000">*</font>
					<?php endif; ?>
<br />
				<?php endif; ?>
				<?php /* 入力欄 */ ?>
				<?php if(!$freezed || $mailform->value("Message." . $record['MailField']['field_name'])): ?>
<font size="1"><?php echo $record['MailField']['before_attachment'] ?></font>
				<?php endif; ?>
					<?php if(!$record['MailField']['no_send'] || !$freezed): ?>
<?php echo $mailform->control($record['MailField']['type'], "Message." . $record['MailField']['field_name'] . "", $mailfield->getOptions($record), $mailfield->getAttributes($record)) ?>
				<?php endif; ?>
				<?php if(!$freezed): ?>
<font size="1"><?php echo $record['MailField']['attention'] ?></font>
				<?php endif; ?>
				<?php if(!$freezed || $mailform->value("Message." . $record['MailField']['field_name'])): ?>
<font size="1"><?php echo $record['MailField']['after_attachment'] ?></font>
				<?php endif; ?>
				<?php if(!$record['MailField']['group_valid']): ?>
					<?php if($mailform->error("Message." . $record['MailField']['field_name'] . "_format", "check")): ?>
<font color="#FF0000"><?php echo $mailform->error("Message." . $record['MailField']['field_name'] . "_format", "形式が不正です",array('wrap' => false)) ?></font>
					<?php else: ?>
<font color="#FF0000"><?php echo $mailform->error("Message." . $record['MailField']['field_name'] . "", "必須項目です",array('wrap' => false)) ?></font>
					<?php endif; ?>
				<?php endif; ?>
				<?php /* 説明欄 */ ?>
				<?php if (($bcArray->last($mailFields,$key)) ||
						($record['MailField']['group_field'] != $mailFields[$next_key]['MailField']['group_field']) ||
						(!$record['MailField']['group_field'] && !$mailFields[$next_key]['MailField']['group_field']) ||
						($record['MailField']['group_field'] != $mailFields[$next_key]['MailField']['group_field'] && $bcArray->first($mailFields,$key))): ?>
					<?php if($record['MailField']['group_valid']): ?>
						<?php if($mailform->error("Message." . $record['MailField']['group_field'] . "_format", "check")): ?>
<font color="#FF0000"><?php echo $mailform->error("Message." . $record['MailField']['group_field'] . "_format", "形式が不正です",array('wrap' => false)) ?></font>
						<?php else: ?>
							<?php if($record['MailField']['valid']) : ?>
<font color="#FF0000"><?php echo $mailform->error("Message." . $record['MailField']['group_field'] . "", "必須項目です",array('wrap' => false)) ?></font>
							<?php endif; ?>
						<?php endif; ?>
<font color="#FF0000"><?php echo $mailform->error("Message." . $record['MailField']['group_field'] . "_not_same", "入力データが一致していません",array('wrap' => false)) ?></font> 
<font color="#FF0000"><?php echo $mailform->error("Message." . $record['MailField']['group_field'] . "_not_complate", "入力データが不完全です",array('wrap' => false)) ?></font>
					<?php endif; ?>
					<?php if(!$freezed): ?>
<br />
<font size="1"><?php echo $description ?></font>
					<?php endif; ?>
				<?php endif; ?>
<?php $group_field=$record['MailField']['group_field'] ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>