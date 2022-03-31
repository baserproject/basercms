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
 * [ADMIN] プラグイン一覧　行
 *
 * @var BcAppView $this
 * @var bool $sortmode
 * @var array $corePlugins
 */

$classies = ['sortable'];
if (!$data['Plugin']['status']) {
	$classies[] = 'disablerow';
}
$class = ' class="' . implode(' ', $classies) . '"';
?>


<tr<?php echo $class; ?>>
	<td class="row-tools bca-table-listup__tbody-td">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['Plugin']['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['Plugin']['id']]) ?>
		<?php endif ?>
		<?php if ($sortmode): ?>
			<span class="sort-handle"><i class="bca-btn-icon-text"
										 data-bca-btn-type="draggable"></i><?php echo __d('baser', 'ドラッグ可能') ?></span>
			<?php echo $this->BcForm->input('Sort.id' . $data['Plugin']['id'], ['type' => 'hidden', 'class' => 'id', 'value' => $data['Plugin']['id']]) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php if ($data['Plugin']['old_version']): ?>
			<div class="annotation-text"><small><?php echo __d('baser', '新しいバージョンにアップデートしてください') ?></small></div>
		<?php elseif ($data['Plugin']['update']): ?>
			<div class="annotation-text"><small><?php echo __d('baser', 'アップデートを完了させてください') ?></small></div>
		<?php endif ?>
		<?php echo h($data['Plugin']['name']) ?><?php if ($data['Plugin']['title']): ?>（<?php echo h($data['Plugin']['title']) ?>）<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['Plugin']['version']) ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['Plugin']['description']) ?></td>
	<td class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($data['Plugin']['author'], $data['Plugin']['url'], ['target' => '_blank', 'escape' => true]) ?></td>
	<td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['Plugin']['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['Plugin']['modified']) ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php if ($data['Plugin']['update']): ?>
			<?php $this->BcBaser->link('', ['controller' => 'updaters', 'action' => 'plugin', $data['Plugin']['name']], [
				'aria-label' => __d('baser', 'このプラグインをアップデートする'),
				'title' => __d('baser', 'アップデート'),
				'class' => 'btn-update bca-btn-icon',
				'data-bca-btn-type' => 'update',
				'data-bca-btn-size' => 'lg'
			]); ?>
		<?php endif ?>
		<?php if ($data['Plugin']['admin_link'] && $data['Plugin']['status'] && !$data['Plugin']['update'] && !$data['Plugin']['old_version']): ?>
			<?php $this->BcBaser->link('', $data['Plugin']['admin_link'], [
				'aria-label' => 'このプラグインの設定を行う',
				'title' => __d('baser', '管理'),
				'class' => 'btn-setting  bca-btn-icon',
				'data-bca-btn-type' => 'setting',
				'data-bca-btn-size' => 'lg'
			]); ?>
		<?php endif; ?>
		<?php if ($data['Plugin']['status']): ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['Plugin']['name']], [
				'aria-label' => __d('baser', 'このプラグインを無効にする'),
				'title' => __d('baser', '無効'),
				'class' => 'btn-delete bca-btn-icon',
				'data-bca-btn-type' => 'unpublish',
				'data-bca-btn-size' => 'lg'
			]); ?>
		<?php elseif (!$data['Plugin']['status'] && !$data['Plugin']['update'] && !$data['Plugin']['old_version']): ?>
			<?php $this->BcBaser->link('',
				['action' => 'install', $data['Plugin']['name']], [
					'aria-label' => __d('baser', 'インストールする'),
					'title' => __d('baser', 'インストール'),
					'class' => 'bca-btn-icon',
					'data-bca-btn-type' => 'download',
					'data-bca-btn-size' => 'lg'
				]); ?>
		<?php endif ?>
		<?php if (!$data['Plugin']['status']): ?>
			<?php if (!in_array($data['Plugin']['name'], $corePlugins)): ?>
				<?php $this->BcBaser->link('',
					['action' => 'ajax_delete_file', $data['Plugin']['name']], [
						'aria-label' => __d('baser', 'このプラグインを削除する'),
						'title' => __d('baser', '削除'),
						'class' => 'btn-delete bca-btn-icon',
						'data-bca-btn-type' => 'delete',
						'data-bca-btn-size' => 'lg'
					]); ?>
			<?php endif ?>
		<?php endif; ?>
	</td>
</tr>
