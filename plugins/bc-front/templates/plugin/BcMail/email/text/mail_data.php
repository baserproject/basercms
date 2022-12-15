<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * メールフォーム送信メール内容
 * 呼出箇所：送信メール
 * @var \BcMail\View\MailFrontEmailView $this
 * @var array $mailFields メールフィールドリスト
 * @checked
 * @noTodo
 * @unitTest
 */
$group_field = null;
foreach ($mailFields as $field) {
	if ($field->use_field && ($group_field != $field->group_field || (!$group_field && !$field->group_field))) {
?>


◇◆ <?php echo $field->head; ?>　
----------------------------------------
<?php
	}
	if ($field->type != 'file' && !empty($field->before_attachment) && isset($message[$field->field_name])) {
		echo " " . $field->before_attachment;
	}
	if (isset($message[$field->field_name]) && !$field->no_send && $field->use_field) {
		if($field->type != 'file') {
			echo $this->Maildata->control($field->type, $message[$field->field_name], $this->Mailfield->getOptions($field));
		} else {
			if($message[$field->field_name]) {
				echo __('添付あり');
			} else {
				echo __('添付なし');
			}
		}
	}
	if ($field->type != 'file' && !empty($field->after_attachment) && isset($message[$field->field_name])) {
		echo " " . $field->after_attachment;
	}
	$group_field = $field->group_field;
}
?>
