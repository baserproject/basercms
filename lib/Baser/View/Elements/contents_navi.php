<?php
/**
 * [PUBLISH] 固定ページコンテンツナビゲーション
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * $this->BcBaser->contentsNavi() で呼び出す
 */
?>


<?php if(!$this->BcBaser->isHome()): ?>
<div id="ContentsNavi">
	<?php $this->BcPage->prevLink() ?>
	&nbsp;｜&nbsp;
	<?php $this->BcPage->nextLink() ?>
</div>
<?php endif ?>