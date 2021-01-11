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

/**
 * @var \BcAppView $this
 */
?>


<tr>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['UploaderCategory']['id'], ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['UploaderCategory']['id']]) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $data['UploaderCategory']['id']], ['title' => __d('baser', '編集')]) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', ['alt' => __d('baser', 'コピー'), 'class' => 'btn']), ['action' => 'ajax_copy', $data['UploaderCategory']['id']], ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy']) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'ajax_delete', $data['UploaderCategory']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
	</td>
	<td><?php echo $data['UploaderCategory']['id'] ?></td>
	<td><?php echo h($data['UploaderCategory']['name']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td>
		<?php echo $data['UploaderCategory']['created'] ?><br/>
		<?php echo $data['UploaderCategory']['modified'] ?>
	</td>
</tr>
