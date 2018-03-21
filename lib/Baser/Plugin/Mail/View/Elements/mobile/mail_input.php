<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [MOBILE] メールフィールド
 * @var \BcAppView $this
 */
$group_field = null;
$iteration = 0;
if (!isset($blockEnd)) {
	$blockEnd = 0;
}
?>

<?php foreach ($mailFields as $key => $record): ?>
    <?php
    $field = $record['MailField'];
	$iteration++;
	?>
	<?php if ($field['use_field']): ?>
		<?php if ($blockStart && $iteration >= $blockStart): ?>
			<?php if (!$blockEnd || $iteration <= $blockEnd): ?>
				<?php $next_key = $key + 1 ?>
				<?php /* 項目名 */ ?>
				<?php $description = $field['description'] ?>
				<?php if ($group_field != $field['group_field'] || (!$group_field && !$field['group_field'])): ?>
					<br />
					<br />
					<span style="color:#8ABE08">■</span> <?php echo $this->Mailform->label("MailMessage." . $field['field_name'] . "", $field['head']) ?>
					<?php if ($field['not_empty']): ?>
						<font color="#FF0000">*</font>
					<?php endif; ?>
					<br />
				<?php endif; ?>
					
				<?php if (!$freezed): ?>
					<font size="1"><?php echo $description ?></font>
				<?php endif; ?>
				<?php /* 入力欄 */ ?>
				<?php if (!$freezed || $this->Mailform->value("MailMessage." . $field['field_name'])): ?>
					<font size="1"><?php echo $field['before_attachment'] ?></font>
				<?php endif; ?>

				<?php
				// =========================================================================================================
				// 2018/02/06 ryuring
				// no_send オプションは、確認画面に表示しないようにするために利用されている可能性が高い
				//（メールアドレスのダブル入力、プライバシーポリシーへの同意に利用されている）
				// 本来であれば、not_display_confirm 等のオプションを別途準備し、そちらを利用するべきだが、
				// 後方互換のため残す
				// =========================================================================================================
				if ($freezed && $field['no_send']) {
					echo $this->Mailform->control('hidden', "MailMessage." . $field['field_name'] . "", $this->Mailfield->getOptions($record), $this->Mailfield->getAttributes($record));
				} else {
					echo $this->Mailform->control($field['type'], "MailMessage." . $field['field_name'] . "", $this->Mailfield->getOptions($record), $this->Mailfield->getAttributes($record));
				}
				?>
					
				<?php if (!$freezed || $this->Mailform->value("MailMessage." . $field['field_name'])): ?>
					<font size="1"><?php echo $field['after_attachment'] ?></font>
				<?php endif; ?>
				<?php if (!$freezed): ?>
					<font size="1"><?php echo $field['attention'] ?></font>
				<?php endif; ?>
				<?php if (!$field['group_valid']): ?>
					<?php if ($this->Mailform->error("MailMessage." . $field['field_name'] . "_format", "check")): ?>
						<font color="#FF0000"><?php echo $this->Mailform->error("MailMessage." . $field['field_name'] . "_format", __("形式が無効です。"), array('wrap' => false)); ?></font>
					<?php else: ?>
						<font color="#FF0000"><?php echo $this->Mailform->error("MailMessage." . $field['field_name'] . "", __("必須項目です。"), array('wrap' => false)); ?></font>
					<?php endif; ?>
				<?php endif; ?>
				<?php /* 説明欄 */ ?>
				<?php if (($this->BcArray->last($mailFields, $key)) ||
					($field['group_field'] != $mailFields[$next_key]['MailField']['group_field']) ||
					(!$field['group_field'] && !$mailFields[$next_key]['MailField']['group_field']) ||
					($field['group_field'] != $mailFields[$next_key]['MailField']['group_field'] && $this->BcArray->first($mailFields, $key))): ?>
					<?php if ($field['group_valid']): ?>
						<?php if ($this->Mailform->error("MailMessage." . $field['group_field'] . "_format", "check")): ?>
							<font color="#FF0000"><?php echo $this->Mailform->error("MailMessage." . $field['group_field'] . "_format", __("形式が無効です。"), array('wrap' => false)) ?></font>
						<?php endif; ?>
						<font color="#FF0000"><?php echo $this->Mailform->error("MailMessage." . $field['group_field'] . "_not_same", __("入力データが一致していません"), array('wrap' => false)) ?></font> 
						<font color="#FF0000"><?php echo $this->Mailform->error("MailMessage." . $field['group_field'] . "_not_complate", __("入力データが不完全です"), array('wrap' => false)) ?></font>

						<?php
						if (!$this->Mailform->error("MailMessage." . $field['group_field'] . "_not_same")
							&& !$this->Mailform->error("MailMessage." . $field['group_field'] . "_not_complate")) {
							$groupValidErrors = $this->Mailform->getGroupValidErrors($mailFields, $field['group_valid'], array('wrap' => false));
							if ($groupValidErrors) {
								foreach ($groupValidErrors as $groupValidError) {
									echo '<font color="#FF0000">' . $groupValidError . '</font>';
								}
							} else {
								echo '<font color="#FF0000">'. $this->Mailform->error("MailMessage." . $field['group_field'], __("必須項目です。"), array('wrap' => false)) . '</font>';
							}
						}
						?>
					<?php endif; ?>
				<?php endif; ?>
				<?php $group_field = $field['group_field'] ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>