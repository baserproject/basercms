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
 *
 * @var BcAppView $this
 * @var string $theme
 * @var string $plugin
 * @var string $type
 */
$this->BcListTable->setColumnNumber(3);
?>


<div class="bca-data-list__top">
	<!-- 一括処理 -->
	<?php if ($this->BcBaser->isAdminUser() && $theme != 'core'): ?>
		<div class="bca-action-table-listup">
			<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select',
				'options' => [
					'del' => __d('baser', '削除')
				],
				'empty' => __d('baser', '一括処理'), 'data-bca-select-size' => 'lg']) ?>
			<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn', 'data-bca-btn-size' => 'lg']) ?>
		</div>
	<?php endif ?>
	<div class="bca-data-list__sub">
		<!-- pagination -->
		<?php $this->BcBaser->element('pagination') ?>
	</div>
</div>


<table class="list-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
	<tr>
		<th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select"<?php if ($this->BcBaser->isAdminUser() && $theme != 'core'): ?> title="<?php echo __d('baser', '一括選択') ?>"<?php endif ?>>
			<?php if ($this->BcBaser->isAdminUser() && $theme != 'core'): ?>
				<?php echo $this->BcForm->input('ListTool.checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser', '一括選択')]) ?>
			<?php endif ?>
			<?php if ($path): ?>
				<?php $this->BcBaser->link('', ['action' => 'index', $theme, $plugin, $type, dirname($path)], [
					'title' => __d('baser', '上へ移動'),
					'class' => 'bca-btn-icon',
					'data-bca-btn-type' => 'up-directory',
					'data-bca-btn-size' => 'lg',
					'data-bca-btn-status' => 'white',
					'aria-label' => __d('baser', '一つ上の階層へ')
				]) ?>
			<?php endif ?>
		</th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'フォルダ名') ?>
			／<?php echo __d('baser', 'テーマファイル名') ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th class="bca-table-listup__thead-th">
			<?php echo __d('baser', 'アクション') ?>
		</th>
	</tr>
	</thead>
	<tbody class="bca-table-listup__tbody">
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

<div class="bca-data-list__bottom">
	<div class="bca-data-list__sub">
		<!-- pagination -->
		<?php $this->BcBaser->element('pagination') ?>
		<!-- list-num -->
		<?php $this->BcBaser->element('list_num') ?>
	</div>
</div>
