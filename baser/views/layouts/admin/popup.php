<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ポップアップレイアウト
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views.layout
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php $bcBaser->xmlHeader() ?>
<?php $bcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta name="robots" content="noindex,nofollow" />
<?php $bcBaser->charset() ?>
<?php $bcBaser->title() ?>
<?php $bcBaser->metaDescription() ?>
<?php $bcBaser->metaKeywords() ?>
<?php $bcBaser->icon() ?>
<?php $bcBaser->css('admin/import') ?>
<!--[if IE]><?php $bcBaser->js(array('excanvas')) ?><![endif]-->
<?php $bcBaser->js(array(
	'jquery-1.4.2.min',
	'jquery.dimensions.min',
	'jquery-ui-1.7.2.custom.min',
	'i18n/ui.datepicker-ja',
	'jquery.bt.min',
	'jquery.corner',
	'functions')) ?>
<?php $bcBaser->scripts() ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>" class="popup">

	<!-- begin contentsBody -->
	<div id="contentsBody">
		<?php $bcBaser->flash() ?>
		<?php $bcBaser->content() ?>
	</div>
	<!-- end contentsBody -->

<?php echo $cakeDebug; ?>
</body>
</html>