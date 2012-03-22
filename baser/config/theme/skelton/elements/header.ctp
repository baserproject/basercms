<?php
/**
 * ヘッダー
 */
?>

<div id="Header">

	<?php $baser->element('search') ?>

	<h1><?php $baser->link($baser->siteConfig['name'],'/') ?></h1>

	<div id="GlobalMenus">
		<?php $baser->element('global_menu') ?>
	</div>

	<?php if(!$baser->isTop()): ?>
	<div id="Navigation">
		<?php $baser->element('crumbs'); ?>
	</div>
	<?php endif ?>

</div>