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
 * [MYPAGE] デフォルトレイアウト（デモ用）
 */
?>
<?php $this->BcBaser->xmlHeader() ?>
<?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
	<head>
		<?php $this->BcBaser->charset() ?>
		<?php $this->BcBaser->title() ?>
		<?php
		$this->BcBaser->css([
			'admin/jquery-ui/jquery-ui.min',
			'admin/import',
			'../js/admin/jquery.contextMenu-1.0/jquery.contextMenu',
			'admin/colorbox/colorbox-1.6.1'])
		?>
	<!--[if IE]><?php $this->BcBaser->js(['admin/vendors/excanvas']) ?><![endif]-->
		<?php
		$this->BcBaser->js([
			'admin/vendors/jquery-2.1.4.min',
			'admin/vendors/jquery-ui-1.11.4.min',
			'admin/vendors/i18n/ui.datepicker-ja',
			'admin/vendors/jquery.corner-2.12',
			'admin/vendors/jquery.bt.min',
			'admin/vendors/jquery.contextMenu-1.0/jquery.contextMenu',
			'admin/vendors/jquery.form-2.94',
			'admin/vendors/jquery.validate.min',
			'admin/vendors/jquery.colorbox-1.6.1.min',
			'admin/libs/jquery.baseUrl',
			'admin/libs/credit',
			'admin/vendors/validate_messages_ja',
			'admin/functions',
			'admin/startup',
			'admin/libs/adjust_scroll'])
		?>
<?php $this->BcBaser->scripts() ?>
	</head>
	<div id="BaseUrl" style="display: none"><?php echo $this->request->base ?></div>
	<body id="<?php $this->BcBaser->contentsName() ?>" class="normal">
		<!-- Waiting -->
		<div id="Waiting" class="waiting-box" style="display:none">
			<div class="corner10">
		<?php echo $this->Html->image('admin/ajax-loader.gif') ?><br />
				W A I T
			</div>
		</div>
<?php $this->BcBaser->flash() ?>
<?php $this->BcBaser->content() ?>

<?php $this->BcBaser->func() ?>
	</body>
</html>
