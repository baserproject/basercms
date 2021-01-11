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
 * [ADMIN] アクセス制限設定一覧
 *
 * @var \BcAppView $this
 */
$this->BcListTable->setColumnNumber(5);
?>


<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
	<tr>
		<th class="list-tool">
			<div>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', ['alt' => __d('baser', '新規追加')]) . __d('baser', '新規追加'), ['action' => 'add', $this->request->params['pass'][0]]) ?>
				　
				<?php if (!$sortmode): ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_sort.png', ['alt' => __d('baser', '並び替え')]) . __d('baser', '並び替え'), ['sortmode' => 1, $this->request->params['pass'][0]]) ?>
				<?php else: ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_sort.png', ['alt' => __d('baser', 'ノーマル')]) . __d('baser', 'ノーマル'), ['sortmode' => 0, $this->request->params['pass'][0]]) ?>
				<?php endif ?>
			</div>
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<div>
					<?php echo $this->BcForm->checkbox('ListTool.checkall', ['title' => __d('baser', '一括選択')]) ?>
					<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['publish' => __d('baser', '有効'), 'unpublish' => __d('baser', '無効'), 'del' => __d('baser', '削除')], 'empty' => __d('baser', '一括処理')]) ?>
					<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled']) ?>
				</div>
			<?php endif ?>
		</th>
		<th>NO</th>
		<th><?php echo __d('baser', 'ルール名') ?><br><?php echo __d('baser', 'URL設定') ?></th>
		<th><?php echo __d('baser', 'アクセス') ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th><?php echo __d('baser', '登録日') ?><br><?php echo __d('baser', '更新日') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($datas)): ?>
		<?php foreach($datas as $data): ?>
			<?php $this->BcBaser->element('permissions/index_row', ['data' => $data]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
					class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
