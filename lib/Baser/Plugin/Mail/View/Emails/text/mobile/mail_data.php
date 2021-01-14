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
 * [EMAIL] MOBILE メール送信データ
 */
?>
<?php $group_field = null; ?>
<?php foreach($mailFields as $fields): ?>
<?php if ($field['use_field'] && isset($message[$field['field_name']]) && ($group_field != $field['group_field'] || (!$group_field && !$field['group_field']))) : ?>


◇◆ <?php echo $fields['MailField']['head']; ?>　
----------------------------
<?php endif; ?>
<?php if ($field['type'] != 'file' && !empty($fields['MailField']['before_attachment']) && !empty($message[$fields['MailField']['field_name']])): ?>
<?php echo " " . $fields['MailField']['before_attachment'] ?>
<?php endif; ?>
<?php if (!empty($message[$fields['MailField']['field_name']]) && !$fields['MailField']['no_send']): ?>
<?php if ($field['type'] != 'file'): ?>
<?php echo $this->Maildata->control($fields['MailField']['type'], $message[$fields['MailField']['field_name']]); ?>
<?php else: ?>
<?php if($message[$field['field_name']]): ?>
<?php echo '添付あり' ?>
<?php else: ?>
<?php echo '添付なし' ?>
<?php endif ?>
<?php endif ?>
<?php endif; ?>
<?php if ($field['type'] != 'file' && !empty($fields['MailField']['after_attachment']) && !empty($message[$fields['MailField']['field_name']])): ?>
<?php echo " " . $fields['MailField']['after_attachment']; ?>
<?php endif; ?>
<?php $group_field = $fields['MailField']['group_field']; ?>
<?php endforeach; ?>
