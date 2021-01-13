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
 * [ADMIN] プラグインメニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'プラグイン管理メニュー') ?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'プラグイン一覧'), ['plugin' => null, 'controller' => 'plugins', 'action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'プラグイン新規追加'), ['plugin' => null, 'controller' => 'plugins', 'action' => 'add']) ?></li>
		</ul>
	</td>
</tr>
