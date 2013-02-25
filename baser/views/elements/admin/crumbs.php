<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] パンくずナビゲーション
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if ($this->viewPath != 'dashboard'){
	$bcBaser->addCrumb($bcBaser->getImg('admin/btn_home.png', array('width' => 15, 'height' => 12, 'alt' => 'Home')), array('plugin' => null, 'controller' => 'dashboard'));
}
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
				$bcBaser->addCrumb('<strong>'.$crumb['name'].'</strong>');
			}elseif($this->name == 'CakeError'){
				$bcBaser->addCrumb('<strong>404 NOT FOUND</strong>');
			}
		} else {
			$bcBaser->addCrumb($crumb['name'], $crumb['url']);
		}
	}
}
?>

<div id="Crumb">
<?php if(!empty($user)): ?>
	<?php $bcBaser->crumbs(' &gt; ') ?>&nbsp;
<?php else: ?>
	&nbsp;
<?php endif ?>
<!-- / #Crumb  --></div>
