<?php
/**
 * メールフォーム
 * 呼出箇所：メールフォーム
 */
?>


<h2><?php $this->BcBaser->contentsTitle() ?></h2>

<h3><?php echo __('入力フォーム') ?></h3>

<?php if ($this->Mail->descriptionExists()): ?>
	<div class="mail-description"><?php $this->Mail->description() ?></div>
<?php endif ?>

<div>
	<?php $this->BcBaser->flash() ?>
	<!-- /Elements/mail_form.php -->
	<?php $this->BcBaser->element('mail_form') ?>
</div>
