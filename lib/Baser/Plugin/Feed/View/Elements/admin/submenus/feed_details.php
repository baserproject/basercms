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
 * [ADMIN] フィード詳細メニュー
 */
?>


<tr>
	<th><?php echo __d('baser', 'フィードメニュー') ?></th>
	<td>
		<ul class="cleafix">
			<?php if ($this->params['controller'] == 'feed_details'): ?>
				<li><?php $this->BcBaser->link(__d('baser', 'フィードを追加'), ['controller' => 'feed_details', 'action' => 'add', $this->BcForm->value('FeedDetail.feed_config_id')]) ?></li>
				<li><?php $this->BcBaser->link(__d('baser', 'フィード設定に戻る'), ['controller' => 'feed_configs', 'action' => 'edit', $this->BcForm->value('FeedDetail.feed_config_id')]) ?></li>
			<?php else: ?>
				<li><?php $this->BcBaser->link(__d('baser', 'フィードを追加'), ['controller' => 'feed_details', 'action' => 'add', $this->BcForm->value('FeedConfig.id')]) ?></li>
			<?php endif; ?>
		</ul>
	</td>
</tr>
