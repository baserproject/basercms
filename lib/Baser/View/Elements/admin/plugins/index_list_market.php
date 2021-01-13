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


<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<thead>
	<tr class="list-tool">
		<th>&nbsp;</th>
		<th><?php echo __d('baser', 'プラグイン名') ?></th>
		<th style="white-space: nowrap"><?php echo __d('baser', 'バージョン') ?></th>
		<th><?php echo __d('baser', '説明') ?></th>
		<th><?php echo __d('baser', '開発者') ?></th>
		<th><?php echo __d('baser', '登録日') ?><br/><?php echo __d('baser', '更新日') ?></th>
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
				<?php if (strtotime('2014-03-31 17:00:00') >= time()): ?>
					<p class="no-data"><?php echo __d('baser', 'baserマーケットは、2014年3月31日 17時に公開です。お楽しみに！') ?></p>
				<?php else: ?>
					<p class="no-data"><?php echo __d('baser', 'baserマーケットのテーマを読み込めませんでした。') ?></p>
				<?php endif ?>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
