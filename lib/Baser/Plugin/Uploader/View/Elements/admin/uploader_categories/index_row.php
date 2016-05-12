<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.View
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<td class="row-tools">
<?php if($this->BcBaser->isAdminUser()): ?>
		<?php echo $this->BcForm->checkbox('ListTool.batch_targets.'.$data['UploaderCategory']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['UploaderCategory']['id'])) ?>
<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['UploaderCategory']['id']), array('title' => '編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['UploaderCategory']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['UploaderCategory']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['UploaderCategory']['id'] ?></td>
	<td><?php echo $data['UploaderCategory']['name'] ?></td>
	<td>
		<?php echo $data['UploaderCategory']['created'] ?><br />
		<?php echo $data['UploaderCategory']['modified'] ?>
	</td>
</tr>
