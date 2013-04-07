<?php
/**
 * メールフォーム
 */
$this->bcBaser->css('jquery-ui/ui.all', array('inline' => true));
$this->bcBaser->js(array('jquery-ui-1.8.14.custom.min','i18n/ui.datepicker-ja'), false);
?>

<h2 class="contents-head">
	<?php $this->bcBaser->contentsTitle() ?>
</h2>

<h3 class="contents-head">入力フォーム</h3>

<div class="section mail-description">
	<?php $this->mail->description() ?>
</div>

<div class="section">
	<?php $this->bcBaser->flash() ?>
	<?php $this->bcBaser->element('mail_form') ?>
</div>
