<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] パンくずナビゲーション
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 1.7.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if ($this->viewPath != 'dashboard'){
	$baser->addCrumb($baser->getImg('admin/btn_home.png', array('width' => 15, 'height' => 12, 'alt' => 'Home')), array('plugin' => null, 'controller' => 'dashboard'));
}
$crumbs = $baser->getCrumbs();
if (!empty($crumbs)){
	foreach($crumbs as $crumb){
		$baser->addCrumb($crumb['name'], $crumb['url']);
	}
}
if ($currentTitle){
	$baser->addCrumb('<strong>'.$currentTitle.'</strong>');
}
?>

<div id="Crumb">
<?php if(!empty($user)): ?>
	<?php $baser->crumbs(' &gt; ') ?>&nbsp;
<?php else: ?>
	&nbsp;
<?php endif ?>
<!-- / #Crumb  --></div>
