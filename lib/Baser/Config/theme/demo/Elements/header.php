<?php
/**
 * ヘッダー
 */
?>

<div id="header">
	<div id="headMain" class="clearfix">
		<h1><?php $this->bcBaser->link($this->bcBaser->siteConfig['name'],'/') ?></h1>
		<?php $this->bcBaser->element('search') ?>
	</div>
	<?php if($this->bcBaser->isTop()): ?>
	<?php $this->bcBaser->img('/img/img_top_main.jpg',array('alt'=>'Let\'s baserCMS','border'=>'0')) ?>
	<?php endif ?>
	<div id="glbMenus">
		<h2 class="display-none">グローバルメニュー</h2>
		<?php $this->bcBaser->element('global_menu') ?>
	</div>
	<?php if(!$this->bcBaser->isTop()): ?>
	<!-- navigation -->
	<div id="navigation">
		<?php $this->bcBaser->element('crumbs'); ?>
	</div>
	<?php endif ?>
</div>
