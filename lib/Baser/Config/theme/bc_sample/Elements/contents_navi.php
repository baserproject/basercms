<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツナビ
 * 呼出箇所：固定ページ
 *
 * BcBaserHelper::contentsNavi() で呼び出す
 * （例）<?php $this->BcBaser->contentsNavi() ?>
 *
 * @var BcAppView $this
 */
?>


<?php if(!$this->BcBaser->isHome() && $this->BcBaser->isPage()): ?>
	<div class="bs-contents-navi">
		<?php $this->BcPage->prevLink() ?><?php $this->BcPage->nextLink() ?>
	</div>
<?php endif ?>
