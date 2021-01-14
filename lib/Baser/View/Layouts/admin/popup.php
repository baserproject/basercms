<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ポップアップレイアウト
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
		<?php $this->BcBaser->css('admin/import') ?>
		<!--[if IE]><?php $this->BcBaser->js(['admin/vendors/excanvas']) ?><![endif]-->
		<?php
		$this->BcBaser->js([
			'admin/vendors/jquery-2.1.4.min',
			'admin/vendors/jquery-ui-1.11.4.min',
			'admin/vendors/i18n/ui.datepicker-ja',
			'admin/vendors/jquery.bt.min',
			'admin/vendors/jquery.corner-2.12',
			'admin/functions'])
		?>
<?php $this->BcBaser->scripts() ?>
	</head>
	<body id="<?php $this->BcBaser->contentsName() ?>" class="popup">

		<!-- begin contentsBody -->
		<div id="contentsBody">
<?php $this->BcBaser->flash() ?>
<?php $this->BcBaser->content() ?>
		</div>
		<!-- end contentsBody -->

<?php $this->BcBaser->func() ?>
	</body>
</html>
