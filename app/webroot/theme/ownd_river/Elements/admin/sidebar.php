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


<script id="AdminMenu" type="application/json">
<?php echo $this->BcAdmin->getJsonMenu() ?>
</script>


<div id="SideBar">
	<div id="FavoriteArea">
		<?php $this->BcBaser->element('favorite_menu') ?>
		<?php $this->BcBaser->element('permission') ?>
	</div>

	<nav class="bca-nav-main">
		<h2>管理メニュー</h2>
		<div class="bca-nav-sub" data-nav-icon="contents" data-nav-closed="false">
			<h3>コンテンツ管理</h3>
			<ul aria-hidden="false">
				<li class="bca-nav-item">
					<a href="#">
						<span class="bca-nav-item-title">コンテンツ一覧</span>
					</a>
				</li>
				<li class="bca-nav-item">
					<a href="#">
						<span class="bca-nav-item-title">新規追加</span>
					</a>
				</li>
				<li class="bca-nav-item">
					<a href="#">
						<span class="bca-nav-item-title">コンテンツ一覧</span>
					</a>
				</li>
			</ul>
		</div>
		<div class="bca-nav-sub" data-nav-icon="mail" data-nav-closed="true">
			<h3>お問い合わせ</h3>
			<ul aria-hidden="true">
				<li class="bca-nav-item">
					<a href="#">
						<span class="bca-nav-item-title">受信メール</span>
					</a>
				</li>
				<li class="bca-nav-item">
					<a href="#">
						<span class="bca-nav-item-title">メールフィールド</span>
					</a>
				</li>
				<li class="bca-nav-item">
					<a href="#">
						<span class="bca-nav-item-title">お問い合わせ設定</span>
					</a>
				</li>
				<li class="bca-nav-item">
					<a href="#">
						<span class="bca-nav-item-title">公開ページ</span>
					</a>
				</li>
			</ul>
		</div>
		<div class="bca-nav-sub" data-nav-icon="mail" data-nav-closed="true">
			<h3>資料請求</h3>
			<ul aria-hidden="true"></ul>
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

