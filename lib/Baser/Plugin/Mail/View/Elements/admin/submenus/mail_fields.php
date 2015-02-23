<?php
/**
 * [管理画面] メールフィールド管理メニュー
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
?>


<tr>
	<th>メールフォーム管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('メールフィールド一覧', array('controller' => 'mail_fields', 'action' => 'index', $mailContent['MailContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('メールフィールド新規追加', array('controller' => 'mail_fields', 'action' => 'add', $mailContent['MailContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link($mailContent['MailContent']['title'].'設定', array('controller' => 'mail_contents', 'action' => 'edit', $mailContent['MailContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('公開ページ確認', '/' . $mailContent['MailContent']['name'] . '/index') ?></li>
			<li><?php $this->BcBaser->link('受信メール一覧', array('controller' => 'mail_messages', 'action' => 'index', $mailContent['MailContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('受信メールCSVダウンロード', array('controller' => 'mail_fields', 'action' => 'download_csv', $mailContent['MailContent']['id'])) ?></li>
		</ul>
	</td>
</tr>
