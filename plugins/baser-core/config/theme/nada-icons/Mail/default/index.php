<?php
/**
 * メールフォーム
 */
$this->BcBaser->css('admin/jquery-ui/jquery-ui.min', array('inline' => true));
$this->BcBaser->js(array('vendor/jquery-ui-1.11.4.min', 'vendor/i18n/ui.datepicker-ja'), false);
?>

<h2 class="contents-head">
	<?php $this->BcBaser->contentsTitle() ?>
</h2>

<h3 class="contents-head"><?php echo __('入力フォーム') ?></h3>

<div class="section mail-description">
	<?php $this->Mail->description() ?>
</div>

<div class="section">
	<?php $this->BcBaser->flash() ?>
	<?php $this->BcBaser->element('mail_form') ?>
</div>
