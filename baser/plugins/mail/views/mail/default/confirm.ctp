<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] メールフォーム確認ページ
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$html->css(array('/mail/css/style', 'jquery-ui/ui.all'),null,null,false);
$baser->js(array('jquery-ui-1.8.14.custom.min','i18n/ui.datepicker-ja'), false);
if($freezed){
	$mailform->freeze();
}
?>

<h2 class="contents-head">
	<?php $baser->contentsTitle() ?>
</h2>

<?php if($freezed): ?>
<h3 class="contents-head">入力内容の確認</h3>
<p class="section">入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。</p>
<?php else: ?>
<h3 class="contents-head">入力フォーム</h3>
<?php endif ?>

<div class="section">
	<?php $baser->flash() ?>
	<?php $baser->element('mail_form') ?>
</div>
