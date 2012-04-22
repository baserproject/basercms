<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] レイアウト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$favoriteBoxOpened = $session->read('Baser.favorite_box_opened');
?>
<?php $baser->xmlHeader() ?>
<?php $baser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta name="robots" content="noindex,nofollow" />
<?php $baser->charset() ?>
<?php $baser->title() ?>
<?php $baser->css(array(
	'jquery-ui/ui.all',
	'admin/import', 
	'../js/jquery.contextMenu-1.0/jquery.contextMenu', 
	'colorbox/colorbox')) ?>
<!--[if IE]><?php $baser->js(array('excanvas')) ?><![endif]-->
<?php $baser->js(array(
	'jquery-1.6.2.min',
	'jquery-ui-1.8.14.custom.min',
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
	'admin/startup')) ?>
<?php $baser->scripts() ?>
</head>

<body id="<?php $baser->contentsName(true) ?>" class="normal">

<div id="Page">
	<div id="SaveFavoriteBoxUrl" style="display:none"><?php $baser->url(array('action' => 'ajax_save_favorite_box')) ?></div>
	<div id="SaveSearchBoxUrl" style="display:none"><?php $baser->url(array('action' => 'ajax_save_search_box', $baser->getContentsName(true))) ?></div>
	<div id="FavoriteBoxOpened" style="display:none"><?php echo (!empty($user))? $session->read('Baser.favorite_box_opened') : false ?></div>
	<div id="SearchBoxOpened" style="display:none"><?php echo $session->read('Baser.searchBoxOpened.'.$baser->getContentsName(true)) ?></div>
	<div id="CurrentPageName" style="display: none"><?php $baser->contentsTitle() ?></div>
	<div id="CurrentPageUrl" style="display: none"><?php echo '/'.$this->params['url']['url'] ?></div>

	<!-- Waiting -->
	<div id="Waiting" class="waiting-box" style="display:none">
		<div class="corner10">
		<?php echo $html->image('ajax-loader.gif') ?><br />
		W A I T
		</div>
	</div>

	<?php $baser->header() ?>

	<div id="Wrap" class="clearfix" style="display:none">

		<?php if(!empty($user)): ?>
		<div id="SideBar" <?php if(!$favoriteBoxOpened): ?> style="display:none"<?php endif ?>>
			<div class="cbb clearfix">

				<?php $baser->element('favorite_menu') ?>
				<?php $baser->element('permission') ?>

			<!-- / .cbb .clearfix --></div>
		<!-- / #SideBar --></div>
		<?php endif ?>

		<div id="Contents">

			<div class="cbb">

				<?php $baser->element('crumbs') ?>

				<div id="ContentsBody" class="contents-body clearfix">

					<div class="clearfix">
						<?php $baser->element('contents_menu') ?>
						<h1><?php $baser->contentsTitle() ?></h1>
					</div>
					
					<?php if($this->params['controller']!='installations' && $this->action != 'update'): ?>
					<?php $baser->updateMessage() ?>
					<?php endif ?>

					<?php if($this->params['controller']!='installations' && !empty($baser->siteConfig['first_access'])): ?>
					<div id="FirstMessage" class="em-box">
						BaserCMSへようこそ。短くスマートなURLを実現する「スマートURL」の設定は、
						<?php $baser->link('システム設定', '/admin/site_configs/form') ?>より行えます。
					</div>
					<?php endif ?>
					
					<?php $baser->flash() ?>

					<?php $baser->element('submenu') ?>

					<?php $baser->element('help') ?>

					<?php $baser->element('search') ?>

					<?php $baser->content() ?>



				<!-- / #ContentsBody .contents-body .clarfix --></div>

				<?php if(!empty($user)): ?>
				<div id="ToTop"><?php $baser->link('▲ トップへ', '#Header') ?></div>
				<?php endif ?>

			<!-- / .cbb --></div>

		<!-- / #Contents --></div>

	<!-- / #Wrap .clearfix --></div>

	<?php $baser->footer() ?>
	
<!-- / #Page --></div>

<?php $baser->element('credit') ?>
	
<?php $baser->func() ?>
</body>

</html>