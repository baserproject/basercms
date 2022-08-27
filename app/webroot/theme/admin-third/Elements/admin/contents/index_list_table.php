<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツ一覧 テーブル
 *
 * @var BcAppView $this
 */

$this->BcListTable->setColumnNumber(8);
?>

<div class="bca-data-list__top">
	<?php if ($this->BcBaser->isAdminUser()): ?>
		<div class="bca-action-table-listup">
			<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['del' => __d('baser', '削除'), 'publish' => __d('baser', '公開'), 'unpublish' => __d('baser', '非公開')], 'empty' => __d('baser', '一括処理')]) ?>
			<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn']) ?>
		</div>
	<?php endif ?>
	<div class="bca-data-list__sub">
		<?php $this->BcBaser->element('pagination') ?>
	</div>
</div>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="list-table bca-table-listup sort-table" id="ListTable">
	<thead class="bca-table-listup__thead">
	<tr>
		<th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select" title="<?php echo __d('baser', '一括選択') ?>">
			<?php echo $this->BcForm->input('ListTool.checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser', '一括選択')]) ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('id',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'No'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'No')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('type',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'タイプ'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'タイプ')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('url',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'URL'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'URL')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
			<br>
			<?php echo $this->Paginator->sort('title',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'タイトル'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'タイトル')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('status',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '公開状態'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '公開状態')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('author_id',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '作成者'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '作成者')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
		</th>

		<?php echo $this->BcListTable->dispatchShowHead() ?>

		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('created_date',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '作成日'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '作成日')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
			<br>
			<?php echo $this->Paginator->sort('modified_date',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '更新日'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '更新日')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
		</th>
		<th class="list-tool bca-table-listup__thead-th">
			<?php echo __d('baser', 'アクション') ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($datas)): ?>
		<?php $count = 0; ?>
		<?php foreach($datas as $data): ?>
			<?php $this->BcBaser->element('contents/index_row_table', ['data' => $data, 'count' => $count]) ?>
			<?php $count++; ?>
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
