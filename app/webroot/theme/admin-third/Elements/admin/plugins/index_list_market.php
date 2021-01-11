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
 * [ADMIN] プラグイン一覧　テーブル
 */
?>


<table cellpadding="0" cellspacing="0" class="list-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
	<tr class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select">
		<th class="bca-table-listup__thead-th">&nbsp;</th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'プラグイン名') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'バージョン') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', '説明') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', '開発者') ?></th>
		<th class="bca-table-listup__thead-th"><?php echo __d('baser', '登録日') ?><br><?php echo __d('baser', '更新日') ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($baserPlugins)): ?>
		<?php foreach($baserPlugins as $data): ?>
			<?php $this->BcBaser->element('plugins/index_row_market', ['data' => $data]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="6">
				<p class="no-data"><?php echo __d('baser', 'baserマーケットのテーマを読み込めませんでした。') ?></p>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
