<?php
/* SVN FILE: $Id$ */
/**
 * [MYPAGE] デフォルトレイアウト（デモ用）
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
<?php $this->BcBaser->charset() ?>
<?php $this->BcBaser->title() ?>
<?php $this->BcBaser->css(array(
	'jquery-ui/ui.all',
	'admin/import',
	'../js/jquery.contextMenu-1.0/jquery.contextMenu',
	'colorbox/colorbox')) ?>
<!--[if IE]><?php $bcBaser->js(array('excanvas')) ?><![endif]-->
<?php $bcBaser->js(array(
	'jquery-1.7.2.min',
	'jquery-ui-1.10.1.custom.min',
	'i18n/ui.datepicker-ja',
	'jquery.corner-2.12',
	'jquery.bt.min',
	'cb',
	'jquery.contextMenu-1.0/jquery.contextMenu',
	'jquery.form-2.94',
	'jquery.validate.min',
	'jquery.colorbox-min',
	'validate_messages_ja',
	'admin/functions',
	'admin/startup',
	'admin/adjust_scroll')) ?>
<?php $this->BcBaser->scripts() ?>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>" class="normal">
	<!-- Waiting -->
	<div id="Waiting" class="waiting-box" style="display:none">
		<div class="corner10">
		<?php echo $this->Html->image('ajax-loader.gif') ?><br />
		W A I T
		</div>
	</div>
	<?php $this->BcBaser->flash() ?>
	<?php $this->BcBaser->content() ?>
<?php echo $cakeDebug; ?>
</body>
</html>
