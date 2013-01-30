<?php
/**
 * ヘッダー
 */
?>

<div id="Header">

	<?php $this->bcBaser->element('search') ?>

	<h1><?php $this->bcBaser->link($this->bcBaser->siteConfig['name'],'/') ?></h1>

	<div id="GlobalMenus">
		<?php $this->bcBaser->element('global_menu') ?>
	</div>

	<?php if(!$this->bcBaser->isTop()): ?>
	<div id="Navigation">
		<?php $this->bcBaser->element('crumbs'); ?>
	</div>
	<?php endif ?>

</div>