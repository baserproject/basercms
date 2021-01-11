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
 * [PUBLISH] 固定ページコンテンツナビゲーション
 *
 * $this->BcBaser->contentsNavi() で呼び出す
 */
if (!BC_INSTALLED) {
	return;
}
?>


<?php if (!$this->BcBaser->isHome()): ?>
	<div class="contents-navi">
		<?php $this->BcPage->prevLink() ?>
		&nbsp;｜&nbsp;
		<?php $this->BcPage->nextLink() ?>
	</div>
<?php endif ?>
