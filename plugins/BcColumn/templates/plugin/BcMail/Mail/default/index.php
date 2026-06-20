<?php
/**
 * メールフォーム
 */
$this->BcBaser->css('admin/jquery-ui/jquery-ui.min', true);
$jsFiles = ['vendor/jquery-ui-1.11.4.min'];
if ($this->BcBaser->isJapaneseLocale()) {
	$jsFiles[] = 'vendor/i18n/ui.datepicker-ja';
}
$this->BcBaser->js($jsFiles, false);
?>


<div class="contact-form">

	<h2><?php $this->BcBaser->contentsTitle() ?></h2>

	<h3><?php echo __d('baser_core', '入力フォーム') ?></h3>

	<div class="section mail-description">
		<?php $this->Mail->description() ?>
	</div>

	<div class="section">
		<?php $this->BcBaser->flash() ?>
		<?php $this->BcBaser->element('mail_form') ?>
	</div>

</div>
