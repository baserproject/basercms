<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $this->BcBaser->xmlHeader() ?>
<?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
	<head>
		<?php $this->BcBaser->charset() ?>
		<?php $this->BcBaser->title() ?>
		<?php $this->BcBaser->metaDescription() ?>
		<?php $this->BcBaser->metaKeywords() ?>
		<?php $this->BcBaser->icon() ?>
		<?php $this->BcBaser->rss('ニュースリリース RSS 2.0', '/news/index.rss') ?>
		<?php $this->BcBaser->css('style') ?>

<?php $this->BcBaser->js(array(
			'jquery-1.7.2.min',
			'jquery.bxSlider.min',
			'jquery.easing.1.3',
			'nada-icons'
)) ?>
<?php $this->BcBaser->scripts() ?>
<?php $this->BcBaser->googleAnalytics() ?>
	</head>
	<body id="<?php $this->BcBaser->contentsName() ?>">


<?php $this->BcBaser->header() ?>

		<div id="Page">
			<div id="Wrap" class="clearfix">


					<?php $this->BcBaser->element('sidebox') ?>

				<div id="Beta">
					<?php if (!$this->BcBaser->isHome()): ?>
					<div id="Navigation">
						<?php $this->BcBaser->crumbsList(); ?>
					</div>
					<?php endif; ?>

					<?php if ($this->BcBaser->isHome()): ?>
					<div id="top-main">
						<?php $this->BcBaser->mainImage(array('all' => true, 'num' => 5, 'width' => 750)) ?>
					</div>
					<?php
					/*
					 * スライダーは色々設定ができるので参考にして下さい  http://zxcvbnmnbvcxz.com/demonstration/bxslide.html
					 * 設定ファイルは js/nada-icons です
					 */
					?>
					<?php endif ?>

					<div id="ContentsBody" class="clearfix">
					<?php if ($this->BcBaser->isHome()): ?>
						<?php $this->BcBaser->element('toppage') ?>
					<?php else: ?>
						<div class="subpage">
							<?php $this->BcBaser->flash() ?>
							<?php $this->BcBaser->content() ?>
							<div class="to-top"> <a href="#Page"><?php $this->BcBaser->img('icons_up.png'); ?><?php echo __('ページトップへ戻る') ?></a></div>
						</div>
					<?php endif ?>

						<div id="top-contents-main">
							<div id="top-main-telfax-title">お気軽にお問い合わせ下さい</div>
							<div id="top-main-telfax-left">
								<div id="top-main-telfax-tel">
									<p class="top-tel">TEL 092-000-5555</p>
									<p class="top-tel-time">受付時間：平日<br>9:30〜18:30</p>
								</div>
								<div id="top-main-telfax-fax">
									<p class="top-fax">FAX 092-000-5555</p>
									<p class="top-fax-time">受付時間<br>24時間受付</p>
								</div>
							</div>
							<div id="top-main-telfax-right">
								<div id="top-main-webcontact"><?php $this->BcBaser->img('icons_contact.png', array('url' => '/contact')); ?></div>
								<div id="top-main-serch"><?php $this->BcBaser->siteSearchForm() ?></div>
							</div>
						</div>

					</div>
				</div><!--Bata-->

			</div><!--Wrap-->



		</div><!--Page-->
<?php $this->BcBaser->footer() ?>
<?php $this->BcBaser->func() ?>
	</body>
</html>
