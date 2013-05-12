<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] デフォルトレイアウト
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
<?php $this->BcBaser->xmlHeader() ?>
<?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta name="robots" content="noindex,nofollow" />
<?php $this->BcBaser->charset() ?>
<?php $this->BcBaser->title() ?>
<?php $bcBaser->css(array(
	'import',
	'colorbox/colorbox')) ?>
<!--[if IE]><?php $bcBaser->js(array('excanvas')) ?><![endif]-->
<?php $this->BcBaser->js(array(
	'jquery-1.7.2.min',
	'jquery-ui-1.8.19.custom.min',
	'jquery.colorbox-min',
	'admin/functions',
	'admin/startup',
	'admin/adjust_scroll')) ?>
<?php $this->BcBaser->scripts() ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>" class="normal">

	<div id="Page" style="text-align: center"><?php $bcBaser->img('admin/logo_header.png', array('alt' => 'baserCMS', 'style' => 'display:block;padding-top:280px')) ?></div>

<?php $bcBaser->func() ?>
</body>
</html>