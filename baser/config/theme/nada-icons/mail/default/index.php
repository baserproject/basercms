<?php
/**
 * メールフォーム
 */
$bcBaser->css('jquery-ui/ui.all', array('inline' => true));
$bcBaser->js(array('jquery-ui-1.8.19.custom.min','i18n/ui.datepicker-ja'), false);
?>

<h2 class="contents-head">
	<?php $bcBaser->contentsTitle() ?>
</h2>

<h3 class="contents-head">入力フォーム</h3>

<div class="section mail-description">
	<?php $mail->description() ?>
</div>

<div class="section">
	<?php $bcBaser->flash() ?>
	<?php $bcBaser->element('mail_form') ?>
</div>
