<?php
/**
 * メールフォーム入力欄（スマホ用）
 * 呼出箇所：メールフォーム入力ページ、メールフォーム入力内容確認ページ
 */
$group_field = null;
$iteration = 0;

if (!isset($blockEnd)) {
	$blockEnd = 0;
}

if (!empty($mailFields)) {

	foreach ($mailFields as $key => $record) {

		$field = $record['MailField'];
		$iteration++;
		if ($field['use_field'] && ($blockStart && $iteration >= $blockStart) && (!$blockEnd || $iteration <= $blockEnd)) {

			$next_key = $key + 1;
			$description = $field['description'];

			/* 項目名 */
			if ($group_field != $field['group_field'] || (!$group_field && !$field['group_field'])) {
				echo '    <h4 id="RowMessage' . Inflector::camelize($record['MailField']['field_name']) . '"';
				if ($field['type'] == 'hidden') {
					echo ' style="display:none"';
				}
				echo '>' . "\n" . $this->Mailform->label("MailMessage." . $field['field_name'] . "", $field['head']);
				if ($field['not_empty']) {
					echo '<span class="required">*</span>';
				}
				echo '</h4>' . "\n" . '<p>';
			}

			echo '<span id="FieldMessage' . Inflector::camelize($record['MailField']['field_name']) . '">';
			if (!$freezed && $description) {
				echo '<span class="mail-description">' . $description . '</span>';
			}

			/* 入力欄 */
			if (!$freezed || $this->Mailform->value("MailMessage." . $field['field_name']) !== '') {
				echo '<span class="mail-before-attachment">' . $field['before_attachment'] . '</span>';
			}

			if ($field['no_send'] && $freezed) {
				echo $this->Mailform->control('hidden', "MailMessage." . $field['field_name'] . "", $this->Mailfield->getOptions($record), $this->Mailfield->getAttributes($record));
			} else {
				echo $this->Mailform->control($field['type'], "MailMessage." . $field['field_name'] . "", $this->Mailfield->getOptions($record), $this->Mailfield->getAttributes($record));
			}

			if (!$freezed || $this->Mailform->value("MailMessage." . $field['field_name']) !== '') {
				echo '<span class="mail-after-attachment">' . $field['after_attachment'] . '</span>';
			}
			if (!$freezed) {
				echo '<span class="mail-attention">' . $field['attention'] . '</span>';
			}
			if (!$field['group_valid']) {
				echo $this->Mailform->error("MailMessage." . $field['field_name']);
			}

			/* 説明欄 */
			if (($this->BcArray->last($mailFields, $key)) ||
				($field['group_field'] != $mailFields[$next_key]['MailField']['group_field']) ||
				(!$field['group_field'] && !$mailFields[$next_key]['MailField']['group_field']) ||
				($field['group_field'] != $mailFields[$next_key]['MailField']['group_field'] && $this->BcArray->first($mailFields, $key))) {

				if ($field['group_valid']) {
					if ($field['valid']) {
						echo $this->Mailform->error("MailMessage." . $field['group_field'], "必須項目です。");
					}
					echo $this->Mailform->error("MailMessage." . $field['group_field'] . "_not_same", "入力データが一致していません。");
					echo $this->Mailform->error("MailMessage." . $field['group_field'] . "_not_complate", "入力データが不完全です。");
				}

				echo '</span>';
				echo "</p>\n";
			} else {
				echo '</span>';
			}
			$group_field = $field['group_field'];
		}
	}
}
