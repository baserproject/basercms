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
 * [ADMIN] 統合コンテンツメニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'コンテンツメニュー') ?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'コンテンツ一覧'), ['plugin' => '', 'admin' => true, 'controller' => 'contents', 'action' => 'index']) ?></li>
			<?php if ($this->name == 'Contents' && $this->request->action == 'admin_index'): ?>
				<li><?php $this->BcBaser->link(__d('baser', 'コンテンツ新規登録'), "#", ['id' => 'BtnAddContent']) ?></li>
			<?php endif ?>
			<li><?php $this->BcBaser->link(__d('baser', 'ゴミ箱'), ['plugin' => '', 'admin' => true, 'controller' => 'contents', 'action' => 'trash_index']) ?></li>
		</ul>
	</td>
</tr>
