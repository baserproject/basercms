<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 受信メール詳細
 */
?>


<!-- view -->
<table cellpadding="0" cellspacing="0" class="list-table bca-form-table" id="ListTable">

	<tr>
		<th class="col-head bca-form-table__label">No</th>
		<td class="col-input bca-form-table__input"><?php echo $message['MailMessage']['id'] ?></td>
	</tr>
	<tr>
		<th class="col-head bca-form-table__label"><?php echo __d('baser', '受信日時') ?></th>
		<td class="col-input bca-form-table__input"><?php echo $this->BcTime->format('Y/m/d H:i:s', $message['MailMessage']['created']) ?></td>
	</tr>
	<?php
	$groupField = null;
	foreach($mailFields as $key => $mailField) {
		$field = $mailField['MailField'];
		if ($field['use_field']) {
			$nextKey = $key + 1;
			/* 項目名 */
			if ($groupField != $field['group_field'] || (!$groupField && !$field['group_field'])) {
				echo '<tr>';
				echo '<th class="col-head bca-form-table__label">' . $field['head'] . '</th>';
				echo '<td class="col-input bca-form-table__input">';
			}
			if (!empty($message['MailMessage'][$mailField['MailField']['field_name']])) {
				echo $field['before_attachment'];
			}
			if (!$field['no_send']) {
				if ($field['type'] == 'file') {
					echo $this->Maildata->control(
						$mailField['MailField']['type'],
						$message['MailMessage'][$mailField['MailField']['field_name']],
						$this->Mailfield->getOptions($mailField['MailField'])
					);
				} else {
					echo nl2br($this->BcText->autoLink($this->Maildata->control(
						$mailField['MailField']['type'],
						$message['MailMessage'][$mailField['MailField']['field_name']],
						$this->Mailfield->getOptions($mailField['MailField'])
					)));
				}
			}
			if (!empty($message['MailMessage'][$mailField['MailField']['field_name']])) {
				echo $field['after_attachment'];
			}
			echo '&nbsp;';
			if (($this->BcArray->last($mailFields, $key)) ||
				($field['group_field'] != $mailFields[$nextKey]['MailField']['group_field']) ||
				(!$field['group_field'] && !$mailFields[$nextKey]['MailField']['group_field']) ||
				($field['group_field'] != $mailFields[$nextKey]['MailField']['group_field'] && $this->BcArray->first($mailFields, $key))) {
				echo '</td></tr>';
			}
			$groupField = $field['group_field'];
		}
	}
	?>
</table>

<!-- button -->
<div class="bca-actions">
	<?php
	$this->BcBaser->link(__d('baser', '削除'),
		[
			'action' => 'delete',
			$mailContent['MailContent']['id'],
			$message['MailMessage']['id']],
		[
			'class' => 'submit-token button bca-btn bca-actions__item',
			'data-bca-btn-type' => 'delete',
			'data-bca-btn-size' => 'sm',
			'data-bca-btn-color' => 'danger',
			'data-bca-btn-type' => 'delete'
		],
		sprintf(__d('baser', '受信メール No「%s」を削除してもいいですか？'), $message['MailMessage']['id']),
		false
	);
	?>
</div>
