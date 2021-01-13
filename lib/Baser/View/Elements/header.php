<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] ヘッダー
 */
?>

<div id="Header">

	<?php $this->BcBaser->siteSearchForm() ?>

	<h1><?php $this->BcBaser->link(@$this->BcBaser->siteConfig['name'], '/') ?></h1>

	<div id="GlobalMenus">
		<?php $this->BcBaser->globalMenu() ?>
	</div>

	<?php if (!$this->BcBaser->isHome()): ?>
		<div id="Navigation">
			<?php $this->BcBaser->element('crumbs'); ?>
		</div>
	<?php endif ?>

</div>
