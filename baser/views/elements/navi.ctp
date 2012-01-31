<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ナビゲーション
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
if ($this->viewPath == 'home'){
	echo '<strong>ホーム</strong>';
}else{
	$crumbs = $baser->getCrumbs();
	if (!empty($crumbs)){
		foreach($crumbs as $crumb){
			$baser->addCrumb($crumb['name'], $crumb['url']);
		}
	}
	if ($this->viewPath != 'home' && $title_for_element){
		$baser->addCrumb('<strong>'.$title_for_element.'</strong>');
	}elseif($this->name == 'CakeError'){
		$baser->addCrumb('<strong>404 NOT FOUND</strong>');
	}
	$baser->crumbs(' &gt; ','ホーム');
}
?>