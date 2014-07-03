<?php
/**
 * [ADMIN] テーマファイル一覧　行
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$writable = true;
if ((is_dir($fullpath) && !is_writable($fullpath)) || $theme == 'core') {
	$writable = false;
}
$params = explode('/', $path);
array_push($params, $data['name']);
?>


<tr>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . str_replace('.', '_', $data['name']), array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['name'])) ?>
		<?php endif ?>
		<?php if ($data['type'] == 'folder'): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_open_folder.png', array('width' => 24, 'height' => 24, 'alt' => '開く', 'class' => 'btn')), array_merge(array('action' => 'index', $theme, $plugin, $type), $params), array('title' => '開く')) ?>
		<?php endif ?>



		<?php if ($writable): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array_merge(array('action' => 'ajax_copy', $theme, $type), $params), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
			<?php if ($data['type'] == 'folder'): ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array_merge(array('action' => 'edit_folder', $theme, $type), $params), array('title' => '編集')) ?>
			<?php else: ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array_merge(array('action' => 'edit', $theme, $type), $params), array('title' => '編集', 'escape' => false)) ?>
			<?php endif ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array_merge(array('action' => 'ajax_del', $theme, $type), $params), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php else: ?>
			<?php if ($data['type'] == 'folder'): ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_view.png', array('width' => 24, 'height' => 24, 'alt' => '表示', 'class' => 'btn')), array_merge(array('action' => 'view_folder', $theme, $plugin, $type), $params), array('class' => 'btn-gray-s button-s')) ?>
			<?php else: ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_view.png', array('width' => 24, 'height' => 24, 'alt' => '表示', 'class' => 'btn')), array_merge(array('action' => 'view', $theme, $plugin, $type), $params), array('class' => 'btn-gray-s button-s')) ?>
			<?php endif ?>
		<?php endif ?>
	</td>
	<td>
		<?php if ($data['type'] == 'image'): ?>
			<?php
			$this->BcBaser->link(
				$this->BcBaser->getImg(array_merge(array('action' => 'img_thumb', 100, 100, $theme, $plugin, $type), $params), array('alt' => $data['name'])), array_merge(array('action' => 'img', $theme, $plugin, $type), explode('/', $path), array($data['name'])), array('rel' => 'colorbox', 'title' => $data['name'], 'style' => 'display:block;padding:5px;important;float:left;background-color:#FFFFFF'), null, false)
			?>&nbsp;
			<?php echo $data['name'] ?>
		<?php elseif ($data['type'] == 'folder'): ?>
			<?php $this->BcBaser->img('admin/folder.gif', array('alt' => $data['name'])) ?>
			<?php echo $data['name'] ?>/
		<?php else: ?>
			<?php $this->BcBaser->img('admin/file.gif', array('alt' => $data['name'])) ?>
			<?php echo $data['name'] ?>
		<?php endif ?>
	</td>
</tr>