<?php
/**
 * [ADMIN] フッター
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>
<div id="Footer">

	<div id="FooterInner" class="clearfix">

		<div id="FooterLogo">
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/logo_footer.png', array('width' => 155, 'height' => 30, 'alt' => 'baserCMSロゴ')), array('controller' => 'dashboard', 'action' => 'index')) ?>
		</div>

		<?php if (!empty($user)): ?>
			<div id="FooterMenu">
				<h2><?php $this->BcBaser->img('admin/head_menu.png', array('width' => 44, 'height' => 16, 'alt' => 'MENU')) ?></h2>
				<ul>
					<li><?php $this->BcBaser->link('固定ページ管理', array('plugin' => '', 'controller' => 'pages', 'action' => 'index')) ?></li>
					<li><?php $this->BcBaser->link('ウィジェット管理', array('plugin' => '', 'controller' => 'widget_areas', 'action' => 'index')) ?></li>
					<li><?php $this->BcBaser->link('テーマ管理', array('plugin' => '', 'controller' => 'themes', 'action' => 'index')) ?></li>
					<li><?php $this->BcBaser->link('プラグイン管理', array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')) ?></li>
					<li><?php $this->BcBaser->link('システム管理', array('plugin' => '', 'controller' => 'site_configs', 'action' => 'form')) ?></li>
				</ul>
			</div>
		<?php endif ?>

		<div id="FooterLink">
			<h2><?php $this->BcBaser->img('admin/head_link.png', array('width' => 36, 'height' => 16, 'alt' => 'LINK')) ?></h2>
			<ul>
				<li><a href="http://basercms.net/" target="_blank">baserCMS 公式サイト</a></li>
				<li><a href="http://sites.google.com/site/baserusers/" target="_blank">baserCMS ユーザー会</a></li>
				<li><a href="http://forum.basercms.net/" target="_blank">baserCMS ユーザーズフォーラム</a></li>
				<li><a href="http://project.e-catchup.jp/projects/basercms" target="_blank">baserCMS コア開発プロジェクト</a></li>
				<li><a href="http://www.facebook.com/basercms" target="_blank">baserCMS Facebook</a></li>
				<li><a href="http://twitter.com/basercms" target="_blank">baserCMS Twitter</a></li>
			</ul>
		</div>

		<div class="float-right">
			<ul id="FooterBanner">
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('baser.power.gif', array('alt' => 'baserCMS Power')), 'http://basercms.net/', array('target' => '_blank', 'title' => 'baserCMS Power')) ?></li>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('cake.power.gif', array('alt' => 'CakePHP Power')), 'http://cakephp.jp/', array('target' => '_blank', 'title' => 'CakePHP Power')) ?></li>
			</ul>
			<p id="BaserVersion">baserCMS <?php echo $baserVersion ?></p>
			<div id="Copyright">Copyright (C) baserCMS Users Community <?php $this->BcBaser->copyYear(2009) ?> All rights reserved.</div>
		</div>

		<!-- / #FooterInner --></div>

	<!-- / #Footer --></div>