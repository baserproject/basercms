<?php
/**
 * [PUBLISH] ヘッダー
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>

<div id="Header">

	<?php $this->BcBaser->siteSearchForm() ?>

	<h1><?php $this->BcBaser->link(@$this->BcBaser->siteConfig['name'], '/') ?></h1>

	<div id="GlobalMenus">
		<?php $this->BcBaser->element('global_menu') ?>
	</div>

	<?php if (!$this->BcBaser->isHome()): ?>
		<div id="Navigation">
			<?php $this->BcBaser->element('crumbs'); ?>
		</div>
	<?php endif ?>

</div>