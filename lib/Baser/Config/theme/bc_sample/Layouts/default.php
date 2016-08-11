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
	<?php $this->BcBaser->css(array(
		'style',
		'jquery-ui/jquery-ui-1.11.4',
		'colorbox/colorbox-1.6.1',
//			'megamenu'
	)) ?>
	<?php $this->BcBaser->js(array(
		'jquery-1.11.3.min',
		'jquery-ui-1.11.4.min',
		'jquery.bxslider-4.12.min',
		'jquery.colorbox-1.6.1.min',
		'i18n/ui.datepicker-ja',
		'jquery-accessibleMegaMenu.js'
	)); ?>
	<script>
		$(function(){
			$("#MainImage").show();
			$("#MainImage").bxSlider({mode:"fade", auto:true});
			$("a[rel='colorbox']").colorbox({transition:"fade"});
		})
	</script>
	<?php $this->BcBaser->scripts() ?>
	<!-- /Elements/google_analytics.php -->
	<?php $this->BcBaser->googleAnalytics() ?>
	<script>
		$(function(){

			$("nav:first").accessibleMegaMenu({
				/* prefix for generated unique id attributes, which are required 
				 to indicate aria-owns, aria-controls and aria-labelledby */
				uuidPrefix: "accessible-megamenu",

				/* css class used to define the megamenu styling */
				menuClass: "nav-menu",

				/* css class for a top-level navigation item in the megamenu */
				topNavItemClass: "nav-item",

				/* css class for a megamenu panel */
				panelClass: "sub-nav",

				/* css class for a group of items within a megamenu panel */
				panelGroupClass: "sub-nav-group",

				/* css class for the hover state */
				hoverClass: "hover",

				/* css class for the focus state */
				focusClass: "focus",

				/* css class for the open state */
				openClass: "open"
			});
		});
	</script>
	<style>
		/* Rudimentary mega menu CSS for demonstration */

		/* mega menu list */
		.nav-menu {
			display: block;
			position: relative;
			list-style: none;
			margin: 0;
			padding: 0;
			z-index: 15;
		}

		/* a top level navigation item in the mega menu */
		.nav-item {
			list-style: none;
			display: inline-block;
			padding: 0;
			margin: 0;
		}

		/* first descendant link within a top level navigation item */
		.nav-item > a {
			position: relative;
			display: inline-block;
			padding: 0.5em 1em;
			margin: 0 0 -1px 0;
			border: 1px solid transparent;
		}

		/* focus/open states of first descendant link within a top level 
		   navigation item */
		.nav-item > a:focus,
		.nav-item > a.open {
			border: 1px solid #dedede;
		}

		/* open state of first descendant link within a top level 
		   navigation item */
		.nav-item > a.open {
			background-color: #fff;
			border-bottom: none;
			z-index: 2;
		}

		/* sub-navigation panel */
		.sub-nav {
			position: absolute;
			display: none;
			/*top: 2.6em;*/
			/*margin-top: -1px;*/
			padding: 0.5em 1em;
			border: 1px solid #dedede;
			background-color: #fff;
			z-index:1;
		}
		#MainImage {
			z-index:0;
		}
		/* sub-navigation panel open state */
		.sub-nav.open {
			display: block;
		}

		/* list of items within sub-navigation panel */
		.sub-nav ul {
			display: inline-block;
			vertical-align: top;
			margin: 0 1em 0 0;
			padding: 0;
		}

		/* list item within sub-navigation panel */
		.sub-nav li {
			display: block;
			list-style-type: none;
			margin: 0;
			padding: 0;
		}
	</style>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>">

<div id="Page">

	<!-- /Elements/header.php -->
	<?php $this->BcBaser->header() ?>

	<!-- /Elements/global_menu.php -->
	<nav><?php $this->BcBaser->contentsMenu(0,2) ?></nav>

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
