<?php
/**
 * ヘッダー
 */
?>

<div id="header">
	<div id="headMain" class="clearfix">
		<h1><?php $bcBaser->link($bcBaser->siteConfig['name'],'/') ?></h1>
		<?php $bcBaser->element('search') ?>
	</div>
	<?php if($bcBaser->isTop()): ?>
	<?php $bcBaser->img('/img/img_top_main.jpg',array('alt'=>'Let\'s baserCMS','border'=>'0')) ?>
	<?php endif ?>
	<div id="glbMenus">
		<h2 class="display-none">グローバルメニュー</h2>
		<?php $bcBaser->element('global_menu') ?>
	</div>
	<?php if(!$bcBaser->isTop()): ?>
	<!-- navigation -->
	<div id="navigation">
		<?php $bcBaser->element('crumbs'); ?>
	</div>
	<?php endif ?>
</div>
