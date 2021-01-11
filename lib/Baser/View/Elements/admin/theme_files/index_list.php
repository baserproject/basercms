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
 * [ADMIN] テーマファイル一覧　テーブル
 * @var \BcAppView $this
 */
$this->BcListTable->setColumnNumber(2);
?>


<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<thead>
	<tr>
		<th style="width:160px" class="list-tool">
			<div>
				<?php if ($this->BcBaser->isAdminUser() && $theme != 'core'): ?>
					<?php echo $this->BcForm->checkbox('ListTool.checkall', ['title' => __d('baser', '一括選択')]) ?>
					<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['del' => __d('baser', '削除')], 'empty' => __d('baser', '一括処理')]) ?>
					<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled']) ?>
				<?php endif ?>
				<?php if ($path): ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/up.gif', ['alt' => __d('baser', '上へ移動')]), ['action' => 'index', $theme, $plugin, $type, dirname($path)], ['title' => __d('baser', '上へ移動')]) ?>
				<?php endif ?>
			</div>
		</th>
		<th><?php echo __d('baser', 'フォルダ名') ?>／<?php echo __d('baser', 'テーマファイル名') ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($themeFiles)): ?>
		<?php foreach($themeFiles as $data): ?>
			<?php $this->BcBaser->element('theme_files/index_row', ['data' => $data]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
					class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
