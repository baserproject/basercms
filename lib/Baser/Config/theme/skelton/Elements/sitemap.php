<?php

/**
 * [PUBLISH] サイトマップ
 * 
 * カテゴリの階層構造を表現する為、再帰呼び出しを行う
 * $this->BcBaser->sitemap() で呼び出す
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
if (!isset($recursive)) {
	$recursive = 1;
}
$this->BcBaser->includeCore('Elements/sitemap', array('pageList' => $pageList, 'recursive' => $recursive));
