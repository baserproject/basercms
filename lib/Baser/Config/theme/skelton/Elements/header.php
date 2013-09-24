<?php
/**
 * ヘッダー
 */
?>

<div id="Header">

	<?php $this->BcBaser->element('search') ?>

	<h1><?php $this->BcBaser->link($this->BcBaser->siteConfig['name'],'/') ?></h1>

	<div id="GlobalMenus">
		<?php $this->BcBaser->element('global_menu') ?>
	</div>

	<?php if(!$this->BcBaser->isTop()): ?>
	<div id="Navigation">
		<?php $this->BcBaser->element('crumbs'); ?>
	</div>
	<?php endif ?>

</div>