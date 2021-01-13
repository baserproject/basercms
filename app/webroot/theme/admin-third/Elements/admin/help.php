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


<div id="Help" class="clearfix bca-help">

	<h2 class="head bca-help__title">
		<i class="bca-icon--question-circle" data-bca-btn-size="md"></i>
		<?php echo __d('baser', 'ヘルプ') ?>
	</h2>

	<div class="body bca-help__body">
		<?php $this->BcBaser->element('helps/' . $help) ?>
	</div>

	<!-- / #Help --></div>
