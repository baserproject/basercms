<?php
/**
 * メールフォーム
 */
$this->BcBaser->css('admin/jquery-ui/jquery-ui.min', array('inline' => true));
$this->BcBaser->js(array('admin/vendors/jquery-ui-1.11.4.min', 'admin/vendors/i18n/ui.datepicker-ja'), false);
?>


<div class="contact-form">

	<h2><?php $this->BcBaser->contentsTitle() ?></h2>

	<h3><?php echo __('入力フォーム') ?></h3>

	<div class="section mail-description">
		<?php $this->Mail->description() ?>
	</div>

	<div class="section">
		<?php $this->BcBaser->flash() ?>
		<?php $this->BcBaser->element('mail_form') ?>
	</div>

</div>