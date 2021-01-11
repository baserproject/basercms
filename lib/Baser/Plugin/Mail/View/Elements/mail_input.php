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
 * @var int  $this->get('blockStart') 表示するフィールドの開始NO
 * @var int  $this->get('blockEnd') 表示するフィールドの終了NO
 * @var bool $this->get('freezed') 確認画面かどうか
 */

if (!$this->get('mailFields')) {
	return;
}

$template = [
	'row'    => [
		'<tr id="{%row_id%}"{%display%}>',
		'<th class="col-head" width="150">{%label%}{%mark%}</th>',
		'<td class="col-input">{%input%}</td>',
		'</tr>'
	],
	'row_id' => 'RowMessage{%camelize(field_name)%}',
	'mark'   => [
		'required' => sprintf('<span class="required">%s</span>', __('必須')),
		'optional' => sprintf('<span class="normal">%s</span>', __('任意'))
	],
	'input' => [
		'wrap'        => [
			'<span id="{%field_id%}">',
			'{%description%}{%before%}{%input%}{%after%}{%attention%}{%error%}{%group-error%}',
			'</span>'
		],
		'field_id'    => 'FieldMessage{%camelize(field_name)%}',
		'description' => '<span class="mail-description">{%description%}</span>',
		'before'      => '<span class="mail-before-attachment">{%before%}</span>',
		'after'       => '<span class="mail-after-attachment">{%after%}</span>',
		'attention'   => '<span class="mail-attention">{%attention%}</span>'
	]
];

$group_field = null;
$iteration = 0;

$row  = null;
$input = [];
$rows = [];
$records = $this->get('mailFields');
foreach ($records as $key => $record) {
	$iteration++;
	if (!$record['MailField']['use_field']) {
		continue;
	}
	if (!$blockStart || $iteration < $blockStart) {
		continue;
	}
	if ($this->get('blockEnd') && $iteration > $this->get('blockEnd')) {
		continue;
	}
	$field = $record['MailField'];
	$next_key = $key + 1;

	// =========================================================================================================
	// 2018/02/06 ryuring
	// no_send オプションは、確認画面に表示しないようにするために利用されている可能性が高い
	//（メールアドレスのダブル入力、プライバシーポリシーへの同意に利用されている）
	// 本来であれば、not_display_confirm 等のオプションを別途準備し、そちらを利用するべきだが、
	// 後方互換のため残す
	// =========================================================================================================
	$isGroupValidComplate = in_array('VALID_GROUP_COMPLATE', explode(',', $field['valid_ex']));
	$tmp = [];
	if(!$this->get('freezed') && $field['description']) {
		$tmp['description'] = $this->Mail->formatText(
			Hash::get($template, 'input.description'), $field
		);
	}
	if(!$this->get('freezed') || $this->Mailform->value('MailMessage.' . $field['field_name']) !== '') {
		$tmp['before'] = $this->Mail->formatText(
			Hash::get($template, 'input.before'), ['before'=>$field['before_attachment']]
		);
		$tmp['after']  = $this->Mail->formatText(
			Hash::get($template, 'input.after'), ['after'=>$field['after_attachment']]
		);
	}
	if (!$isGroupValidComplate) {
		$tmp['error'] = $this->Mailform->error('MailMessage.' . $field['field_name']);
	}
	$errors = [];
	if ($this->Mailform->isGroupLastField($this->get('mailFields'), $field)) {
		if ($isGroupValidComplate) {
			$groupValidErrors = $this->Mailform->getGroupValidErrors(
				$this->get('mailFields'),
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
	$input[] = $this->Mail->formatText(
		Hash::get($template, 'input.wrap'),
		[
			'field_id'      => $this->Mail->formatText(
				Hash::get($template, 'input.field_id'),
				['camelize(field_name)'=>Inflector::camelize($field['field_name'])]
			),
			'description' => Hash::get($tmp, 'description', ''),
			'before'      => Hash::get($tmp, 'before', ''),
			'input' => $this->Mailform->control(
				($this->get('freezed') && $field['no_send']) ? 'hidden' : $field['type'],
				'MailMessage.' . $field['field_name'],
				$this->Mailfield->getOptions($record),
				$this->Mailfield->getAttributes($record)
			),
			'after'       => Hash::get($tmp, 'after', ''),
			'attention'   => !$this->get('freezed') ? $this->Mailform->error(Hash::get($template, 'input.attention'), $field) : '',
			'error'       => Hash::get($tmp, 'error', ''),
			'group-error' => implode("\n", $errors)

		]
	);

	/* 項目名 */
	if ($group_field != $field['group_field'] || (!$group_field && !$field['group_field'])) {
		$row = $this->mail->formatText(
			Hash::get($template, 'row'),
			[
				'row_id'  => $this->Mail->formatText(
					Hash::get($template, 'row_id'),
					['camelize(field_name)'=>Inflector::camelize($field['field_name'])]
				),
				'display' => $field['type'] === 'hidden' ? ' style="display:none"' : '',
				'label'   =>$this->Mailform->label(
					sprintf('MailMessage.%s', $field['field_name']),
					$field['head']
				),
				'mark'    => Hash::get(
					$template,
					$field['not_empty'] ? 'mark.required' : 'mark.optional'
				)
			]
		);
	}

	if ($this->Mailform->isGroupLastField($this->get('mailFields'), $field) || empty($field['group_field'])) {
		$rows[] = $this->Mail->formatText($row,['input'=>implode("\n", $input)]);
		$row = false;
		$input = [];
	}
	$group_field = $field['group_field'];
}

echo implode("\n", $rows);
