<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * Plugin Index
 *
 * @var AppView $this
 * @var bool $sortmode
 */

use BaserCore\View\AppView;

?>
<div class="bca-data-list__top">
	<!-- 一括処理 -->
	<?php if ($this->BcBaser->isAdminUser()): ?>
		<div>
			<?php echo $this->BcForm->input('ListTool.batch', ['type' => 'select', 'options' => ['del' => __d('baser', '無効')], 'empty' => __d('baser', '一括処理')]) ?>
			<?php echo $this->BcForm->button(__d('baser', '適用'), ['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn', 'data-bca-btn-size' => 'lg']) ?>
		</div>
	<?php endif ?>

</div>


<table cellpadding="0" cellspacing="0" class="list-table sort-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
	<tr class="list-tool">
		<th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select"><?php // 一括選択 ?>
			<?php if ($this->BcBaser->isAdminUser()): ?>
				<?php echo $this->BcForm->input('ListTool.checkall', ['type' => 'checkbox', 'label' => __d('baser', '一括選択')]) ?>
			<?php endif ?>
			<?php if (!$sortmode): ?>
				<?php $this->BcBaser->link('<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>' . __d('baser', '並び替え'), ['sortmode' => 1]) ?>
			<?php else: ?>
				<?php $this->BcBaser->link('<i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>' . __d('baser', 'ノーマル'), ['sortmode' => 0]) ?>
			<?php endif ?>
		</th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'プラグイン名') ?></th>
		<th class="bca-table-listup__thead-th" style="white-space: nowrap"><?php echo __d('baser', 'バージョン') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', '説明') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', '開発者') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', '登録日') ?><br><?php __d('baser', '更新日') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($datas)): ?>
		<?php foreach($datas as $data): ?>
			<?php $this->BcBaser->element('plugins/index_row', ['data' => $data]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="6"><p class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
