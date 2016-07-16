<?php
/**
 * [PUBLISH] ウィジェットエリア
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
if (!isset($subDir)) {
	$subDir = true;
}
$this->BcBaser->includeCore('Elements/widget_area', array('subDir' => $subDir, 'no' => $no));
