<?php
/**
 * メールフォーム確認ページ（スマホ用）
 * 呼出箇所：メールフォーム
 */
if ($freezed) {
	$this->Mailform->freeze();
}
?>


<h2><?php $this->BcBaser->contentsTitle(); ?></h2>

<?php if ($freezed): ?>
	<h3><?php echo __('入力内容の確認') ?></h3>
	<p><?php echo __('入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。') ?></p>
	<?php else: ?>
	<h3><?php echo __('入力フォーム') ?></h3>
<?php endif; ?>

<div class="mail-form">
	<?php $this->BcBaser->flash(); ?>
	<!-- /Elements/smartphone/mail_form.php -->
	<?php $this->BcBaser->element('mail_form'); ?>
</div>
