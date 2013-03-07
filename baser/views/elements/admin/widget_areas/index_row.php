<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ウィジェットエリア一覧 行
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<td class="row-tools">
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['WidgetArea']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['WidgetArea']['id'])) ?>
<?php endif ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['WidgetArea']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['WidgetArea']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['WidgetArea']['id']; ?></td>
	<td><?php $bcBaser->link($data['WidgetArea']['name'],array('action'=>'edit', $data['WidgetArea']['id'])); ?></td>
	<td><?php echo $data['WidgetArea']['count']; ?></td>
	<td><?php echo $bcTime->format('Y-m-d',$data['WidgetArea']['created']); ?><br />
		<?php echo $bcTime->format('Y-m-d',$data['WidgetArea']['modified']); ?></td>
</tr>