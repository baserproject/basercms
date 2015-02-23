<?php
/**
 * [MOBILE] メールフィールド
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

$group_field = null;
$iteration = 0;
if (!isset($blockEnd)) {
	$blockEnd = 0;
}
?>

<?php foreach ($mailFields as $key => $record): ?>
	<?php $iteration++; ?>
	<?php if ($record['MailField']['use_field']): ?>
		<?php if ($blockStart && $iteration >= $blockStart): ?>
			<?php if (!$blockEnd || $iteration <= $blockEnd): ?>
				<?php $next_key = $key + 1 ?>
				<?php /* 項目名 */ ?>
				<?php $description = $record['MailField']['description'] ?>
				<?php if ($group_field != $record['MailField']['group_field'] || (!$group_field && !$record['MailField']['group_field'])): ?>
					<br />
					<br />
					<span style="color:#8ABE08">■</span> <?php echo $this->Mailform->label("Message." . $record['MailField']['field_name'] . "", $record['MailField']['head']) ?>
					<?php if ($record['MailField']['not_empty']): ?>
						<font color="#FF0000">*</font>
					<?php endif; ?>
					<br />
				<?php endif; ?>
					
				<?php if (!$freezed): ?>
					<font size="1"><?php echo $description ?></font>
				<?php endif; ?>
				<?php /* 入力欄 */ ?>
				<?php if (!$freezed || $this->Mailform->value("Message." . $record['MailField']['field_name'])): ?>
					<font size="1"><?php echo $record['MailField']['before_attachment'] ?></font>
				<?php endif; ?>
				
				<?php echo $this->Mailform->control($record['MailField']['type'], "Message." . $record['MailField']['field_name'] . "", $this->Mailfield->getOptions($record), $this->Mailfield->getAttributes($record)) ?>
					
				<?php if (!$freezed || $this->Mailform->value("Message." . $record['MailField']['field_name'])): ?>
					<font size="1"><?php echo $record['MailField']['after_attachment'] ?></font>
				<?php endif; ?>
				<?php if (!$freezed): ?>
					<font size="1"><?php echo $record['MailField']['attention'] ?></font>
				<?php endif; ?>
				<?php if (!$record['MailField']['group_valid']): ?>
					<?php if ($this->Mailform->error("Message." . $record['MailField']['field_name'] . "_format", "check")): ?>
						<font color="#FF0000"><?php echo $this->Mailform->error("Message." . $record['MailField']['field_name'] . "_format", "形式が不正です", array('wrap' => false)); ?></font>
					<?php else: ?>
						<font color="#FF0000"><?php echo $this->Mailform->error("Message." . $record['MailField']['field_name'] . "", "必須項目です", array('wrap' => false)); ?></font>
					<?php endif; ?>
				<?php endif; ?>
				<?php /* 説明欄 */ ?>
				<?php if (($this->BcArray->last($mailFields, $key)) ||
					($record['MailField']['group_field'] != $mailFields[$next_key]['MailField']['group_field']) ||
					(!$record['MailField']['group_field'] && !$mailFields[$next_key]['MailField']['group_field']) ||
					($record['MailField']['group_field'] != $mailFields[$next_key]['MailField']['group_field'] && $this->BcArray->first($mailFields, $key))): ?>
					<?php if ($record['MailField']['group_valid']): ?>
						<?php if ($this->Mailform->error("Message." . $record['MailField']['group_field'] . "_format", "check")): ?>
							<font color="#FF0000"><?php echo $this->Mailform->error("Message." . $record['MailField']['group_field'] . "_format", "形式が不正です", array('wrap' => false)) ?></font>
						<?php else: ?>
							<?php if ($record['MailField']['valid']) : ?>
								<font color="#FF0000"><?php echo $this->Mailform->error("Message." . $record['MailField']['group_field'] . "", "必須項目です", array('wrap' => false)) ?></font>
							<?php endif; ?>
						<?php endif; ?>
						<font color="#FF0000"><?php echo $this->Mailform->error("Message." . $record['MailField']['group_field'] . "_not_same", "入力データが一致していません", array('wrap' => false)) ?></font> 
						<font color="#FF0000"><?php echo $this->Mailform->error("Message." . $record['MailField']['group_field'] . "_not_complate", "入力データが不完全です", array('wrap' => false)) ?></font>
					<?php endif; ?>
				<?php endif; ?>
				<?php $group_field = $record['MailField']['group_field'] ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>