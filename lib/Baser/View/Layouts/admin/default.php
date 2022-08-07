<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
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
		$this->BcBaser->css([
			'admin/jquery-ui/jquery-ui.min',
			'../js/admin/vendors/jquery.jstree-3.3.8/themes/proton/style.min',
			'../js/admin/vendors/jquery-contextMenu-2.2.0/jquery.contextMenu.min',
			'admin/import',
			'admin/colorbox/colorbox-1.6.1',
			'admin/toolbar',
			'admin/jquery.timepicker'])
		?>
		<?php if($favoriteBoxOpened): ?>
			<?php $this->BcBaser->css('admin/sidebar_opened') ?>
		<?php endif ?>
		<!--[if IE]><?php $this->BcBaser->js(['admin/vendors/excanvas']) ?><![endif]-->
		<?php
        echo $this->BcBaser->declarationI18n();
        echo $this->BcBaser->i18nScript([
			'commonCancel'                  => __d('baser', 'キャンセル'),
			'commonSave'                    => __d('baser', '保存'),
			'commonExecCompletedMessage'    => __d('baser', '処理が完了しました。'),
			'commonSaveFailedMessage'       => __d('baser', '保存に失敗しました。'),
			'commonExecFailedMessage'       => __d('baser', '処理に失敗しました。'),
			'commonBatchExecFailedMessage'  => __d('baser', '一括処理に失敗しました。'),
			'commonGetDataFailedMessage'    => __d('baser', 'データ取得に失敗しました。'),
			'commonSortSaveFailedMessage'   => __d('baser', '並び替えの保存に失敗しました。'),
			'commonSortSaveConfirmMessage'	=> __d('baser', 'コンテンツを移動します。よろしいですか？'),
			'commonNotFoundProgramMessage'  => __d('baser', '送信先のプログラムが見つかりません。'),
			'commonSelectDataFailedMessage' => __d('baser', 'データが選択されていません。'),
			'commonConfirmDeleteMessage'    => __d('baser', '本当に削除してもよろしいですか？'),
			'commonConfirmHardDeleteMessage'=> __d('baser', "このデータを本当に削除してもよろしいですか？\n※ 削除したデータは元に戻すことができません。"),
			'commonPublishFailedMessage'    => __d('baser', '公開処理に失敗しました。'),
			'commonChangePublishFailedMessage'=> __d('baser', '公開状態の変更に失敗しました。'),
			'commonUnpublishFailedMessage'  => __d('baser', '非公開処理に失敗しました。'),
			'commonCopyFailedMessage'       => __d('baser', 'コピーに失敗しました。'),
			'commonDeleteFailedMessage'     => __d('baser', '削除に失敗しました。'),
			'batchListConfirmDeleteMessage' => __d('baser', "選択したデータを全て削除します。よろしいですか？\n※ 削除したデータは元に戻すことができません。"),
			'batchListConfirmPublishMessage'=> __d('baser', '選択したデータを全て公開状態に変更します。よろしいですか？'),
			'batchListConfirmUnpublishMessage'=> __d('baser', '選択したデータを全て非公開状態に変更します。よろしいですか？'),
			'bcConfirmTitle1'               => __d('baser', 'ダイアログ'),
            'bcConfirmAlertMessage1'        => __d('baser', 'メッセージを指定してください。'),
            'bcConfirmAlertMessage2'        => __d('baser', 'コールバック処理が登録されていません。'),
            'favoriteTitle1'                => __d('baser', 'よく使う項目登録'),
			'favoriteTitle2'                => __d('baser', 'よく使う項目編集'),
            'favoriteAlertMessage1'         => __d('baser', '並び替えの保存に失敗しました。'),
			'favoriteAlertMessage2'         => __d('baser', 'よく使う項目の追加に失敗しました。'),
        ], ['inline' => true]);
		$this->BcBaser->js([
			'admin/vendors/jquery-2.1.4.min',
			'admin/vendors/jquery-ui-1.11.4.min',
			'admin/vendors/i18n/ui.datepicker-ja',
			'admin/vendors/jquery.bt.min',
			'admin/vendors/jquery-contextMenu-2.2.0/jquery.contextMenu.min',
			'admin/vendors/jquery.form-2.94',
			'admin/vendors/jquery.validate.min',
			'admin/vendors/jquery.colorbox-1.6.1.min',
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
			'admin/vendors/yuga',
			'admin/vendors/jquery.timepicker'])
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
			<div id="SaveFavoriteBoxUrl" style="display:none"><?php $this->BcBaser->url(['plugin' => '', 'controller' => 'dashboard', 'action' => 'ajax_save_favorite_box']) ?></div>
			<div id="SaveSearchBoxUrl" style="display:none"><?php $this->BcBaser->url(['plugin' => '', 'controller' => 'dashboard', 'action' => 'ajax_save_search_box', $this->BcBaser->getContentsName(true)]) ?></div>
			<div id="SearchBoxOpened" style="display:none"><?php echo $this->Session->read('Baser.searchBoxOpened.' . $this->BcBaser->getContentsName(true)) ?></div>
			<div id="CurrentPageName" style="display: none"><?php echo h($this->BcBaser->getContentsTitle()) ?></div>
			<div id="CurrentPageUrl" style="display: none"><?php echo ($this->request->url == Configure::read('Routing.prefixes.0')) ? '/' . BcUtil::getAdminPrefix() . '/dashboard/index' : '/' . h($this->request->url); ?></div>

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
									<?php echo __d('baser', 'baserCMSへようこそ。')?><br />
									<ul style="font-weight:normal;font-size:14px;">
										<li><?php echo __d('baser', '画面右上の「システムナビ」より管理システムの全ての機能にアクセスする事ができます。')?></li>
										<li><?php echo __d('baser', 'よく使う機能については、画面左側にある「よく使う項目」の「新規追加」をクリックして、お気に入りとして登録する事ができます。')?></li>
										<li><?php echo __d('baser', 'まずは、画面上部のメニュー、「コンテンツ管理」よりWebサイトの全体像を確認しましょう。')?></li>
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
