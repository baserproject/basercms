<?php
/**
 * [ADMIN] ページカテゴリー一覧　行
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr id="Row<?php echo $data['PageCategory']['id'] ?>"<?php echo $rowGroupClass ?>>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['PageCategory']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['PageCategory']['id'])) ?>
		<?php endif ?>
		<?php if (in_array($data['PageCategory']['owner_id'], $allowOwners) || $this->BcAdmin->isSystemAdmin()): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['PageCategory']['id']), array('title' => '編集')) ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['PageCategory']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['PageCategory']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php endif ?>
		<?php if ($count != 1 || !isset($datas)): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_up.png', array('width' => 24, 'height' => 24, 'alt' => '上へ移動', 'class' => 'btn')), array('controller' => 'page_categories', 'action' => 'ajax_up', $data['PageCategory']['id']), array('class' => 'btn-up', 'title' => '上へ移動')) ?>
		<?php else: ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_up.png', array('width' => 24, 'height' => 24, 'alt' => '上へ移動', 'class' => 'btn')), array('controller' => 'page_categories', 'action' => 'ajax_up', $data['PageCategory']['id']), array('class' => 'btn-up', 'title' => '上へ移動', 'style' => 'display:none')) ?>
		<?php endif ?>
		<?php if (!isset($datas) || count($datas) != $count): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_down.png', array('width' => 24, 'height' => 24, 'alt' => '下へ移動', 'class' => 'btn')), array('controller' => 'page_categories', 'action' => 'ajax_down', $data['PageCategory']['id']), array('class' => 'btn-down', 'title' => '下へ移動')) ?>
		<?php else: ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_down.png', array('width' => 24, 'height' => 24, 'alt' => '下へ移動', 'class' => 'btn')), array('controller' => 'page_categories', 'action' => 'ajax_down', $data['PageCategory']['id']), array('class' => 'btn-down', 'title' => '下へ移動', 'style' => 'display:none')) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['PageCategory']['id']; ?></td>
	<td>
		<?php if (in_array($data['PageCategory']['owner_id'], $allowOwners) || $this->BcAdmin->isSystemAdmin()): ?>
			<?php $this->BcBaser->link($data['PageCategory']['name'], array('action' => 'edit', $data['PageCategory']['id'])); ?>
		<?php else: ?>
			<?php echo $data['PageCategory']['name'] ?>
		<?php endif ?>
		<?php if ($this->BcBaser->siteConfig['category_permission']): ?>
			<br />
			<?php echo $this->BcText->arrayValue($data['PageCategory']['owner_id'], $owners) ?>
		<?php endif ?>
	</td>
	<td><?php echo $data['PageCategory']['title']; ?></td>
	<td style="white-space:nowrap"><?php echo $this->BcTime->format('Y-m-d', $data['PageCategory']['created']); ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['PageCategory']['modified']); ?></td>
</tr>
