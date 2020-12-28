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
 * メールフォーム入力欄
 * 呼出箇所：メールフォーム入力ページ、メールフォーム入力内容確認ページ
 *
 * @var int $blockStart 表示するフィールドの開始NO
 * @var int $blockEnd 表示するフィールドの終了NO
 * @var bool $freezed 確認画面かどうか
 */

if (empty($mailFields)) {
	return;
}
$group_field = null;
$iteration = 0;
if (!isset($blockEnd)) {
	$blockEnd = 0;
}

$title = null;
$form = [];
foreach ($mailFields as $key => $record) {

	$iteration++;
	if (!$record['MailField']['use_field']) {
		continue;
	}
	if (!$blockStart || $iteration < $blockStart) {
		continue;
	}
	if ($blockEnd && $iteration > $blockEnd) {
		continue;
	}
	$field = $record['MailField'];
	$next_key = $key + 1;

	/* 項目名 */
	if ($group_field != $field['group_field'] || (!$group_field && !$field['group_field'])) {
		$title = $this->mail->formatText(
			'<tr id="{%row_id%}"{%display%}><th class="col-head" width="150">{%title%}<span class="{%required_class%}">{%required_word%}</span></th><td class="col-input">{%form%}</td></tr>',
			[
				'row_id'  => 'RowMessage' . Inflector::camelize($field['field_name']),
				'display' => $field['type'] === 'hidden' ? ' style="display:none"' : '',
				'title'   =>$this->Mailform->label(
					sprintf(
						'MailMessage.%s',
						$field['field_name']
					),
					$field['head']
				),
				'required_class' => $field['not_empty'] ? 'required' : 'normal',
				'required_word'  => $field['not_empty'] ? __('必須') : __('任意')
			]
		);
	}

	// =========================================================================================================
	// 2018/02/06 ryuring
	// no_send オプションは、確認画面に表示しないようにするために利用されている可能性が高い
	//（メールアドレスのダブル入力、プライバシーポリシーへの同意に利用されている）
	// 本来であれば、not_display_confirm 等のオプションを別途準備し、そちらを利用するべきだが、
	// 後方互換のため残す
	// =========================================================================================================
	$errors = [];
	if ($this->Mailform->isGroupLastField($mailFields, $field)) {
		if ($isGroupValidComplate) {
			$groupValidErrors = $this->Mailform->getGroupValidErrors(
				$mailFields,
				$field['group_valid']
			);
			if ($groupValidErrors) {
				foreach ($groupValidErrors as $groupValidError) {
					$errors[] = $groupValidError;
				}
			}
		}
		$errors[] = $this->Mailform->error(
			'MailMessage.' . $field['group_valid'] . "_not_same",
			__('入力データが一致していません。')
		);
		$errors[] = $this->Mailform->error(
			'MailMessage.' . $field['group_valid'] . '_not_complate',
			__('入力データが不完全です。')
		);
	}
	$hasDescription = (!$freezed && $field['description']);
	$hasAttachment = (!$freezed || $this->Mailform->value('MailMessage.' . $field['field_name']) !== '');
	$isGroupValidComplate = in_array('VALID_GROUP_COMPLATE', explode(',', $field['valid_ex']));
	$form[] = $this->Mail->formatText(
		'<span id="{%id%}">{%description%}{%before-attach%}{%form%}{%after-attach%}{%attention%}{%error%}{%lastErrors%}</span>',
		[
			'id'=>'FieldMessage' . Inflector::camelize($field['field_name']),
			'description' => $hasDescription ? sprintf('<span class="mail-description">%s</span>', $field['description']) : '',
			'before-attach' => $hasAttachment  ? sprintf('<span class="mail-before-attachment">%s</span>', $field['before_attachment']) : '',
			'form' => $this->Mailform->control(
				($freezed && $field['no_send']) ? 'hidden' : $field['type'],
				'MailMessage.' . $field['field_name'],
				$this->Mailfield->getOptions($record),
				$this->Mailfield->getAttributes($record)
			),
			'after-attach'=>$hasAttachment ? sprintf('<span class="mail-after-attachment">%s</span>', $field['after_attachment']) : '',
			'attention'=>!$freezed ? sprintf('<span class="mail-attention">%s</span>', $field['attention']) : '',
			'error'=>!$isGroupValidComplate ? $this->Mailform->error('MailMessage.' . $field['field_name']) : '',
			'lastErrors' => implode("\n", $errors)

		]
	);

	if ($this->Mailform->isGroupLastField($mailFields, $field) || empty($field['group_field'])) {
		echo $this->Mail->formatText($title,['form'=>implode("\n", $form)]);
		$title = null;
		$form = [];
	}
	$group_field = $field['group_field'];
}
