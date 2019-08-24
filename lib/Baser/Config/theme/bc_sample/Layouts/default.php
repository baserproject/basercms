<?php
/**
 * レイアウト
 * 呼出箇所：全ページ
 */
?>
<?php $this->BcBaser->docType('html5') ?>
<html>
<head>
	<?php $this->BcBaser->charset() ?>
	<?php $this->BcBaser->title() ?>
	<?php $this->BcBaser->metaDescription() ?>
	<?php $this->BcBaser->metaKeywords() ?>
	<?php $this->BcBaser->icon() ?>
	<?php $this->BcBaser->css(array(
		'style',
		'jquery-ui/jquery-ui-1.11.4',
		'colorbox/colorbox-1.6.1',
	)) ?>
	<?php $this->BcBaser->js(array(
		'jquery-1.11.3.min',
		'jquery-ui-1.11.4.min',
		'jquery.bxslider-4.12.min',
		'jquery.colorbox-1.6.1.min',
		'i18n/ui.datepicker-ja',
		'jquery-accessibleMegaMenu',
		'startup'
	)); ?>
	<?php $this->BcBaser->scripts() ?>
	<!-- /Elements/google_analytics.php -->
	<?php $this->BcBaser->googleAnalytics() ?>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>">

<div id="Page">

	<!-- /Elements/header.php -->
	<?php $this->BcBaser->header() ?>

	<!-- /Elements/global_menu.php -->
	<nav><?php $this->BcBaser->globalMenu(2) ?></nav>

	<?php if ($this->BcBaser->isHome()): ?>
		<?php $this->BcBaser->mainImage(array('all' => true, 'num' => 5, 'width' => "100%")) ?>
	<?php else: ?>
		<!-- /Elements/crumbs.php -->
		<?php $this->BcBaser->crumbsList(); ?>
	<?php endif ?>

	<div id="Wrap" class="clearfix">

		<section id="ContentsBody" class="contents-body">
			<?php $this->BcBaser->flash() ?>
			<?php $this->BcBaser->content() ?>
			<!-- /Elements/contents_navi.php -->
			<?php $this->BcBaser->contentsNavi() ?>
		</section>

		<div id="SideBox">
			<!-- /Elements/widget_area.php -->
			<?php $this->BcBaser->widgetArea() ?>
		</div>

	</div>

	<!-- /Elements/footer.php -->
	<?php $this->BcBaser->footer() ?>

</div>

<?php $this->BcBaser->func() ?>
</body>
</html>
