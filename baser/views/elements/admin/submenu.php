<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] サブメニュー
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
if(!empty($user)) {
	$elementPath = 'submenus'.DS;
	if (!empty($subMenuElements)){ ?>
<div id="SubMenu" class="clearfix">
	<table class="sub-menu">
<?php
		foreach ($subMenuElements as $subMenuElement){
			$bcBaser->element($elementPath.$subMenuElement);
		}
?>
	</table>
</div>
<?php
	}
}
?>