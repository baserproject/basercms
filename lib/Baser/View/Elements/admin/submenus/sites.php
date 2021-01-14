<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * サブサイト管理メニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'サブサイト管理メニュー') ?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'サブサイト一覧'), ['plugin' => '', 'admin' => true, 'controller' => 'sites', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'サブサイト新規登録'), ['plugin' => '', 'admin' => true, 'controller' => 'sites', 'action' => 'add']) ?></li>
		</ul>
	</td>
</tr>
