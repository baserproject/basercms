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
<div class="bca-search">
	<h2 class="head bca-search__head">
		<a
			href="javascript:void(0)"
			id="BtnMenuSearch"
			class="bca-icon--search"
		><?= __d('baser', '絞り込み検索') ?></a>
	</h2>
	<div id="Search" class="body bca-search__body">
		<?php $this->BcBaser->element('searches/' . $search) ?>
	</div>
</div><!-- / #Search clearfix -->
