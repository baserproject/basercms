<?php
/**
 * メールフォーム確認ページ
 */
$this->BcBaser->css('admin/jquery-ui/jquery-ui.min', array('inline' => true));
$this->BcBaser->js(array('admin/vendors/jquery-ui-1.11.4.min', 'admin/vendors/i18n/ui.datepicker-ja'), false);
if ($freezed) {
	$this->Mailform->freeze();
}
?>


<div class="contact-form">

	<h2><?php $this->BcBaser->contentsTitle() ?></h2>

	<?php if ($freezed): ?>
	<h3><?php echo __('入力内容の確認') ?></h3>
	<p class="section"><?php echo __('入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。') ?></p>
	<?php else: ?>
	<h3><?php echo __('入力フォーム') ?></h3>
	<?php endif; ?>
	<div class="section">
		<?php $this->BcBaser->flash() ?>
		<?php $this->BcBaser->element('mail_form') ?>
	</div>

</div>