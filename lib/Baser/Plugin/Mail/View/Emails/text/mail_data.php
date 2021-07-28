<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [EMAIL] メール送信データ
 */
$group_field = null;
foreach ($mailFields as $field) {
	$field = $field['MailField'];
	if ($field['use_field'] && ($group_field != $field['group_field'] || (!$group_field && !$field['group_field']))) {
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
			echo $this->Maildata->control($field['type'], $message[$field['field_name']]);
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
