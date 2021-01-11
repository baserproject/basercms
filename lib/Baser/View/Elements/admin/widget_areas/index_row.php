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
 * [ADMIN] ウィジェットエリア一覧 行
 */
?>


<tr>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['WidgetArea']['id'], ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['WidgetArea']['id']]) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $data['WidgetArea']['id']], ['title' => __d('baser', '編集')]) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $data['WidgetArea']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
	</td>
	<td><?php echo $data['WidgetArea']['id']; ?></td>
	<td><?php $this->BcBaser->link($data['WidgetArea']['name'], ['action' => 'edit', $data['WidgetArea']['id']], ['escape' => true]); ?></td>
	<td><?php echo $data['WidgetArea']['count']; ?></td>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['WidgetArea']['created']); ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['WidgetArea']['modified']); ?></td>
</tr>
