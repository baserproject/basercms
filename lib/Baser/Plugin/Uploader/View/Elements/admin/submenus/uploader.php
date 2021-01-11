<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.View
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */
?>


<tr>
	<th><?php echo __d('baser', 'アップローダーメニュー') ?></th>
	<td>
		<ul>
			<li><?php $this->BcBaser->link(__d('baser', 'アップロードファイル一覧'), ['plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'カテゴリ一覧'), ['plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'カテゴリ新規登録'), ['plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'add']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'プラグイン基本設定'), ['plugin' => 'uploader', 'controller' => 'uploader_configs', 'action' => 'index']) ?></li>
		</ul>
	</td>
</tr>
