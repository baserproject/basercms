<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * レイアウト
 * 呼出箇所：全ページ
 * @var BcAppView $this
 */
?>
<?php $this->BcBaser->docType('html5') ?>
<html>
<head>
	<?php $this->BcBaser->charset() ?>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
	<?php $this->BcBaser->title() ?>
	<?php $this->BcBaser->metaDescription() ?>
	<?php $this->BcBaser->metaKeywords() ?>
	<?php $this->BcBaser->icon() ?>
	<?php $this->BcBaser->css([
		'style',
		'jquery-ui/jquery-ui-1.11.4',
		'colorbox/colorbox-1.6.1',
		'editor'
	]) ?>
	<?php $this->BcBaser->js([
		'jquery-1.11.3.min',
		'jquery-ui-1.11.4.min',
		'jquery.bxslider-4.12.min',
		'jquery.colorbox-1.6.1.min',
		'i18n/ui.datepicker-ja',
		'jquery-accessibleMegaMenu',
		'startup'
	]); ?>
	<?php $this->BcBaser->scripts() ?>
	<!-- /Elements/google_analytics.php -->
	<?php $this->BcBaser->googleAnalytics() ?>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>">

<div class="bs-container">

	<!-- /Elements/header.php -->
	<?php $this->BcBaser->header() ?>

	<?php if ($this->BcBaser->isHome()): ?>
		<?php $this->BcBaser->mainImage(['all' => true, 'num' => 5, 'width' => '100%', 'class' => 'bs-main-image']) ?>
	<?php else: ?>
		<!-- /Elements/crumbs.php -->
		<?php $this->BcBaser->crumbsList(['onSchema' => true]); ?>
	<?php endif ?>

	<div class="bs-wrap clearfix">

		<main class="bs-main-contents">
			<?php $this->BcBaser->flash() ?>

			<?php if($this->BcBaser->isHome()): ?>
				<!-- /Elements/top_info.php -->
				<?php $this->BcBaser->element('top_info') ?>
			<?php endif ?>

			<?php $this->BcBaser->content() ?>

			<!-- /Elements/contents_navi.php -->
			<?php $this->BcBaser->contentsNavi() ?>
		</main>

		<section class="bs-sub-contents">
			<!-- /Elements/widget_area.php -->
			<?php $this->BcBaser->widgetArea() ?>
		</section>

	</div>

	<!-- /Elements/footer.php -->
	<?php $this->BcBaser->footer() ?>

</div>

<?php $this->BcBaser->func() ?>
</body>
</html>
