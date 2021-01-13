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
 * [ADMIN] ヘルプ
 */
if (empty($help)) {
	return;
}
?>


<div id="Help" class="clearfix">

	<h2 class="head">
		<?php echo $this->BcBaser->getImg('admin/head_help.png', ['alt' => __d('baser', 'ヘルプ')]) . __d('baser', 'ヘルプ') ?>
	</h2>

	<div class="body">
		<?php $this->BcBaser->element('helps/' . $help) ?>
	</div>

	<div class="clearfix close">
		<div id="CloseHelp">
			<a><?php $this->BcBaser->img('admin/btn_close.png', ['width' => 14, 'height' => 14, 'alt' => 'Close', 'class' => 'btn']) ?></a>
		</div>
	</div>

	<!-- / #Help --></div>
