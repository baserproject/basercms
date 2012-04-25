<?php
/**
 * メールフォーム
 */
$baser->css('jquery-ui/ui.all', array('inline' => true));
$baser->js(array('jquery-ui-1.8.14.custom.min','i18n/ui.datepicker-ja'), false);
?>

<h2 class="contents-head">
	<?php $baser->contentsTitle() ?>
</h2>

<h3 class="contents-head">入力フォーム</h3>

<div class="section mail-description">
	<?php $mail->description() ?>
</div>

<div class="section">
	<?php $baser->flash() ?>
	<?php $baser->element('mail_form') ?>
</div>
