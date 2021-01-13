<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマファイル一覧　行
 * @var \BcAppView $this
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
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . str_replace('.', '_', h($data['name'])), ['type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['name']]) ?>
		<?php endif ?>
		<?php if ($data['type'] == 'folder'): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_open_folder.png', ['alt' => __d('baser', '開く'), 'class' => 'btn']), array_merge(['action' => 'index', $theme, $plugin, $type], $params), ['title' => __d('baser', '開く')]) ?>
		<?php endif ?>
		<?php if ($writable): ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', ['alt' => __d('baser', 'コピー'), 'class' => 'btn']), array_merge(['action' => 'ajax_copy', $theme, $type], $params), ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy']) ?>
			<?php if ($data['type'] == 'folder'): ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), array_merge(['action' => 'edit_folder', $theme, $type], $params), ['title' => __d('baser', '編集')]) ?>
			<?php else: ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), array_merge(['action' => 'edit', $theme, $type], $params), ['title' => __d('baser', '編集'), 'escape' => false]) ?>
			<?php endif ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), array_merge(['action' => 'ajax_del', $theme, $type], $params), ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
		<?php else: ?>
			<?php if ($data['type'] == 'folder'): ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_view.png', ['alt' => __d('baser', '表示'), 'class' => 'btn']), array_merge(['action' => 'view_folder', $theme, $plugin, $type], $params), ['class' => 'btn-gray-s button-s']) ?>
			<?php else: ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_view.png', ['alt' => __d('baser', '表示'), 'class' => 'btn']), array_merge(['action' => 'view', $theme, $plugin, $type], $params), ['class' => 'btn-gray-s button-s']) ?>
			<?php endif ?>
		<?php endif ?>
	</td>
	<td>
		<?php if ($data['type'] == 'image'): ?>
			<?php
			$this->BcBaser->link(
				$this->BcBaser->getImg(array_merge(['action' => 'img_thumb', 100, 100, $theme, $plugin, $type], $params), ['alt' => $data['name']]), array_merge(['action' => 'img', $theme, $plugin, $type], explode('/', $path), [$data['name']]), ['rel' => 'colorbox', 'title' => $data['name'], 'style' => 'display:block;padding:5px;important;float:left;background-color:#FFFFFF'], null, false)
			?>&nbsp;
			<?php echo h($data['name']) ?>
		<?php elseif ($data['type'] == 'folder'): ?>
			<?php $this->BcBaser->img('admin/icon_folder.png', ['alt' => $data['name']]) ?>
			<?php echo h($data['name']) ?>/
		<?php else: ?>
			<?php $this->BcBaser->img('admin/icon_content.png', ['alt' => $data['name']]) ?>
			<?php echo h($data['name']) ?>
		<?php endif ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
</tr>
