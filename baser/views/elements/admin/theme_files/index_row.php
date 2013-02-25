<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマファイル一覧　行
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
$writable = true;
if((is_dir($fullpath) && !is_writable($fullpath)) || $theme == 'core'){
	$writable = false;
}
?>


<tr>
	<td class="row-tools">
<?php if($bcBaser->isAdminUser()): ?>
	<?php echo $bcForm->checkbox('ListTool.batch_targets.'.str_replace('.','_',$data['name']), array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['name'])) ?>
<?php endif ?>
	<?php if($data['type']=='folder'): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_open_folder.png', array('width' => 24, 'height' => 24, 'alt' => '開く', 'class' => 'btn')), array('action' => 'index', $theme, $plugin, $type, $path, $data['name']), array('title' => '開く')) ?>
	<?php endif ?>

	<?php if($writable): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $theme, $type, $path, $data['name']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
		<?php if($data['type'] == 'folder'): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit_folder', $theme, $type, $path, $data['name']), array('title' => '編集')) ?>
		<?php else: ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $theme, $type, $path, $data['name']), array('title' => '編集')) ?>
		<?php endif ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_del', $theme, $type, $path, $data['name']), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php else: ?>
		<?php if($data['type']=='folder'): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_view.png', array('width' => 24, 'height' => 24, 'alt' => '表示', 'class' => 'btn')), array('action' => 'view_folder', $theme, $plugin, $type, $path, $data['name']), array('class' => 'btn-gray-s button-s')) ?>
		<?php else: ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_view.png', array('width' => 24, 'height' => 24, 'alt' => '表示', 'class' => 'btn')),array('action' => 'view', $theme, $plugin, $type, $path, $data['name']), array('class' => 'btn-gray-s button-s')) ?>
		<?php endif ?>
	<?php endif ?>
	</td>
	<td>
	<?php if($data['type'] == 'image'): ?>
		<?php $bcBaser->link(
				$bcBaser->getImg(array('action' => 'img_thumb', 100, 100, $theme, $plugin, $type, $path, $data['name']), array('alt'=>$data['name'])),
				array('action' => 'img', $theme, $plugin, $type, $path, $data['name']),
				array('rel' => 'colorbox', 'title' => $data['name'], 'style' => 'display:block;padding:10px;float:left;background-color:#FFFFFF'), null, false) ?>
		<?php echo $data['name'] ?>
	<?php elseif($data['type'] == 'folder'): ?>
		<?php $bcBaser->img('folder.gif', array('alt' => $data['name'])) ?>
		<?php echo $data['name'] ?>/
	<?php else: ?>
		<?php $bcBaser->img('file.gif', array('alt' => $data['name'])) ?>
		<?php echo $data['name'] ?>
	<?php endif ?>
	</td>
</tr>