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
 * [ADMIN] ユーザー一覧　テーブル
 */
$this->BcListTable->setColumnNumber(6);
?>


<!-- pagination -->
<?php $this->BcBaser->element('pagination') ?>

<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<thead>
	<tr>
		<th style="width:140px" class="list-tool">
			<div>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', ['alt' => __d('baser', '新規追加')]) . __d('baser', '新規追加'), ['action' => 'add']) ?>
				　
			</div>
		</th>
		<th><?php echo $this->Paginator->sort('id', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . 'NO', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . 'NO'], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<th><?php echo $this->Paginator->sort('name', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . __d('baser', 'アカウント名'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . __d('baser', 'アカウント名')], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<th><?php echo $this->Paginator->sort('nickname', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . __d('baser', 'ニックネーム'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . __d('baser', 'ニックネーム')], ['escape' => false, 'class' => 'btn-direction']) ?></th>
		<th><?php echo $this->Paginator->sort('user_group_id', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . __d('baser', 'グループ'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . __d('baser', 'グループ')], ['escape' => false, 'class' => 'btn-direction']) ?>
			<br/>
			<?php echo $this->Paginator->sort('real_name_1', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . __d('baser', '氏名'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . __d('baser', '氏名')], ['escape' => false, 'class' => 'btn-direction']) ?>
		</th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th><?php echo $this->Paginator->sort('created', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . __d('baser', '登録日'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . __d('baser', '登録日')], ['escape' => false, 'class' => 'btn-direction']) ?>
			<br/>
			<?php echo $this->Paginator->sort('modified', ['asc' => $this->BcBaser->getImg('admin/blt_list_down.png', ['alt' => __d('baser', '昇順'), 'title' => __d('baser', '昇順')]) . __d('baser', '更新日'), 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', ['alt' => __d('baser', '降順'), 'title' => __d('baser', '降順')]) . __d('baser', '更新日')], ['escape' => false, 'class' => 'btn-direction']) ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($users)): ?>
		<?php foreach($users as $data): ?>
			<?php $this->BcBaser->element('users/index_row', ['data' => $data]) ?>
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
