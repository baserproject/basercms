<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] プラグイン一覧　行
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
if(!$data['Plugin']['status']) {
	$class = ' class="disablerow"';
} else {
	$class = '';
}
?>


<tr<?php echo $class; ?>>
	<td class="row-tools">
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['Plugin']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['Plugin']['id'])) ?>
<?php endif ?>
<?php if($data['Plugin']['update']): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_update.png', array('width' => 24, 'height' => 24, 'alt' => 'アップデート', 'class' => 'btn')), array('controller' => 'updaters', 'action' => 'plugin', $data['Plugin']['name']), array('title' => 'アップデート', 'class' => 'btn-update')); ?>
<?php endif ?>
<?php if($data['Plugin']['admin_link'] && $data['Plugin']['status'] && !$data['Plugin']['update'] && !$data['Plugin']['old_version']): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_manage.png', array('width' => 24, 'height' => 24, 'alt' => '管理', 'class' => 'btn')), $data['Plugin']['admin_link'], array('title' => '管理')) ?>
<?php endif; ?>
<?php if($data['Plugin']['status']): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_disable.png', array('width' => 24, 'height' => 24, 'alt' => '無効', 'class' => 'btn')), array('action' => 'ajax_delete', $data['Plugin']['id']), array('title' => '無効', 'class' => 'btn-delete')) ?>
<?php elseif(!$data['Plugin']['status'] && !$data['Plugin']['update'] && !$data['Plugin']['old_version']): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_install.png', array('width' => 24, 'height' => 24, 'alt' => 'インストール', 'class' => 'btn')), array('action' => 'add', $data['Plugin']['name']), array('title' => 'インストール')) ?>
<?php endif ?>
<?php if(!$data['Plugin']['status']): ?>
	<?php if(!in_array($data['Plugin']['name'], $corePlugins)): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete_file', $data['Plugin']['name']), array('title' => '削除', 'class' => 'btn-delfile')); ?>
	<?php endif ?>
<?php endif; ?>
	</td>
	<td>
<?php if($data['Plugin']['old_version']): ?>
		<div class="annotation-text"><small>新しいバージョンにアップデートしてください</small></div>
<?php elseif($data['Plugin']['update']): ?>
		<div class="annotation-text"><small>アップデートを完了させてください</small></div>
<?php endif ?>
	<?php echo $data['Plugin']['name'] ?><?php if($data['Plugin']['title']): ?>（<?php echo $data['Plugin']['title'] ?>）<?php endif ?>
	</td>
	<td><?php echo $data['Plugin']['version'] ?></td>
	<td><?php echo $data['Plugin']['description'] ?></td>
	<td><?php $bcBaser->link($data['Plugin']['author'], $data['Plugin']['url'], array('target' => '_blank')) ?></td>
	<td style="width:10%;white-space: nowrap">
		<?php echo $bcTime->format('Y-m-d',$data['Plugin']['created']) ?><br />
		<?php echo $bcTime->format('Y-m-d',$data['Plugin']['modified']) ?>
	</td>
</tr>