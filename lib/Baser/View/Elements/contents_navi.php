<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] 固定ページコンテンツナビゲーション
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */

/**
 * $this->BcBaser->contentsNavi() で呼び出す
 */
if (!isset($this->BcPage) || !$this->BcPage->contensNaviAvailable()) {
	return;
}
?>

<div id="ContentsNavi">
	<?php $this->BcPage->prevLink() ?>
	&nbsp;｜&nbsp;
	<?php $this->BcPage->nextLink() ?>
</div>