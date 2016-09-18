<?php
/**
 * [ADMIN] レイアウト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
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
		<?php
		$this->BcBaser->css(array(
			'admin/jquery-ui/ui.all',
			'admin/import',
			'../js/admin/jquery.contextMenu-1.0/jquery.contextMenu',
			'admin/colorbox/colorbox',
			'admin/toolbar'))
		?>
		<!--[if IE]><?php $this->BcBaser->js(array('admin/excanvas')) ?><![endif]-->
		<?php
		$this->BcBaser->js(array(
			'admin/jquery-1.7.2.min',
			'admin/jquery-ui-1.8.19.custom.min',
			'admin/i18n/ui.datepicker-ja',
			'admin/jquery.corner-2.12',
			'admin/jquery.bt.min',
			'admin/cb',
			'admin/jquery.baseUrl',
			'admin/jquery.contextMenu-1.0/jquery.contextMenu',
			'admin/jquery.form-2.94',
			'admin/jquery.validate.min',
			'admin/jquery.colorbox-min-1.4.5',
			'admin/jquery.mScroll',
			'admin/jquery.baseUrl',
			'admin/credit',
			'admin/validate_messages_ja',
			'admin/functions',
			'admin/jquery.bcToken',
			'admin/jquery.bcUtil',
			'admin/startup',
			'admin/adjust_scroll',
			'admin/yuga'))
		?>
<?php $this->BcBaser->scripts() ?>
	</head>

	<body id="<?php $this->BcBaser->contentsName() ?>" class="normal">

		<div id="Page">
			<div id="BaseUrl" style="display: none"><?php echo $this->request->base ?></div>
			<div id="SaveFavoriteBoxUrl" style="display:none"><?php $this->BcBaser->url(array('action' => 'ajax_save_favorite_box')) ?></div>
			<div id="SaveSearchBoxUrl" style="display:none"><?php $this->BcBaser->url(array('action' => 'ajax_save_search_box', $this->BcBaser->getContentsName(true))) ?></div>
			<div id="FavoriteBoxOpened" style="display:none"><?php echo $favoriteBoxOpened ?></div>
			<div id="SearchBoxOpened" style="display:none"><?php echo $this->Session->read('Baser.searchBoxOpened.' . $this->BcBaser->getContentsName(true)) ?></div>
			<div id="CurrentPageName" style="display: none"><?php $this->BcBaser->contentsTitle() ?></div>
			<div id="CurrentPageUrl" style="display: none"><?php echo ($this->request->url == Configure::read('Routing.prefixes.0')) ? '/admin/dashboard/index' : '/' . $this->request->url; ?></div>

			<!-- Waiting -->
			<div id="Waiting" class="waiting-box" style="display:none">
				<div class="corner10">
			<?php echo $this->Html->image('admin/ajax-loader.gif') ?><br />
					W A I T
				</div>
			</div>

				<?php $this->BcBaser->header() ?>

			<div id="Wrap" class="clearfix" style="display:none">

<?php if (!empty($user)): ?>
			<?php $this->BcBaser->element('sidebar') ?>
<?php endif ?>

				<div id="Contents" class="clearfix">

					<div class="cbb">

								<?php $this->BcBaser->element('crumbs') ?>

						<div id="ContentsBody" class="contents-body clearfix">

							<div class="clearfix">
							<?php $this->BcBaser->element('contents_menu') ?>
								<h1><?php $this->BcBaser->contentsTitle() ?></h1>
							</div>

							<?php if ($this->request->params['controller'] != 'installations' && $this->request->params['action'] != 'update'): ?>
								<?php $this->BcBaser->updateMessage() ?>
							<?php endif ?>

							<?php if ($this->request->params['controller'] != 'installations' && !empty($this->BcBaser->siteConfig['first_access'])): ?>
								<div id="FirstMessage" class="em-box" style="text-align:left">
									baserCMSへようこそ。<br />
									<ul style="font-weight:normal;font-size:14px;"><li>画面右上の「システムナビ」より管理システムの全ての機能にアクセスする事ができます。</li>
										<li>よく使う機能については、画面右側にある「よく使う項目」をクリックして、お気に入りとして登録する事ができます。</li>
										<li>短くスマートなURLを実現する「スマートURL」の設定は、<?php $this->BcBaser->link('システム設定', '/admin/site_configs/form') ?>より行えます。</li>
									</ul>
								</div>
							<?php endif ?>

							<?php $this->BcBaser->flash() ?>

							<?php $this->BcBaser->element('submenu') ?>

							<?php $this->BcBaser->element('help') ?>

							<?php $this->BcBaser->element('search') ?>

							<?php $this->BcBaser->content() ?>



							<!-- / #ContentsBody .contents-body .clarfix --></div>

							<?php if (!empty($user)): ?>
							<div id="ToTop"><?php $this->BcBaser->link('▲ トップへ', '#Header') ?></div>
							<?php endif ?>

						<!-- / .cbb --></div>

					<!-- / #Contents --></div>

				<!-- / #Wrap .clearfix --></div>

<?php $this->BcBaser->footer() ?>

			<!-- / #Page --></div>

<?php $this->BcBaser->func() ?>
	</body>

</html>
