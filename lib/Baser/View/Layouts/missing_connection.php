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
			'admin/vendors/jquery-2.1.4.min',
			'admin/vendors/jquery-ui-1.11.4.min',
			'admin/vendors/jquery.colorbox-1.6.1.min',
			'admin/vendors/jquery-accessibleMegaMenu',
			'admin/libs/jquery.bcToken',
			'admin/functions',
			'admin/startup',
			'admin/libs/adjust_scroll',
			'admin/vendors/yuga',
			'startup']); ?>
			<?php $this->BcBaser->scripts() ?>
	</head>
	<body id="Error" class="normal front">
		<div id="Page" style="text-align: center">
			<div id="Logo"><?php $this->BcBaser->img('admin/logo_header.png', ['alt' => 'baserCMS', 'style' => 'display:block;padding-top:60px']) ?></div>
			<div id="Wrap">
				<div class="contents-body">
					<?php $this->BcBaser->content() ?>
				</div>
			</div>
		</div>
	<?php $this->BcBaser->func() ?>
	</body>
</html>
