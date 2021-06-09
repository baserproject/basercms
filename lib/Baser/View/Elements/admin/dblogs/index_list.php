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
 * [ADMIN] 最近の動き一覧 テーブル
 * <div class="bca-action-table-listup">
 */
$this->BcListTable->setColumnNumber(4);
?>
<div class="bca-data-list__top">
	<?php if (BcUtil::isAdminUser()): ?>
	<div class="submit clear bca-update-log__delete">
	<?php
		$this->BcBaser->link(
			__d('baser', 'ログを全て削除'),
			[
				'action' => 'del'
			],
			[
				'class' => 'btn-gray button submit-token bca-btn',
				'data-bca-btn-type' => 'delete'
			],
			__d('baser', '最近の動きのログを削除します。いいですか？')
		);
	?>
	</div>
	<?php endif ?>
	<!-- pagination -->
	<?php $this->BcBaser->element('pagination') ?>
</div>
<table class="list-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
	<tr>
		<th class="bca-table-listup__thead-th" style="max-width:100px">
		<?php
			echo $this->Paginator->sort('id',
				[
					'asc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'No'),
					'desc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'No')
				],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
		?>
		</th>
		<th class="bca-table-listup__thead-th">
		<?php
			echo $this->Paginator->sort(
				'name',
				[
					'asc' => '<i class="bca-icon--desc"></i>' . __d('baser', '内容'),
					'desc' => '<i class="bca-icon--asc"></i>' . __d('baser', '内容')
				],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
		?>
		</th>
		<th class="bca-table-listup__thead-th">
		<?php
			echo $this->Paginator->sort(
				'user_id',
				[
					'asc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'ユーザー'),
					'desc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'ユーザー')
				],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
		?>
		</th>
		<th class="bca-table-listup__thead-th">
		<?php
			echo $this->Paginator->sort(
				'created',
				[
					'asc' => '<i class="bca-icon--desc"></i>' . __d('baser', '操作日時'),
					'desc' => '<i class="bca-icon--asc"></i>' . __d('baser', '操作日時')
				],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
		?>
		</th>
	</tr>
	</thead>
	<tbody class="bca-table-listup__tbody">
	<?php if (!empty($logs)): ?>
		<?php
			foreach($logs as $log) {
				echo $this->element('dblogs/index_row', ['row' => $log]);
			}
		?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>">
			<p class="no-data">
				<?php echo __d('baser', 'データが見つかりませんでした。') ?>
			</p>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
