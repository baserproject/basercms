<?php
/**
 * メールフォーム
 */
$html->css('jquery-ui-1.7.2/ui.all', null, null, false);
$baser->js(array('jquery-ui-1.7.2.custom.min','i18n/ui.datepicker-ja'), false);
$mail->indexFields($mailContent['MailContent']['id']);
?>

<h2 class="contents-head">
	<?php $baser->contentsTitle() ?>
</h2>
<h3 class="contents-head">入力フォーム</h3>
<div class="section">
	<p><span class="required">*</span> 印の項目は必須となりますので、必ず入力してください。</p>
	<?php $baser->flash() ?>
	<?php $baser->element('mail_form') ?>
</div>
