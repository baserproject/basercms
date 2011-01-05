<?php
/* SVN FILE: $Id$ */
/**
 * 管理画面 ポップアップレイアウト
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
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