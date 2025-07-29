<?php
/**
 * [PUBLISH] デフォルトレイアウト
 *
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
?>
<?php $this->BcBaser->xmlHeader() ?>
<?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
	<head>
		<meta name="robots" content="noindex,nofollow" />
		<?php $this->BcBaser->charset() ?>
		<?php $this->BcBaser->title() ?>
		<?php $this->BcBaser->metaDescription() ?>
		<?php $this->BcBaser->metaKeywords() ?>
		<?php $this->BcBaser->icon() ?>
		<?php $this->BcBaser->webClipIcon() ?>
		<?php $this->BcBaser->css([
			'import',
			'admin/colorbox/colorbox-1.6.1']); ?>
		<!--[if IE]><?php $this->BcBaser->js(['admin/vendors/excanvas']) ?><![endif]-->
		<?php $this->BcBaser->js([
			'admin/vendors/jquery-3.7.1.min',
			'admin/vendors/jquery-ui-1.14.1.min',
			'admin/vendors/jquery.colorbox-1.6.4.min',
			'admin/vendors/jquery-accessibleMegaMenu',
			'admin/libs/jquery.bcToken',
			'admin/functions',
			'admin/startup',
			'admin/libs/adjust_scroll',
			'admin/vendors/yuga',
			'startup']); ?>
			<?php $this->BcBaser->scripts() ?>
	</head>
	<body id="<?php $this->BcBaser->contentsName() ?>" class="normal front">
		<div id="Page" style="text-align: center">
			<?php if(BC_INSTALLED): ?>
			<?php $this->BcBaser->relatedSiteLinks() ?>
			<?php endif ?>
			<div id="Logo"><?php $this->BcBaser->img('admin/logo_header.png', ['alt' => 'baserCMS', 'style' => 'display:block;padding-top:60px']) ?></div>
			<?php if(BC_INSTALLED): ?>
			<nav id="GlobalMenu" class="clearfix"><?php $this->BcBaser->globalMenu(3) ?></nav>
			<?php endif ?>
			<div id="Wrap">
				<?php if(!$this->BcBaser->isHome()): ?>
				<div id="CrumbList" class="clearfix"><?php $this->BcBaser->crumbsList(['onSchema' => true]) ?></div>
				<?php endif ?>
				<div class="contents-body">
					<?php $this->BcBaser->content() ?>
					<?php $this->BcBaser->contentsNavi() ?>
					<?php $this->BcBaser->widgetArea() ?>
				</div>
			</div>
		</div>
	<?php $this->BcBaser->func() ?>
	</body>
</html>
