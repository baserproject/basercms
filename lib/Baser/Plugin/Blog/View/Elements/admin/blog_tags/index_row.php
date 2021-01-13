<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログタグ一覧　行
 * @var \BcAppView $this
 */
?>


<tr>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['BlogTag']['id'], ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['BlogTag']['id']]) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $data['BlogTag']['id']], ['title' => __d('baser', '編集')]) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $data['BlogTag']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
	</td>
	<td><?php echo $data['BlogTag']['id'] ?></td>
	<td><?php $this->BcBaser->link(h($data['BlogTag']['name']), ['action' => 'edit', $data['BlogTag']['id']]) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['BlogTag']['created']); ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['BlogTag']['modified']); ?></td>
</tr>
