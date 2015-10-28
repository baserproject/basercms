<?php
/**
 * [PUBLISH] デフォルトレイアウト
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
		<?php $this->BcBaser->css(array(
			'import',
			'admin/colorbox/colorbox')); ?>
		<!--[if IE]><?php $this->BcBaser->js(array('admin/excanvas')) ?><![endif]-->
		<?php $this->BcBaser->js(array(
			'admin/jquery-1.7.2.min',
			'admin/jquery-ui-1.8.19.custom.min',
			'admin/jquery.colorbox-min-1.4.5',
			'admin/jquery.mScroll',
			'admin/functions',
			'admin/startup',
			'admin/adjust_scroll',
			'admin/yuga')); ?>
			<?php $this->BcBaser->scripts() ?>
	</head>
	<body id="<?php $this->BcBaser->contentsName() ?>" class="normal">
		<div id="Page" style="text-align: center">
		<?php $this->BcBaser->img('admin/logo_header.png', array('alt' => 'baserCMS', 'style' => 'display:block;padding-top:60px')) ?>
			<div class="contents-body" style="text-align:left;width:1000px;margin-left:auto;margin-right:auto;margin-top:60px;background-color:#FFF;padding:40px;">
				<?php $this->BcBaser->content() ?>
			</div>
		</div>
	<?php $this->BcBaser->func() ?>
	</body>
</html>
