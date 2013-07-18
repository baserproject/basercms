<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] レイアウト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
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
<?php $bcBaser->css(array(
	'jquery-ui/ui.all',
	'admin/import',
	'../js/jquery.contextMenu-1.0/jquery.contextMenu',
	'colorbox/colorbox',
	'admin/toolbar')) ?>
<!--[if IE]><?php $bcBaser->js(array('excanvas')) ?><![endif]-->
<?php $bcBaser->js(array(
	'jquery-1.7.2.min',
	'jquery-ui-1.8.19.custom.min',
	'i18n/ui.datepicker-ja',
	'jquery.corner-2.12',
	'jquery.bt.min',
	'cb',
	'jquery.contextMenu-1.0/jquery.contextMenu',
	'jquery.form-2.94',
	'jquery.validate.min',
	'jquery.colorbox-min-1.4.5',
	'jquery.mScroll',
	'validate_messages_ja',
	'admin/functions',
	'admin/startup',
	'admin/adjust_scroll', 
	'yuga')) ?>
<?php $bcBaser->scripts() ?>
</head>

<body id="<?php $bcBaser->contentsName() ?>" class="normal">

<div id="Page">
	<div id="SaveFavoriteBoxUrl" style="display:none"><?php $bcBaser->url(array('action' => 'ajax_save_favorite_box')) ?></div>
	<div id="SaveSearchBoxUrl" style="display:none"><?php $bcBaser->url(array('action' => 'ajax_save_search_box', $bcBaser->getContentsName(true))) ?></div>
	<div id="FavoriteBoxOpened" style="display:none"><?php echo $favoriteBoxOpened ?></div>
	<div id="SearchBoxOpened" style="display:none"><?php echo $session->read('Baser.searchBoxOpened.'.$bcBaser->getContentsName(true)) ?></div>
	<div id="CurrentPageName" style="display: none"><?php $bcBaser->contentsTitle() ?></div>
	<div id="CurrentPageUrl" style="display: none"><?php echo '/'.$this->params['url']['url'] ?></div>

	<!-- Waiting -->
	<div id="Waiting" class="waiting-box" style="display:none">
		<div class="corner10">
		<?php echo $html->image('ajax-loader.gif') ?><br />
		W A I T
		</div>
	</div>

	<?php $bcBaser->header() ?>

	<div id="Wrap" class="clearfix" style="display:none">

		<?php if(!empty($user)): ?>
		<div id="SideBar">
			
			<div id="BtnSideBarOpener"></div>
			
			<div class="cbb clearfix">	
				
				<?php $bcBaser->element('favorite_menu') ?>
				<?php $bcBaser->element('permission') ?>

			<!-- / .cbb .clearfix --></div>
		<!-- / #SideBar --></div>
		<?php endif ?>

		<div id="Contents" class="clearfix">

			<div class="cbb">

				<?php $bcBaser->element('crumbs') ?>

				<div id="ContentsBody" class="contents-body clearfix">

					<div class="clearfix">
						<?php $bcBaser->element('contents_menu') ?>
						<h1><?php $bcBaser->contentsTitle() ?></h1>
					</div>

					<?php if($this->params['controller']!='installations' && $this->action != 'update'): ?>
					<?php $bcBaser->updateMessage() ?>
					<?php endif ?>

					<?php if($this->params['controller']!='installations' && !empty($bcBaser->siteConfig['first_access'])): ?>
					<div id="FirstMessage" class="em-box" style="text-align:left">
						baserCMSへようこそ。<br />
						<ul style="font-weight:normal;font-size:14px;"><li>画面右上の「システムナビ」より管理システムの全ての機能にアクセスする事ができます。</li>
							<li>よく使う機能については、画面右側にある「よく使う項目」をクリックして、お気に入りとして登録する事ができます。</li>
							<li>短くスマートなURLを実現する「スマートURL」の設定は、
						<?php $bcBaser->link('システム設定', '/admin/site_configs/form') ?>より行えます。</li>
						</ul>
					</div>
					<?php endif ?>

					<?php $bcBaser->flash() ?>

					<?php $bcBaser->element('submenu') ?>

					<?php $bcBaser->element('help') ?>

					<?php $bcBaser->element('search') ?>

					<?php $bcBaser->content() ?>



				<!-- / #ContentsBody .contents-body .clarfix --></div>

				<?php if(!empty($user)): ?>
				<div id="ToTop"><?php $bcBaser->link('▲ トップへ', '#Header') ?></div>
				<?php endif ?>

			<!-- / .cbb --></div>

		<!-- / #Contents --></div>

	<!-- / #Wrap .clearfix --></div>

	<?php $bcBaser->footer() ?>

<!-- / #Page --></div>

<?php $bcBaser->element('credit') ?>

<?php $bcBaser->func() ?>
</body>

</html>