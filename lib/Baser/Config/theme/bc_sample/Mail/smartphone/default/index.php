<?php
/**
 * メールフォーム（スマホ用）
 * 呼出箇所：メールフォーム
 */
?>


<h2><?php $this->BcBaser->contentsTitle() ?></h2>

<h3><?php echo __('入力フォーム') ?></h3>

<div class="mail-description"><?php $this->Mail->description() ?></div>

<div class="mail-form">
	<?php $this->BcBaser->flash() ?>
	<!-- /Elements/smartphone/mail_form.php -->
	<?php $this->BcBaser->element('mail_form') ?>
</div>
