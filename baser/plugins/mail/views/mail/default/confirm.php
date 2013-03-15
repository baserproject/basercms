<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] メールフォーム確認ページ
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$bcBaser->css(array('/mail/css/style', 'jquery-ui/ui.all'), array('inline' => true));
$bcBaser->js(array('jquery-ui-1.8.19.custom.min','i18n/ui.datepicker-ja'), false);
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
