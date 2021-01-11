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
 *
 * @var BcAppView $this
 * @var string $fullpath
 * @var string $theme
 * @var string $plugin
 * @var string $type
 */

$writable = true;
if ((is_dir($fullpath) && !is_writable($fullpath)) || $theme == 'core') {
	$writable = false;
}
$params = explode('/', $path);
array_push($params, $data['name']);
?>


<tr>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--select">
		<?php if ($this->BcBaser->isAdminUser() && $theme != 'core'): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . str_replace('.', '_', h($data['name'])), [
					'type' => 'checkbox',
					'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>',
					'class' => 'batch-targets bca-checkbox__input',
					'value' => $data['name']]
			) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td" data-bca-text-size="md">
		<?php if ($data['type'] == 'image'): ?>
			<?php $this->BcBaser->link(
				$this->BcBaser->getImg(array_merge(['action' => 'img_thumb', 100, 100, $theme, $plugin, $type], $params), ['alt' => $data['name']]),
				array_merge(['action' => 'img', $theme, $plugin, $type], explode('/', $path), [$data['name']]),
				['rel' => 'colorbox', 'title' => $data['name'], 'style' => 'display:block;padding:5px;important;float:left;background-color:#FFFFFF'],
				false
			) ?>&nbsp;
			<?php echo $data['name'] ?>
		<?php elseif ($data['type'] == 'folder'): ?>
			<?php $this->BcBaser->link(
				'<i class="bca-icon--folder" data-bca-icon-size="md"></i>' . h($data['name']),
				array_merge(['action' => 'index', $theme, $plugin, $type], $params),
				['class' => '']
			) ?>/
		<?php else: ?>
			<?php if ($writable): ?>
				<?php $this->BcBaser->link(
					'<i class="bca-icon--file" data-bca-icon-size="md"></i>' . h($data['name']),
					array_merge(['action' => 'edit', $theme, $type], $params),
					['class' => '']
				) ?>
			<?php else: ?>
				<?php $this->BcBaser->link(
					'<i class="bca-icon--file" data-bca-icon-size="md"></i>' . h($data['name']),
					array_merge(['action' => 'view', $theme, $type], $params),
					['class' => '']
				) ?>
			<?php endif ?>
		<?php endif ?>
	</td>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php if ($writable): ?>
			<?php $this->BcBaser->link('', array_merge(['action' => 'ajax_copy', $theme, $type], $params), ['title' => __d('baser', 'コピー'), 'class' => 'btn-copy bca-btn-icon', 'data-bca-btn-type' => 'copy', 'data-bca-btn-size' => 'lg']) ?>
			<?php if ($data['type'] == 'folder'): ?>
				<?php $this->BcBaser->link('', array_merge(['action' => 'edit_folder', $theme, $type], $params), ['title' => __d('baser', '編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
			<?php else: ?>
				<?php $this->BcBaser->link('', array_merge(['action' => 'edit', $theme, $type], $params), ['title' => __d('baser', '編集'), 'escape' => false, 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
			<?php endif ?>
			<?php $this->BcBaser->link('', array_merge(['action' => 'ajax_del', $theme, $type], $params), ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php else: ?>
			<?php if ($data['type'] == 'folder'): ?>
				<?php $this->BcBaser->link('', array_merge(['action' => 'view_folder', $theme, $plugin, $type], $params), ['class' => 'btn-gray-s button-s bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']) ?>
			<?php else: ?>
				<?php $this->BcBaser->link('', array_merge(['action' => 'view', $theme, $plugin, $type], $params), ['class' => 'btn-gray-s button-s bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']) ?>
			<?php endif ?>
		<?php endif ?>
	</td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
</tr>
