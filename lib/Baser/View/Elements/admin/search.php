<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] 検索ボックス
 */
if (empty($search)) {
	return;
}
?>

<div id="Search" class="clearfix">

	<h2 class="head">
		<?php $this->BcBaser->img('admin/head_search.png', ['alt' => __d('baser', '検索')]) ?><?php echo __d('baser', '検索') ?>
	</h2>

	<div class="body">
		<?php $this->BcBaser->element('searches/' . $search) ?>
	</div>

	<div class="clearfix close">
		<div id="CloseSearch">
			<a><?php $this->BcBaser->img('admin/btn_close.png', ['width' => 14, 'height' => 14, 'alt' => 'Close', 'class' => 'btn']) ?></a>
		</div>
	</div>

	<!-- / #Search clearfix --></div>
