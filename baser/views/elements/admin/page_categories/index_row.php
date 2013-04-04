<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリー一覧　行
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr id="Row<?php echo $data['PageCategory']['id'] ?>"<?php echo $rowGroupClass ?>>
	<td class="row-tools">
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['PageCategory']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['PageCategory']['id'])) ?>
<?php endif ?>
<?php if(in_array($data['PageCategory']['owner_id'], $allowOwners) || $bcAdmin->isSystemAdmin()): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['PageCategory']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['PageCategory']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['PageCategory']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
<?php endif ?>
<?php if($count != 1 || !isset($datas)): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_up.png', array('width' => 24, 'height' => 24, 'alt' => '上へ移動', 'class' => 'btn')), array('controller' => 'page_categories', 'action' => 'ajax_up', $data['PageCategory']['id']), array('class' => 'btn-up', 'title' => '上へ移動')) ?>
<?php else: ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_up.png', array('width' => 24, 'height' => 24, 'alt' => '上へ移動', 'class' => 'btn')), array('controller' => 'page_categories', 'action' => 'ajax_up', $data['PageCategory']['id']), array('class' => 'btn-up', 'title' => '上へ移動', 'style' => 'display:none')) ?>
<?php endif ?>
<?php if(count($datas) != $count || !isset($datas)): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_down.png', array('width' => 24, 'height' => 24, 'alt' => '下へ移動', 'class' => 'btn')), array('controller' => 'page_categories', 'action' => 'ajax_down', $data['PageCategory']['id']), array('class' => 'btn-down', 'title' => '下へ移動')) ?>
<?php else: ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_down.png', array('width' => 24, 'height' => 24, 'alt' => '下へ移動', 'class' => 'btn')), array('controller' => 'page_categories', 'action' => 'ajax_down', $data['PageCategory']['id']), array('class' => 'btn-down', 'title' => '下へ移動', 'style' => 'display:none')) ?>
<?php endif ?>
	</td>
	<td><?php echo $data['PageCategory']['id']; ?></td>
	<td>
<?php if(in_array($data['PageCategory']['owner_id'], $allowOwners) || $bcAdmin->isSystemAdmin()): ?>
		<?php $bcBaser->link($data['PageCategory']['name'], array('action' => 'edit', $data['PageCategory']['id'])); ?>
<?php else: ?>
		<?php echo $data['PageCategory']['name'] ?>
<?php endif ?>
	<?php if($bcBaser->siteConfig['category_permission']): ?>
	<br />
	<?php echo $bcText->arrayValue($data['PageCategory']['owner_id'], $owners) ?>
	<?php endif ?>
	</td>
	<td><?php echo $data['PageCategory']['title']; ?></td>
	<td style="white-space:nowrap"><?php echo $bcTime->format('Y-m-d', $data['PageCategory']['created']); ?><br />
		<?php echo $bcTime->format('Y-m-d', $data['PageCategory']['modified']); ?></td>
</tr>