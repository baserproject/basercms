<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * コンテンツ一覧 テーブル
 */
?>


<?php $this->BcBaser->element('pagination') ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
	<tr>
		<th class="list-tool">
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<div>
					<?php echo $this->BcForm->checkbox('ListTool.checkall', ['title' => '一括選択']) ?>
					<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['del' => '削除', 'publish' => '公開', 'unpublish' => '非公開'], 'empty' => '一括処理']) ?>
					<?php echo $this->BcForm->button('適用', ['id' => 'BtnApplyBatch', 'disabled' => 'disabled']) ?>
				</div>
			<?php endif ?>
		</th>
		<th>NO</th>
		<th>タイプ</th>
		<th>URL<br />タイトル</th>
		<th>公開状態</th>
		<th>作成者</th>
		<th>作成日<br />更新日</th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($datas)): ?>
		<?php $count = 0; ?>
		<?php foreach ($datas as $data): ?>
			<?php $this->BcBaser->element('contents/index_row_table', array('data' => $data, 'count' => $count)) ?>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="7"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>

<?php $this->BcBaser->element('list_num') ?>
