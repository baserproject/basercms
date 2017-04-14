<?php
/**
 * [ADMIN] 検索インデックス一覧　テーブル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>
<script type="text/javascript">
$(function(){
	$(".priority").change(function() {
		var id = this.id.replace('ContentPriority', '');
		var priority = $(this).val();
		$.bcToken.check(function(){
			var data = {
				'data[Content][id]':id,
				'data[Content][priority]': priority,
				'data[_Token][key]': $.bcToken.key
			};
			$.ajax({
				type: "POST",
				url: $("#AjaxChangePriorityUrl").html()+'/'+id,
				data: data,
				beforeSend: function() {
					$("#flashMessage").slideUp();
					$("#PriorityAjaxLoader"+id).show();
				},
				success: function(result){
					if(!result) {
						$("#flashMessage").html('処理中にエラーが発生しました。');
						$("#flashMessage").slideDown();
					}
				},
				error: function() {
					$("#flashMessage").html('処理中にエラーが発生しました。');
					$("#flashMessage").slideDown();
				},
				complete: function() {
					$("#PriorityAjaxLoader"+id).hide();
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
	<div>
		<?php //$this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('action' => 'add')) ?>
	</div>
	<?php if ($this->BcBaser->isAdminUser()): ?>
		<div>
			<?php echo $this->BcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
			<?php echo $this->BcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('del' => '削除'), 'empty' => '一括処理')) ?>
			<?php echo $this->BcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
		</div>
	<?php endif ?>
</th>
<th>NO</th>
<th>タイプ<br />カテゴリー</th>
<th>タイトル</th>
<th>コンテンツ内容</th>
<th>公開状態</th>
<th>登録日<br />更新日</th>
</tr>
</thead>
<tbody>
	<?php if (!empty($datas)): ?>
		<?php $count = 0; ?>
		<?php foreach ($datas as $data): ?>
			<?php $this->BcBaser->element('contents/index_row', array('data' => $data, 'count' => $count)) ?>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>

<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
