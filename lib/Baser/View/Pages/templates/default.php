<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * 固定ページデフォルトテンプレート
 */
if($this->pageTitle && !$this->BcBaser->isHome()) {
	echo '<h1>' . $this->pageTitle . '</h1>';
}
$this->BcPage->content();
$this->BcBaser->updateInfo();

