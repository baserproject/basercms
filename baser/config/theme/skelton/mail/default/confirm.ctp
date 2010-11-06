<?php
/**
 * メールフォーム確認ページ
 */
if($freezed){
	$mailform->freeze();
}
?>

<h2 class="contents-head">
	<?php $baser->contentsTitle() ?>
</h2>
<?php if($freezed): ?>
<h3 class="contents-head">入力内容の確認</h3>
<p class="section">入力した内容に間違いがなければ「送信する」ボタンをクリックして下さい。</p>
<?php else: ?>
<h3 class="contents-head">入力フォーム</h3>
<?php endif ?>
<div class="section">
	<?php $baser->flash() ?>
	<?php $baser->element('mail_form') ?>
</div>
