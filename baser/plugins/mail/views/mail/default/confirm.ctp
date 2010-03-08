<?php
/* SVN FILE: $Id$ */
/**
 * メールフォーム確認ページ
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$html->css('/mail/css/style',null,null,false);
if($freezed){
	$mailform->freeze();
}
?>


<h2 class="contents-head"><?php $baser->contentsTitle() ?></h2>
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