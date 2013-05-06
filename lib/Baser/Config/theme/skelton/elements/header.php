<?php
/**
 * ヘッダー
 */
?>

<div id="Header">

	<?php $bcBaser->element('search') ?>

	<h1><?php $bcBaser->link($bcBaser->siteConfig['name'],'/') ?></h1>

	<div id="GlobalMenus">
		<?php $bcBaser->element('global_menu') ?>
	</div>

	<?php if(!$bcBaser->isTop()): ?>
	<div id="Navigation">
		<?php $bcBaser->element('crumbs'); ?>
	</div>
	<?php endif ?>

</div>