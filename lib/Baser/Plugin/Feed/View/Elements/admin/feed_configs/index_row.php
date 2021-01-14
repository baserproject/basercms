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
 * [ADMIN] フィード設定一覧　行
 * @var \BcAppView $this
 */
?>


<tr>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['FeedConfig']['id'], ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['FeedConfig']['id']]) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', ['alt' => __d('baser', '確認'), 'class' => 'btn']), ['controller' => 'feed_configs', 'action' => 'preview', $data['FeedConfig']['id']], ['title' => __d('baser', '確認'), 'target' => '_blank']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $data['FeedConfig']['id']], ['title' => __d('baser', '編集')]) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $data['FeedConfig']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
	</td>
	<td><?php echo $data['FeedConfig']['id']; ?></td>
	<td><?php $this->BcBaser->link($data['FeedConfig']['name'], ['action' => 'edit', $data['FeedConfig']['id']], ['escape' => true]) ?></td>
	<td><?php echo $data['FeedConfig']['display_number'] ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['FeedConfig']['created']); ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['FeedConfig']['modified']); ?></td>
</tr>
