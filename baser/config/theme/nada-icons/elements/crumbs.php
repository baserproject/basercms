<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ナビゲーション
 *
 * ページタイトルが直属のカテゴリ名と同じ場合は、直属のカテゴリ名を省略する
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
	$crumbs = $bcBaser->getCrumbs();
	if (!empty($crumbs)){
		foreach($crumbs as $key => $crumb){
			if($bcArray->last($crumbs, $key+1)) {
				if($crumbs[$key+1]['name'] == $crumb['name']) {
					continue;
				}
			}
			if($bcArray->last($crumbs, $key)) {
				if ($this->viewPath != 'home' && $crumb['name']){
					$bcBaser->addCrumb($crumb['name']);
				}elseif($this->name == 'CakeError'){
					$bcBaser->addCrumb('<strong>404 NOT FOUND</strong>');
				}
			} else {
				$bcBaser->addCrumb($crumb['name'], $crumb['url']);
			}
		}
	}
	$bcBaser->crumbs(' &gt; ','ホーム');
}
?>