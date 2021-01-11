<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 3.0.3
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] サイドバー
 */
?>


<div id="SideBar">
	<div id="BtnSideBarOpener">＜</div>
	<div id="FavoriteArea">
		<?php $this->BcBaser->element('favorite_menu') ?>
		<?php $this->BcBaser->element('permission') ?>
		<!-- / .cbb .clearfix --></div>

	<?php if (!empty($this->BcBaser->siteConfig['admin_side_banner'])): ?>
		<div id="BannerArea">
			<ul>
				<li><a href="https://market.basercms.net/" target="_blank"><img
							src="https://basercms.net/img/banner_baser_market.png" width="205" alt="baserマーケット"
							title="baserマーケット"/></a></li>
				<li><a href="http://magazine.basercms.net/" target="_blank"><img
							src="https://basercms.net/img/banner_basers_magazine.png" width="205" alt="basersマガジン"
							title="baserマーケット"/></a></li>
			</ul>
		</div>
	<?php endif ?>

	<!-- / #SideBar --></div>
