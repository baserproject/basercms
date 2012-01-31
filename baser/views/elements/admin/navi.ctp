<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ナビゲーション
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if ($this->viewPath != 'dashboard'){
	$baser->addCrumb('ダッシュボード',array('plugin' => null, 'controller' => 'dashboard'));
}
if (!empty($navis)){
	foreach($navis as $key => $navi){
		$baser->addCrumb($key,$navi);
	}
}
if ($title_for_element){
	$baser->addCrumb('<strong>'.$title_for_element.'</strong>');
}
$baser->crumbs(' &gt; ');
?>