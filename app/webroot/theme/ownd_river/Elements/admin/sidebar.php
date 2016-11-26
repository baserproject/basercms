<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 3.0.3
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] サイドバー
 */
?>

<div id="SideBar">
	<div id="FavoriteArea">
		<?php $this->BcBaser->element('favorite_menu') ?>
		<?php $this->BcBaser->element('permission') ?>
	</div>

	<nav class="bca-nav-main" data-js-tmpl="AdminMenu" hidden>
		<h2 class="bca-nav-main-title">管理メニュー</h2>
		<div v-for="content in contentList" class="bca-nav-sub" v-bind:data-content-type="content.type">
			<h3 class="bca-nav-sub-title"><span class="bca-nav-favorite-title-label">{{ content.title }}</span></h3>
			<ul class="bca-nav-sub-list">
				<li v-for="subContent in content.menus" class="bca-nav-sub-list-item">
					<a v-bind:href="baseURL + subContent.url">
						<span class="bca-nav-sub-list-item-title">{{ subContent.title }}</span>
					</a>
				</li>
			</ul>
		</div>
	</nav>

<?php if(!empty($this->BcBaser->siteConfig['admin_side_banner'])): ?>
	<div id="BannerArea">
		<ul>
			<li><a href="https://market.basercms.net/" target="_blank"><img src="http://basercms.net/img/banner_baser_market.png" width="205" alt="baserマーケット" title="baserマーケット" /></a></li>
			<li><a href="http://magazine.basercms.net/" target="_blank"><img src="http://basercms.net/img/banner_basers_magazine.png" width="205" alt="basersマガジン" title="baserマーケット" /></a></li>
		</ul>
	</div>
<?php endif ?>
<!-- / #SideBar --></div>

<script id="AdminMenu" type="application/json">
<?php echo $this->BcAdmin->getJsonMenu() ?>
</script>