<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ポップアップレイアウト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views.layout
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php $baser->xmlHeader() ?>
<?php $baser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta name="robots" content="noindex,nofollow" />
<?php $baser->charset() ?>
<?php $baser->title() ?>
<?php $baser->metaDescription() ?>
<?php $baser->metaKeywords() ?>
<?php $baser->icon() ?>
<?php $baser->css('admin/import') ?>
<!--[if IE]><?php $baser->js(array('excanvas')) ?><![endif]-->
<?php $baser->js(array(
	'jquery-1.4.2.min',
	'jquery.dimensions.min',
	'jquery-ui-1.7.2.custom.min',
	'i18n/ui.datepicker-ja',
	'jquery.bt.min',
	'jquery.corner',
	'functions')) ?>
<?php $baser->scripts() ?>
</head>
<body id="<?php $baser->contentsName() ?>" class="popup">

	<!-- begin contentsBody -->
	<div id="contentsBody">
		<?php $baser->flash() ?>
		<?php $baser->content() ?>
	</div>
	<!-- end contentsBody -->

<?php echo $cakeDebug; ?>
</body>
</html>