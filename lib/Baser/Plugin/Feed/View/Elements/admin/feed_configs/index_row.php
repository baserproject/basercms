<?php
/**
 * [ADMIN] フィード設定一覧　行
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['FeedConfig']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['FeedConfig']['id'])) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), array('controller' => 'feed_configs', 'action' => 'preview', $data['FeedConfig']['id']), array('title' => '確認', 'target' => '_blank')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['FeedConfig']['id']), array('title' => '編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['FeedConfig']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['FeedConfig']['id']; ?></td>
	<td><?php $this->BcBaser->link($data['FeedConfig']['name'], array('action' => 'edit', $data['FeedConfig']['id'])) ?></td>
	<td><?php echo $data['FeedConfig']['display_number'] ?></td>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['FeedConfig']['created']); ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['FeedConfig']['modified']); ?></td>
</tr>
