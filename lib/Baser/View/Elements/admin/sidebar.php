<?php
/**
 * [ADMIN] サイドバー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 3.0.3
 * @license			http://basercms.net/license/index.html
 */
?>

<div id="SideBar">
	<div id="BtnSideBarOpener">＜</div>
	<div id="FavoriteArea">
		<?php $this->BcBaser->element('favorite_menu') ?>
		<?php $this->BcBaser->element('permission') ?>
	<!-- / .cbb .clearfix --></div>

<?php if(!empty($this->BcBaser->siteConfig['admin_side_banner'])): ?>
	<div id="BannerArea">
		<ul>
			<li><a href="https://market.basercms.net/" target="_blank"><img src="http://basercms.net/img/banner_baser_market.png" width="205" alt="baserマーケット" title="baserマーケット" /></a></li>
			<li><a href="http://magazine.basercms.net/" target="_blank"><img src="http://basercms.net/img/banner_basers_magazine.png" width="205" alt="basersマガジン" title="baserマーケット" /></a></li>
		</ul>
	</div>
<?php endif ?>

<!-- / #SideBar --></div>
