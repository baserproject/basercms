<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * メールフォーム入力欄
 * 呼出箇所：メールフォーム入力ページ、メールフォーム入力内容確認ページ
 *
 * @var int $blockStart 表示するフィールドの開始NO
 * @var int $blockEnd 表示するフィールドの終了NO
 * @var bool $freezed 確認画面かどうか
 * @var array $mailFields メールフィールドリスト
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
				echo '    <tr id="RowMessage' . Inflector::camelize($record['MailField']['field_name']) . '"';
				if ($field['type'] == 'hidden') {
					echo ' style="display:none"';
				}
				echo '>' . "\n" . '        <th class="col-head" width="150">' . $this->Mailform->label("MailMessage." . $field['field_name'] . "", $field['head']);
				if ($field['not_empty']) {
					echo '<span class="required">' . __('必須') . '</span>';
				} else {
					echo '<span class="normal">' . __('任意') . '</span>';
				}
				echo '</th>' . "\n" . '        <td class="col-input">';
			}

			echo '<span id="FieldMessage' . Inflector::camelize($record['MailField']['field_name']) . '">';
			if (!$freezed && $description) {
				echo '<span class="bs-mail-description">' . $description . '</span>';
			}
			/* 入力欄 */
			if (!$freezed || $this->Mailform->value("MailMessage." . $field['field_name']) !== '') {
				echo '<span class="bs-mail-before-attachment">' . $field['before_attachment'] . '</span>';
			}

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

			if (!$freezed || $this->Mailform->value("MailMessage." . $field['field_name']) !== '') {
				echo '<span class="bs-mail-after-attachment">' . $field['after_attachment'] . '</span>';
			}
			if (!$freezed) {
				echo '<span class="bs-mail-attention">' . $field['attention'] . '</span>';
			}

			/* 説明欄 */
			$isGroupValidComplate = in_array('VALID_GROUP_COMPLATE', explode(',', $field['valid_ex']));
			if(!$isGroupValidComplate) {
				echo $this->Mailform->error("MailMessage." . $field['field_name']);
			}
			$isRequiredToClose = true;
			if ($this->Mailform->isGroupLastField($mailFields, $field)) {
				if($isGroupValidComplate) {
					$groupValidErrors = $this->Mailform->getGroupValidErrors($mailFields, $field['group_valid']);
					if ($groupValidErrors) {
						foreach($groupValidErrors as $groupValidError) {
							echo $groupValidError;
						}
					}
				}
				echo $this->Mailform->error("MailMessage." . $field['group_valid'] . "_not_same", __("入力データが一致していません。"));
				echo $this->Mailform->error("MailMessage." . $field['group_valid'] . "_not_complate", __("入力データが不完全です。"));
			} elseif(!empty($field['group_field'])) {
				$isRequiredToClose = false;
			}
			echo '</span>';
			if($isRequiredToClose) {
				echo "</td>\n    </tr>\n";
			}
			$group_field = $field['group_field'];
		}
	}
}
