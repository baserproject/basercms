<?php
/**
 * [PUBLISH] メールフォーム確認ページ
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->css(array('Mail.style', 'admin/jquery-ui/ui.all'), array('inline' => true));
$this->BcBaser->js(array('admin/jquery-ui-1.8.19.custom.min', 'admin/i18n/ui.datepicker-ja'), false);
if ($freezed) {
	$this->Mailform->freeze();
}
?>


<div class="articleArea bgGray" id="contact">
<article class="mainWidth">
<h2 class="fontawesome-circle-arrow-down"><?php $this->BcBaser->contentsTitle() ?></h2>
<?php if ($freezed): ?>
<section>
<h3>入力内容の確認</h3>
<p>入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。</p>
</section>
<?php else: ?>
<section>
<h3>入力フォーム</h3>
</section>
<?php endif ?>

<?php $this->BcBaser->flash() ?>
<?php $this->BcBaser->element('mail_form') ?>

</article>
</div><!-- /articleArea -->

