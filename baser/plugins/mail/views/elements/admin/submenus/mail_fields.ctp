<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] メールフィールド管理メニュー
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
?>
<div class="side-navi">
<h2>メールフィールド<br />管理メニュー</h2>
<ul>
<li><?php $baser->link('フィールド一覧',array('controller'=>'mail_fields','action'=>'index',$mailContent['MailContent']['id'])) ?></li>
<li><?php $baser->link('新規フィールドを登録',array('controller'=>'mail_fields','action'=>'add',$mailContent['MailContent']['id'])) ?></li>
</ul>
</div>