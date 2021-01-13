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

<div id="SideBar" class="bca-nav">
	<?php $this->BcBaser->element('favorite_menu') ?>
	<?php $this->BcBaser->element('permission') ?>

	<nav class="bca-nav__main" data-js-tmpl="AdminMenu" hidden>
		<h2 class="bca-nav__main-title"><?php echo __d('baser', '管理メニュー') ?></h2>
		<div v-for="content in contentList" class="bca-nav__sub"
			 v-if="content.siteId === currentSiteId || content.siteId === null" v-bind:data-content-type="content.type"
			 v-bind:data-content-is-current="content.current" v-bind:data-content-is-expanded="content.expanded">
			<h3 class="bca-nav__sub-title">
				<a v-bind:href="baseURL + content.url" v-bind:class="'bca-nav__sub-title-label ' + content.icon"><span>{{ content.title }}</span></a>
			</h3>
			<ul v-if="content.menus.length" class="bca-nav__sub-list">
				<li v-for="subContent in content.menus" class="bca-nav__sub-list-item"
					v-bind:data-sub-content-is-current="subContent.current">
					<a v-bind:href="baseURL + subContent.url">
						<span class="bca-nav__sub-list-item-title">{{ subContent.title }}</span>
					</a>
				</li>
			</ul>
		</div>

		<div v-if="systemList.length" class="bca-nav__system" v-bind:data-content-is-expanded="isSystemSettingPage"
			 v-bind:data-bca-state="systemExpanded">
			<h3 class="bca-nav__system-title">
				<button class="bca-nav__sub-title-label" @click="openSystem">
					<span><?php echo __d('baser', '設定') ?></span> <i
						class="bca-icon--chevron-down bca-nav__system-title-icon"></i></button>
			</h3>
			<div class="bca-nav__system-list" v-bind:hidden="!systemExpanded">
				<div v-for="system in systemList" class="bca-nav__system-list-item"
					 v-bind:data-system-type="system.name.toLowerCase()"
					 v-bind:data-sub-content-is-expanded="system.expanded"
					 v-bind:data-sub-content-is-current="system.current">
					<h4 class="bca-nav__system-list-item-title">
						<a v-bind:href="baseURL + system.url"
						   v-bind:class="'bca-nav__system-list-item-title-label ' + system.icon"><span>{{ system.title }}</span></a>
					</h4>
					<ul v-if="system.menus && system.menus.length" class="bca-nav__system-sub-list">
						<li v-for="subSystem in system.menus" class="bca-nav__system-sub-list-item"
							v-bind:data-sub-item-is-current="subSystem.current">
							<a v-bind:href="baseURL + subSystem.url">
								<span class="bca-nav__system-sub-list-item-title">{{ subSystem.title }}</span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>

	<nav class="bca-nav__main" data-js-container="AdminMenu" hidden></nav>

	<?php if (!empty($this->BcBaser->siteConfig['admin_side_banner'])): ?>
		<div id="BannerArea" class="bca-banners">
			<ul class="bca-banners__ul">
				<li class="bca-banners__li"><a href="https://market.basercms.net/" target="_blank"><img
							src="https://basercms.net/img/banner_baser_market.png" width="205" alt="baserマーケット"
							title="baserマーケット"/></a></li>
				<li class="bca-banners__li"><a href="http://magazine.basercms.net/" target="_blank"><img
							src="https://basercms.net/img/banner_basers_magazine.png" width="205" alt="basersマガジン"
							title="baserマーケット"/></a></li>
			</ul>
		</div>
	<?php endif ?>
	<!-- / #SideBar --></div>

<script id="AdminMenu" type="application/json">
<?php echo $this->BcAdmin->getJsonMenu() ?>

</script>
