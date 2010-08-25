<?php
/* SVN FILE: $Id$ */
/**
 * メールフォーム本体
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

			/* 項目名 */
			if ($group_field != $field['group_field']  || (!$group_field && !$field['group_field'])) {
				$description = $field['description'];
				echo '<tr';
				if ($field['type'] == 'hidden') {
					echo ' style="display:none"';
				}
				echo '><th class="col-head" width="150">'.$mailform->label("Message." . $field['field_name'] . "", $field['head']);
				if($field['not_empty']) {
					echo '<span class="required">*</span>';
				}
				echo '</th><td class="col-input">';
			}

			/* 入力欄 */
			if (!$freezed || $mailform->value("Message." . $field['field_name'])) {
				echo $field['before_attachment'];
			}
			if (!$field['no_send'] || !$freezed) {
				echo $mailform->control($field['type'], "Message." . $field['field_name'] . "", $mailfield->getOptions($record), $mailfield->getAttributes($record));
			}
			if (!$freezed || $mailform->value("Message." . $field['field_name'])) {
				echo $field['after_attachment'];
			}
			if (!$freezed) {
				echo $field['attention'];
			}
			if (!$field['group_valid']) {
				if($mailform->error("Message." . $field['field_name'] . "_format", "check")) {
					echo $mailform->error("Message." . $field['field_name'] . "_format", ">> 形式が不正です");
				}else {
					echo $mailform->error("Message." . $field['field_name'] . "", ">> 必須項目です");
				}
			}

			/* 説明欄 */
			if (($array->last($mailFields,$record)) ||
					($field['group_field'] != $mailFields[$next_key]['MailField']['group_field']) ||
					(!$field['group_field'] && !$mailFields[$next_key]['MailField']['group_field']) ||
					($field['group_field'] != $mailFields[$next_key]['MailField']['group_field'] && $array->first($mailFields,$record))) {
				if (!$freezed && $description) {
					echo $html->image('img_icon_help.gif',array('id'=>Inflector::variable('help_'.$field['field_name']),'class'=>'help','alt'=>'ヘルプ'));
				}
				if ($field['group_valid']) {
					if ($mailform->error("Message." . $field['group_field'] . "_format", "check")) {
						echo $mailform->error("Message." . $field['group_field'] . "_format", ">> 形式が不正です");
					} else {
						echo $mailform->error("Message." . $field['group_field'] . "", ">> 必須項目です");
					}
					echo $mailform->error("Message." . $field['group_field'] . "_not_same", ">> 入力データが一致していません");
					$mailform->error("Message." . $field['group_field'] . "_not_complate", ">> 入力データが不完全です");
				}
				if (!$freezed && $description) {
					echo '<div id="'.Inflector::variable('helptext_'.$field['field_name']) . '" class="helptext">'. $description .'</div>';
				}
				echo '</td></tr>';
			}
			$group_field=$field['group_field'];
		}
	}
}
?>