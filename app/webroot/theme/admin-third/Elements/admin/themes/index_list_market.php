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
 * [ADMIN] テーマ一覧　テーブル
 */
?>


<script type="text/javascript">
	$(function () {
		$(".theme-popup").colorbox({inline: true, width: "60%"});
	});
</script>

<ul class="list-panel bca-list-panel">
	<?php if (!empty($baserThemes)): ?>
		<?php $key = 0 ?>
		<?php foreach($baserThemes as $data): ?>
			<?php $this->BcBaser->element('themes/index_row_market', ['data' => $data, 'key' => $key++]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<li class="no-data"><?php echo __d('baser', 'baserマーケットのテーマを読み込めませんでした。') ?></li>
	<?php endif; ?>
</ul>
