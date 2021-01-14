<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード設定一覧　テーブル
 * @var \BcAppView $this
 */
$this->BcListTable->setColumnNumber(5);
?>


<!-- pagination -->
<?php $this->BcBaser->element('pagination') ?>

<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<thead>
	<tr>
		<th style="width:160px" class="list-tool">
			<div>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', ['alt' => __d('baser', '新規追加')]) . __d('baser', '新規追加'), ['action' => 'add']) ?>
				　
			</div>
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<div>
					<?php echo $this->BcForm->checkbox('ListTool.checkall', ['title' => __d('baser', '一括選択')]) ?>
					<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['del' => __d('baser', '削除')], 'empty' => __d('baser', '一括処理')]) ?>
					<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled']) ?>
				</div>
			<?php endif ?>
		</th>
		<th><?php echo $this->Paginator->sort('id', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . 'NO', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . 'NO'], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<th><?php echo $this->Paginator->sort('name', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . ' ' . __d('baser', 'フィード設定名'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . ' ' . __d('baser', 'フィード設定名')], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<th><?php echo $this->Paginator->sort('display_number', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . ' ' . __d('baser', '表示件数'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . ' ' . __d('baser', '表示件数')], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th><?php echo $this->Paginator->sort('created', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . ' ' . __d('baser', '登録日'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . ' ' . __d('baser', '登録日')], ['escape' => false, 'class' => 'btn-direction']) ?>
			<br/>
			<?php echo $this->Paginator->sort('modified', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . ' ' . __d('baser', '更新日'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . ' ' . __d('baser', '更新日')], ['escape' => false, 'class' => 'btn-direction']) ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($feedConfigs)): ?>
		<?php foreach($feedConfigs as $data): ?>
			<?php $this->BcBaser->element('feed_configs/index_row', ['data' => $data]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
					class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>


<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
