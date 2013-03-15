<?php
/**
 * メールフォーム確認ページ
 */
$bcBaser->css('jquery-ui/ui.all', array('inline' => true));
$bcBaser->js(array('jquery-ui-1.8.19.custom.min', 'i18n/ui.datepicker-ja'), false);
if($freezed){
	$mailform->freeze();
}
?>

<h2 class="contents-head">
	<?php $bcBaser->contentsTitle() ?>
</h2>
<?php if($freezed): ?>
<h3 class="contents-head">入力内容の確認</h3>
<p class="section">入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。</p>
<?php else: ?>
<h3 class="contents-head">入力フォーム</h3>
<?php endif ?>
<div class="section">
	<?php $bcBaser->flash() ?>
	<?php $bcBaser->element('mail_form') ?>
</div>
