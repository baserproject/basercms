<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フッター
 */
?>


<div id="Footer">

	<div id="FooterInner" class="clearfix">

		<div id="FooterLogo">
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/logo_footer.png', ['width' => 155, 'height' => 30, 'alt' => __d('baser', 'baserCMSロゴ')]), ['controller' => 'dashboard', 'action' => 'index']) ?>
		</div>

		<?php if (!empty($user)): ?>
			<div id="FooterMenu">
				<h2><?php $this->BcBaser->img('admin/head_menu.png', ['width' => 44, 'height' => 16, 'alt' => 'MENU']) ?></h2>
				<ul>
					<li><?php $this->BcBaser->link(__d('baser', 'コンテンツ管理'), ['plugin' => '', 'controller' => 'contents', 'action' => 'index']) ?></li>
					<li><?php $this->BcBaser->link(__d('baser', 'ウィジェット管理'), ['plugin' => '', 'controller' => 'widget_areas', 'action' => 'index']) ?></li>
					<li><?php $this->BcBaser->link(__d('baser', 'テーマ管理'), ['plugin' => '', 'controller' => 'themes', 'action' => 'index']) ?></li>
					<li><?php $this->BcBaser->link(__d('baser', 'プラグイン管理'), ['plugin' => '', 'controller' => 'plugins', 'action' => 'index']) ?></li>
					<li><?php $this->BcBaser->link(__d('baser', 'システム管理'), ['plugin' => '', 'controller' => 'site_configs', 'action' => 'form']) ?></li>
				</ul>
			</div>
		<?php endif ?>

		<div id="FooterLink">
			<h2><?php $this->BcBaser->img('admin/head_link.png', ['width' => 36, 'height' => 16, 'alt' => 'LINK']) ?></h2>
			<ul>
				<li><a href="https://basercms.net/" target="_blank"><?php echo __d('baser', 'baserCMS 公式サイト') ?></a>
				</li>
				<li><a href="https://basercms.net/community/"
					   target="_blank"><?php echo __d('baser', 'baserCMS ユーザー会') ?></a></li>
				<li><a href="http://forum.basercms.net/"
					   target="_blank"><?php echo __d('baser', 'baserCMS ユーザーズフォーラム') ?></a></li>
				<li><a href="http://project.e-catchup.jp/projects/basercms"
					   target="_blank"><?php echo __d('baser', 'baserCMS コア開発プロジェクト') ?></a></li>
				<li><a href="https://www.facebook.com/basercms"
					   target="_blank"><?php echo __d('baser', 'baserCMS Facebook') ?></a></li>
				<li><a href="https://twitter.com/basercms"
					   target="_blank"><?php echo __d('baser', 'baserCMS Twitter') ?></a></li>
			</ul>
		</div>

		<div class="float-right">
			<ul id="FooterBanner">
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('baser.power.gif', ['alt' => 'baserCMS Power']), 'https://basercms.net/', ['target' => '_blank', 'title' => 'baserCMS Power']) ?></li>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('cake.power.gif', ['alt' => 'CakePHP Power']), 'https://cakephp.org/jp/', ['target' => '_blank', 'title' => 'CakePHP Power']) ?></li>
			</ul>
			<p id="BaserVersion">baserCMS <?php if (BcUtil::isAdminUser()) echo h($baserVersion); ?></p>
			<div id="Copyright">Copyright (C) baserCMS Users Community <?php $this->BcBaser->copyYear(2009) ?>.</div>
		</div>

		<!-- / #FooterInner --></div>

	<!-- / #Footer --></div>
