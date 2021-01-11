<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード設定共通メニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'フィード設定メニュー') ?></th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link(__d('baser', 'フィード設定一覧'), ['action' => 'index']) ?></li>
			<li><?php $this->BcBaser->link(__d('baser', 'フィード設定新規追加'), ['action' => 'add']) ?></li>
			<?php if ($this->params['controller'] == 'feed_configs' && $this->action == 'admin_index'): ?>
				<li><?php $this->BcBaser->link(__d('baser', 'フィードキャッシュ削除'), ['action' => 'delete_cache'], ['class' => 'submit-token'], __d('baser', 'フィードのキャッシュを削除します。いいですか？')) ?></li>
			<?php endif ?>
		</ul>
	</td>
</tr>
