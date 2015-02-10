<?php
/**
 * [ADMIN] ポップアップレイアウト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.layout
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
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
		<!--[if IE]><?php $this->BcBaser->js(array('admin/excanvas')) ?><![endif]-->
		<?php
		$this->BcBaser->js(array(
			'jquery-1.4.2.min',
			'jquery-ui-1.8.19.custom.min',
			'admin/i18n/ui.datepicker-ja',
			'admin/jquery.bt.min',
			'admin/jquery.corner-2.12',
			'admin/functions'))
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