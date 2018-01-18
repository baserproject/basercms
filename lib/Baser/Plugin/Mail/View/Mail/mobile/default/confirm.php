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
 * [MOBILE] メールフォーム確認ページ
 */
if ($freezed) {
	$this->Mailform->freeze();
}
?>


<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"><span style="color:white;">
		<?php $this->BcBaser->contentsTitle(); ?>
	</span></div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<br />

<?php if ($freezed): ?>
	<?php echo __('入力内容を確認する') ?><br />
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<font size="1"><?php echo __('入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。') ?></font>
	<?php else: ?>
	<?php echo __('入力フォーム') ?>
<?php endif; ?>

<?php $this->BcBaser->flash(); ?>
<?php $this->BcBaser->element('mail_form'); ?>