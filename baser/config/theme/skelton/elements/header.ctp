<?php
/**
 * ヘッダー
 */
?>

<div id="Header">

	<h1><?php $baser->link($baser->siteConfig['name'],'/') ?></h1>
	
	<div id="GlobalMenus">
		<?php $baser->element('global_menu') ?>
	</div>
	
	<?php if(!$baser->isTop()): ?>
	<div id="Navigation">
		<?php $baser->element('navi',array('title_for_element'=>$baser->getContentsTitle())); ?>
	</div>
	<?php endif ?>
	
</div>