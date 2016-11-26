<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.View
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>アップローダーメニュー</th>
	<td>
		<ul>
			<li><?php $this->BcBaser->link('アップロードファイル一覧', array('plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('カテゴリ一覧', array('plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('カテゴリ新規登録', array('plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'add')) ?></li>
			<li><?php $this->BcBaser->link('プラグイン基本設定', array('plugin' => 'uploader', 'controller' => 'uploader_configs', 'action' => 'index')) ?></li>
		</ul>
	</td>
</tr>
