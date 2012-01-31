<?php
/**
 * ヘッダー
 */
?>

<div id="header">
	<div id="headMain" class="clearfix">
		<h1><?php $baser->link($baser->siteConfig['name'],'/') ?></h1>
		<?php $baser->element('search') ?>
	</div>
	<?php if($baser->isTop()): ?>
	<?php $baser->img('/img/img_top_main.jpg',array('alt'=>'Let\'s baserCMS','border'=>'0')) ?>
	<?php endif ?>
	<div id="glbMenus">
		<h2 class="display-none">グローバルメニュー</h2>
		<?php $baser->element('global_menu') ?>
	</div>
	<?php if(!$baser->isTop()): ?>
	<!-- navigation -->
	<div id="navigation">
		<?php $baser->element('navi',array('title_for_element'=>$baser->getContentsTitle())); ?>
	</div>
	<?php endif ?>
</div>
