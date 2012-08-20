<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ナビゲーション
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 * @deprecated		2.0の次のバージョンで削除
 */
if ($this->viewPath != 'dashboard'){
	$bcBaser->addCrumb('ダッシュボード',array('plugin' => null, 'controller' => 'dashboard'));
}
if (!empty($navis)){
	foreach($navis as $key => $navi){
		$bcBaser->addCrumb($key,$navi);
	}
}
if ($title_for_element){
	$bcBaser->addCrumb('<strong>'.$title_for_element.'</strong>');
}
$bcBaser->crumbs(' &gt; ');
?>