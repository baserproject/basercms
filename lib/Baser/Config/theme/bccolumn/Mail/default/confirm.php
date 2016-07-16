<?php
/**
 * メールフォーム確認ページ
 */
$this->BcBaser->css('admin/jquery-ui/ui.all', array('inline' => true));
$this->BcBaser->js(array('admin/jquery-ui-1.8.19.custom.min', 'admin/i18n/ui.datepicker-ja'), false);
if ($freezed) {
	$this->Mailform->freeze();
}
?>


<div class="contact-form">

	<h2><?php $this->BcBaser->contentsTitle() ?></h2>

	<?php if ($freezed): ?>
	<h3>入力内容の確認</h3>
	<p class="section">入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。</p>
	<?php else: ?>
	<h3>入力フォーム</h3>
	<?php endif; ?>
	<div class="section">
		<?php $this->BcBaser->flash() ?>
		<?php $this->BcBaser->element('mail_form') ?>
	</div>

</div>