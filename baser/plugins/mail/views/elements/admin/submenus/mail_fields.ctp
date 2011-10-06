<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] メールフィールド管理メニュー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
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
?>

<div class="side-navi">
	<h2>メールフォーム<br />
		管理メニュー</h2>
	<ul>
		<li><?php $baser->link('公開ページ確認', array('admin' => false, 'plugin' => '', 'controller' => $mailContent['MailContent']['name'], 'action' => 'index'), array('target' => '_blank')) ?></li>
		<li><?php $baser->link('受信メール一覧', array('controller' => 'mail_messages', 'action' => 'index', $mailContent['MailContent']['id'])) ?></li>
		<li><?php $baser->link('フィールド一覧', array('controller' => 'mail_fields', 'action' => 'index', $mailContent['MailContent']['id'])) ?></li>
		<li><?php $baser->link('新規フィールドを登録', array('controller' => 'mail_fields', 'action' => 'add', $mailContent['MailContent']['id'])) ?></li>
		<li><?php $baser->link('メールフォーム基本設定', array('controller' => 'mail_contents', 'action' => 'edit', $mailContent['MailContent']['id'])) ?></li>
	</ul>
</div>
