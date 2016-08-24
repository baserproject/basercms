<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] デフォルトレイアウト
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
			'admin/colorbox/colorbox-1.6.1')); ?>
		<!--[if IE]><?php $this->BcBaser->js(array('admin/vendors/excanvas')) ?><![endif]-->
		<?php $this->BcBaser->js(array(
			'admin/vendors/jquery-2.1.4.min',
			'admin/vendors/jquery-ui-1.11.4.min',
			'admin/vendors/jquery.colorbox-1.6.1.min',
			'admin/libs/jquery.mScroll',
			'admin/functions',
			'admin/startup',
			'admin/libs/adjust_scroll',
			'admin/vendors/yuga')); ?>
			<?php $this->BcBaser->scripts() ?>
	</head>
	<body id="<?php $this->BcBaser->contentsName() ?>" class="normal">
		<p style="text-align:center;font-weight: bold;">English Layout</p>
		<div id="Page" style="text-align: center">
		<?php $this->BcBaser->img('admin/logo_header.png', array('alt' => 'baserCMS', 'style' => 'display:block;padding-top:60px')) ?>
			<div class="contents-body" style="text-align:left;width:1000px;margin-left:auto;margin-right:auto;margin-top:60px;background-color:#FFF;padding:40px;">
				<?php $this->BcBaser->content() ?>
			</div>
		</div>
	<?php $this->BcBaser->func() ?>
	</body>
</html>
