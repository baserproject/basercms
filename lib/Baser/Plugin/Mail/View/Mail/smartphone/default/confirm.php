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
 * [PUBLISH] メールフォーム確認ページ
 */
$this->BcBaser->css('admin/jquery-ui/jquery-ui.min', ['inline' => true]);
$this->BcBaser->js(['admin/vendors/jquery-ui-1.11.4.min', 'admin/vendors/i18n/ui.datepicker-ja'], false);
if ($freezed) {
	$this->Mailform->freeze();
}
?>


<h1 class="contents-head">
	<?php $this->BcBaser->contentsTitle(); ?>
</h1>

<?php if ($freezed): ?>
	<h2 class="contents-head"><?php echo __('入力内容を確認する') ?></h2>
	<p class="section"><?php echo __('入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。') ?></p>
<?php else: ?>
	<h2 class="contents-head"><?php echo __('入力フォーム') ?></h2>
<?php endif; ?>

<div class="section mail-form">
	<?php $this->BcBaser->flash(); ?>
	<?php $this->BcBaser->element('mail_form'); ?>
</div>
