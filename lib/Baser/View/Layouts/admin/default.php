<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] レイアウト
 * @var BcAppView $this
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
			'admin/jquery-ui/jquery-ui.min',
			'../js/admin/vendors/jquery.jstree-3.3.1/themes/proton/style.min',
			'../js/admin/vendors/jquery-contextMenu-2.2.0/jquery.contextMenu.min',
			'admin/import',
			'admin/colorbox/colorbox-1.6.1',
			'admin/toolbar'))
		?>
		<?php if($favoriteBoxOpened): ?>
			<?php $this->BcBaser->css('admin/sidebar_opened') ?>
		<?php endif ?>
		<!--[if IE]><?php $this->BcBaser->js(array('admin/vendors/excanvas')) ?><![endif]-->
		<?php
		$this->BcBaser->js(array(
			'admin/vendors/jquery-2.1.4.min',
			'admin/vendors/jquery-ui-1.11.4.min',
			'admin/vendors/i18n/ui.datepicker-ja',
			'admin/vendors/jquery.bt.min',
			'admin/vendors/jquery-contextMenu-2.2.0/jquery.contextMenu.min',
			'admin/vendors/jquery.form-2.94',
			'admin/vendors/jquery.validate.min',
			'admin/vendors/jquery.colorbox-1.6.1.min',
			'admin/libs/jquery.mScroll',
			'admin/libs/jquery.baseUrl',
			'admin/libs/jquery.bcConfirm',
			'admin/libs/credit',
			'admin/vendors/validate_messages_ja',
			'admin/functions',
			'admin/libs/adjust_scroll',
			'admin/libs/jquery.bcUtil',
			'admin/libs/jquery.bcToken',
			'admin/startup',
			'admin/favorite',
			'admin/permission',
			'admin/vendors/yuga'))
		?>
	<script>
		$.bcUtil.init({
			baseUrl: '<?php echo $this->request->base ?>',
			adminPrefix: '<?php echo BcUtil::getAdminPrefix() ?>'
		});
	</script>
<?php $this->BcBaser->scripts() ?>
	</head>

	<body id="<?php $this->BcBaser->contentsName() ?>" class="normal">

		<div id="Page">
			<div id="BaseUrl" style="display: none"><?php echo $this->request->base ?></div>
			<div id="SaveFavoriteBoxUrl" style="display:none"><?php $this->BcBaser->url(array('plugin' => '', 'controller' => 'dashboard', 'action' => 'ajax_save_favorite_box')) ?></div>
			<div id="SaveSearchBoxUrl" style="display:none"><?php $this->BcBaser->url(array('plugin' => '', 'controller' => 'dashboard', 'action' => 'ajax_save_search_box', $this->BcBaser->getContentsName(true))) ?></div>
			<div id="SearchBoxOpened" style="display:none"><?php echo $this->Session->read('Baser.searchBoxOpened.' . $this->BcBaser->getContentsName(true)) ?></div>
			<div id="CurrentPageName" style="display: none"><?php echo h($this->BcBaser->getContentsTitle()) ?></div>
			<div id="CurrentPageUrl" style="display: none"><?php echo ($this->request->url == Configure::read('Routing.prefixes.0')) ? '/' . BcUtil::getAdminPrefix() . '/dashboard/index' : '/' . $this->request->url; ?></div>

			<!-- Waiting -->
			<div id="Waiting" class="waiting-box" style="display:none">
				<div class="corner10">
			<?php echo $this->Html->image('admin/ajax-loader.gif') ?><br />
					W A I T
				</div>
			</div>

				<?php $this->BcBaser->header() ?>

			<div id="Wrap" class="clearfix">

<?php if ($this->name != 'Installations' && $this->name != 'Updaters' && ('/' . $this->request->url != Configure::read('BcAuthPrefix.admin.loginAction')) && !empty($user)): ?>
			<?php $this->BcBaser->element('sidebar') ?>
<?php endif ?>

				<div id="Contents" class="clearfix">

					<div class="cbb">

								<?php $this->BcBaser->element('crumbs') ?>

						<div id="ContentsBody" class="contents-body clearfix">

							<div class="clearfix">
							<?php $this->BcBaser->element('contents_menu') ?>
								<h1><?php echo h($this->BcBaser->getContentsTitle()) ?></h1>
							</div>

							<?php if ($this->request->params['controller'] != 'installations' && !empty($this->BcBaser->siteConfig['first_access'])): ?>
								<div id="FirstMessage" class="em-box" style="text-align:left">
									baserCMSへようこそ。<br />
									<ul style="font-weight:normal;font-size:14px;">
										<li>画面右上の「システムナビ」より管理システムの全ての機能にアクセスする事ができます。</li>
										<li>よく使う機能については、画面左側にある「よく使う項目」の「新規追加」をクリックして、お気に入りとして登録する事ができます。</li>
										<li>まずは、画面上部のメニュー、「コンテンツ管理」よりWebサイトの全体像を確認しましょう。</li>
									</ul>
								</div>
							<?php endif ?>

							<?php $this->BcBaser->flash() ?>

							<div id="BcMessageBox"><div id="BcSystemMessage" class="notice-message">&nbsp;</div></div>

							<?php $this->BcBaser->element('submenu') ?>

							<?php if(@$help): ?>
							<?php $this->BcBaser->element('help', [], ['cache' => ['key' => '_admin_help_' . $help]]) ?>
							<?php endif ?>
							
							<?php $this->BcBaser->element('search') ?>
                            
                            <?php echo $this->BcLayout->dispatchContentsHeader() ?>
                            
							<?php $this->BcBaser->content() ?>

							<?php echo $this->BcLayout->dispatchContentsFooter() ?>

							<!-- / #ContentsBody .contents-body .clarfix --></div>

							<?php if (!empty($user)): ?>
							<div id="ToTop"><?php $this->BcBaser->link('▲ トップへ', '#Header') ?></div>
							<?php endif ?>

						<!-- / .cbb --></div>

					<!-- / #Contents --></div>

				<!-- / #Wrap .clearfix --></div>

<?php $bcUtilLoginUser = BcUtil::loginUser(); ?>
<?php if (!empty($bcUtilLoginUser)): ?>
	<?php $this->BcBaser->footer([], ['cache' => ['key' => '_admin_footer']]) ?>
<?php else: ?>
	<?php $this->BcBaser->footer() ?>
<?php endif ?>

			<!-- / #Page --></div>

<?php $this->BcBaser->func() ?>
	</body>

</html>
