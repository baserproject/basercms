<?php
/**
 * [ADMIN] フィード設定　行
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
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['id'])) ?>
		<?php endif ?>		
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), $data['url'], array('title' => '確認', 'target' => '_blank')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('controller' => 'feed_details', 'action' => 'edit', $this->BcForm->value('FeedConfig.id'), $data['id']), array('title' => '編集')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('controller' => 'feed_details', 'action' => 'ajax_delete', $this->BcForm->value('FeedConfig.id'), $data['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td>
		<?php if ($data['url']): ?>
			<?php $this->BcBaser->link($data['name'], array('controller' => 'feed_details', 'action' => 'edit', $this->BcForm->value('FeedConfig.id'), $data['id'])) ?>
		<?php else: ?>
			<?php echo $data['name'] ?>
		<?php endif; ?>
	</td>
	<td><?php echo $data['category_filter'] ?></td>
	<td><?php echo $this->BcText->listValue('FeedDetail.cache_time', $data['cache_time']) ?></td>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['created']) ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['modified']) ?></td>
</tr>