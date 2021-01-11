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
 * [ADMIN] 検索インデックス一覧　テーブル
 *
 * @var \BcAppView $this
 */
$this->BcListTable->setColumnNumber(6);
?>


<script>
	$(function () {
		// 《一覧のロード時にイベント登録を行う為、外部ファイルに分けない》
		// 本来であれば、一覧のロード完了イベントを作成し、
		// そのタイミングでイベント登録をすべきだが、ロード完了イベントがないので応急措置とする
		$(".priority").change(function () {
			var id = this.id.replace('SearchIndexPriority', '');
			var priority = $(this).val();
			$.bcToken.check(function () {
				var data = {
					'data[SearchIndex][id]': id,
					'data[SearchIndex][priority]': priority,
					'data[_Token][key]': $.bcToken.key
				};
				return $.ajax({
					type: "POST",
					url: $("#AjaxChangePriorityUrl").html() + '/' + id,
					data: data,
					beforeSend: function () {
						$("#flashMessage").slideUp();
						$("#PriorityAjaxLoader" + id).show();
					},
					success: function (result) {
						if (!result) {
							$("#flashMessage").html('処理中にエラーが発生しました。');
							$("#flashMessage").slideDown();
						}
					},
					error: function () {
						$("#flashMessage").html('処理中にエラーが発生しました。');
						$("#flashMessage").slideDown();
					},
					complete: function () {
						$("#PriorityAjaxLoader" + id).hide();
					}
				});
			});
		});
	});
</script>


<!-- pagination -->
<?php $this->BcBaser->element('pagination') ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
	<tr>
		<th class="list-tool">
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<div>
					<?php echo $this->BcForm->checkbox('ListTool.checkall', ['title' => __d('baser', '一括選択')]) ?>
					<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['del' => __d('baser', '削除')], 'empty' => __d('baser', '一括処理')]) ?>
					<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled']) ?>
				</div>
			<?php endif ?>
		</th>
		<th>NO</th>
		<th><?php echo __d('baser', 'タイプ') ?><br/><?php echo __d('baser', 'タイトル') ?></th>
		<th><?php echo __d('baser', 'コンテンツ内容') ?></th>
		<th><?php echo __d('baser', '公開状態') ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th><?php echo __d('baser', '登録日') ?><br/><?php echo __d('baser', '更新日') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($datas)): ?>
		<?php $count = 0; ?>
		<?php foreach($datas as $data): ?>
			<?php $this->BcBaser->element('search_indices/index_row', ['data' => $data, 'count' => $count]) ?>
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

<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
