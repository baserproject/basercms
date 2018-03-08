<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] プラグイン一覧　テーブル
 */
?>


<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
<thead>
	<tr class="list-tool">
		<th>
			<div>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', ['width' => 69, 'height' => 18, 'alt' => __d('baser', '新規追加'), 'class' => 'btn']), ['action' => 'add']) ?>
				<?php if (!$sortmode): ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_sort.png', ['width' => 65, 'height' => 14, 'alt' => __d('baser', '並び替え'), 'class' => 'btn']), ['sortmode' => 1]) ?>
				<?php else: ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_normal.png', ['width' => 65, 'height' => 14, 'alt' => __d('baser', 'ノーマル'), 'class' => 'btn']), ['sortmode' => 0]) ?>
				<?php endif ?>
			</div>
			<?php if ($this->BcBaser->isAdminUser()): ?>
			<div>
				
				<?php echo $this->BcForm->checkbox('ListTool.checkall', ['title' => __d('baser', '一括選択')]) ?>
				<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['del' => __d('baser', '一括無効')], 'empty' => __d('baser', '一括処理')]) ?>
				<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled']) ?>
			</div>
			<?php endif ?>
		</th>
		<th>プラグイン名</th>
		<th style="white-space: nowrap">バージョン</th>
		<th>説明</th>
		<th>開発者</th>
		<th>登録日<br />更新日</th>
	</tr>
</thead>
<tbody>
	<?php if (!empty($datas)): ?>
		<?php foreach ($datas as $data): ?>
			<?php $this->BcBaser->element('plugins/index_row', ['data' => $data]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="6"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
