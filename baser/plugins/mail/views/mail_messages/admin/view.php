<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] 受信メール詳細
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<!-- view -->
<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<tr><th>NO</th><td><?php echo $message['Message']['id'] ?></td></tr>
	<tr><th>受信日時</th><td><?php echo $bcTime->format('Y/m/d H:i:s', $message['Message']['created']) ?></td></tr>
<?php 
$groupField = null;
foreach($mailFields as $key => $mailField) {
	$field = $mailField['MailField'];
	if($field['use_field'] && $field['type'] != 'hidden') {
		$nextKey = $key + 1;
		/* 項目名 */
		if ($groupField != $field['group_field']  || (!$groupField && !$field['group_field'])) {
			echo '<tr>';
			echo '<th class="col-head" width="160">'.$field['head'].'</th>';
			echo '<td class="col-input">';
		}
		if(!empty($message['Message'][$mailField['MailField']['field_name']])) {
			echo $field['before_attachment'];
		}
		if (!$field['no_send']) {
			echo $bcText->autoLink(nl2br($maildata->control(
				$mailField['MailField']['type'],
				$message['Message'][$mailField['MailField']['field_name']],
				$mailfield->getOptions($mailField['MailField'])
			)));
		}
		if(!empty($message['Message'][$mailField['MailField']['field_name']])) {
			echo $field['after_attachment'];
		}
		echo '&nbsp;';
		if (($bcArray->last($mailFields, $key)) ||
				($field['group_field'] != $mailFields[$nextKey]['MailField']['group_field']) ||
				(!$field['group_field'] && !$mailFields[$nextKey]['MailField']['group_field']) ||
				($field['group_field'] != $mailFields[$nextKey]['MailField']['group_field'] && $bcArray->first($mailFields,$key))) {
			echo '</td></tr>';
		}
		$groupField=$field['group_field'];
	}
}
?>
</table>

<!-- button -->
<p class="submit">
	<?php $bcBaser->link('削除',
					array('action'=>'delete', $mailContent['MailContent']['id'], $message['Message']['id']),
					array('class'=>'btn-gray button'),
					sprintf('受信メール NO「%s」を削除してもいいですか？', $message['Message']['id']), false) ?>
</p>
