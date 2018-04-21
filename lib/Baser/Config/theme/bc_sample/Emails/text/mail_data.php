<?php
/**
 * メールフォーム送信メール内容
 * 呼出箇所：送信メール
 */
$group_field = null;
foreach ($mailFields as $field) {
	$field = $field['MailField'];
	if ($field['use_field'] && isset($message[$field['field_name']]) && ($group_field != $field['group_field'] || (!$group_field && !$field['group_field']))) {
?>


◇◆ <?php echo $field['head']; ?>　
----------------------------------------
<?php
	}
	if ($field['type'] != 'file' && !empty($field['before_attachment']) && isset($message[$field['field_name']])) {
		echo " " . $field['before_attachment'];
	}
	if (isset($message[$field['field_name']]) && !$field['no_send'] && $field['use_field']) {
		if($field['type'] != 'file') {
			echo $this->Maildata->control($field['type'], $message[$field['field_name']], $this->Mailfield->getOptions($field));
		} else {
			if($message[$field['field_name']]) {
				echo '添付あり';
			} else {
				echo '添付なし';
			}
		}
	}
	if ($field['type'] != 'file' && !empty($field['after_attachment']) && isset($message[$field['field_name']])) {
		echo " " . $field['after_attachment'];
	}
	$group_field = $field['group_field'];
}
?>