<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [管理画面] メールフィールド管理メニュー
 */
?>


<tr>
	<th>メールフォーム管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link($this->request->params['Content']['title'] . '設定', array('controller' => 'mail_contents', 'action' => 'edit', $mailContent['MailContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('メールフィールド一覧', array('controller' => 'mail_fields', 'action' => 'index', $mailContent['MailContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('メールフィールド新規追加', array('controller' => 'mail_fields', 'action' => 'add', $mailContent['MailContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('受信メール一覧', array('controller' => 'mail_messages', 'action' => 'index', $mailContent['MailContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('メールプラグイン基本設定', array('controller' => 'mail_configs', 'action' => 'form')) ?></li>
		</ul>
	</td>
</tr>
