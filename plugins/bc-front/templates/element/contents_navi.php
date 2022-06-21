<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

use BaserCore\View\AppView;
/**
 * コンテンツナビ
 * 呼出箇所：固定ページ
 *
 * BcBaserHelper::contentsNavi() で呼び出す
 * （例）<?php $this->BcBaser->contentsNavi() ?>
 *
 * @var AppView $this
 */
?>


<?php if(!$this->BcBaser->isHome() && $this->BcBaser->isPage()): ?>
	<div class="bs-contents-navi">
		<?php $this->BcContents->prevLink() ?><?php $this->BcContents->nextLink() ?>
	</div>
<?php endif ?>
