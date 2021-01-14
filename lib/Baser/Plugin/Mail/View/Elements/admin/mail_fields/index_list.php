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
 * [ADMIN] メールフィールド 一覧　テーブル
 * @var \BcAppView $this
 */
$this->BcListTable->setColumnNumber(7);
?>


<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
	<tr>
		<th class="list-tool">
			<div>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', ['alt' => __d('baser', '新規追加')]) . __d('baser', '新規追加'), ['action' => 'add', $mailContent['MailContent']['id']]) ?>
				　
				<?php if (!$sortmode): ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_sort.png', ['alt' => __d('baser', '並び替え')]) . __d('baser', '並び替え'), [$mailContent['MailContent']['id'], 'sortmode' => 1]) ?>
				<?php else: ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_sort.png', ['alt' => __d('baser', 'ノーマル')]) . __d('baser', 'ノーマル'), [$mailContent['MailContent']['id'], 'sortmode' => 0]) ?>
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
		<th><?php echo __d('baser', 'フィールド名') ?><br/><?php echo __d('baser', '項目名') ?></th>
		<th><?php echo __d('baser', 'タイプ') ?></th>
		<th><?php echo __d('baser', 'グループ名') ?></th>
		<th><?php echo __d('baser', '必須') ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th><?php echo __d('baser', '登録日') ?><br/><?php echo __d('baser', '更新日') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($datas)): ?>
		<?php $count = 1; ?>
		<?php foreach($datas as $data): ?>
			<?php $this->BcBaser->element('mail_fields/index_row', ['data' => $data, 'count' => $count]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
					class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
