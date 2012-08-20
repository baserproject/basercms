<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] グロバールメニュー
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
 */
if(Configure::read('BcRequest.isMaintenance')) {
	return;
}
$prefix = '';
if(Configure::read('BcRequest.agent')) {
	$prefix = '/'.Configure::read('BcRequest.agentAlias');
}
?>
<ul class="global-menu clearfix">
	<?php if(empty($menuType)) $menuType = '' ?>
		<?php $globalMenus = $bcBaser->getMenus() ?>
		<?php if(!empty($globalMenus)): ?>
			<?php foreach($globalMenus as $key => $globalMenu): ?>
				<?php $no = sprintf('%02d',$key+1) ?>
				<?php if($globalMenu['GlobalMenu']['status']): ?>
					<?php if($key == 0): ?>
						<?php $class = ' class="first menu'.$no.'"' ?>
					<?php elseif($key == count($globalMenus) - 1): ?>
						<?php $class = ' class="last menu'.$no.'"' ?>
					<?php else: ?>
						<?php $class = ' class="menu'.$no.'"' ?>
					<?php endif ?>
					<?php if(!Configure::read('BcRequest.agent') && $this->base == '/index.php' && $globalMenu['GlobalMenu']['link'] == '/'): ?>
	<?php /* PC版トップページ */ ?>
	<li<?php echo $class ?>><?php echo str_replace('/index.php','',$bcBaser->link($globalMenu['GlobalMenu']['name'],$globalMenu['GlobalMenu']['link'])) ?></li>
					<?php else: ?>
	<li<?php echo $class ?>>
	<?php $bcBaser->link($globalMenu['GlobalMenu']['name'], $prefix.$globalMenu['GlobalMenu']['link']) ?>
	</li>
					<?php endif ?>
				<?php endif ?>
		<?php endforeach ?>
	<?php endif ?>
</ul>
