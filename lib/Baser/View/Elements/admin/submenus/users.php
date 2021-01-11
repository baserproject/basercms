<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーザー管理メニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'ユーザー管理メニュー') ?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'ユーザー一覧'), ['controller' => 'users', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'ユーザー新規追加'), ['controller' => 'users', 'action' => 'add']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'ユーザーグループ一覧'), ['controller' => 'user_groups', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'ユーザーグループ新規登録'), ['controller' => 'user_groups', 'action' => 'add']) ?></li>
		</ul>
	</td>
</tr>
