<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 0.1.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーザーグループ一覧　テーブル
 * @var BcAppView $this
 */

$this->BcListTable->setColumnNumber(5);
?>


<script type="text/javascript">
	$(function () {
		$('.tag a').css({'text-decoration': 'none'})
	});
</script>

<div class="bca-data-list__top">
	<div class="bca-data-list__sub">
		<!-- pagination -->
		<?php $this->BcBaser->element('pagination') ?>
	</div>
</div>

<table cellpadding="0" cellspacing="0" class="list-table bca-form-table" id="ListTable">
	<thead>
	<tr>
		<th class="bca-table-listup__thead-th"><?php echo $this->Paginator->sort('id',
				[
					'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', 'No'),
					'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', 'No')
				],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?></th>
		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('name',
				[
					'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', 'ユーザーグループ名'),
					'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', 'ユーザーグループ名')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			) ?>
		</th>
		<th class="bca-table-listup__thead-th"><?php echo $this->Paginator->sort('title', ['asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '表示名'), 'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', '表示名')], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('created', ['asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '登録日'), 'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', '登録日')], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
			<br>
			<?php echo $this->Paginator->sort('modified', ['asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '更新日'), 'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', '更新日')], ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
		</th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($datas)): ?>
		<?php foreach($datas as $data): ?>
			<?php $this->BcBaser->element('user_groups/index_row', ['data' => $data]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
					class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p>
			</td>
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
