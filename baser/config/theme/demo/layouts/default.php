<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $bcBaser->xmlHeader() ?>
<?php $bcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<?php $bcBaser->charset() ?>
<?php $bcBaser->title() ?>
<?php $bcBaser->metaDescription() ?>
<?php $bcBaser->metaKeywords() ?>
<?php $bcBaser->icon() ?>
<?php $bcBaser->rss('ニュースリリース RSS 2.0', '/news/index.rss') ?>
<?php $bcBaser->css(array('import')) ?>
<?php if($bcBaser->isHome()): ?>
<?php $bcBaser->css(array('top')) ?>
<?php endif ?>
<?php $bcBaser->js(array(
	'jquery-1.7.2.min',
	'startup'
)) ?>
<?php $bcBaser->scripts() ?>
<?php $bcBaser->element('google_analytics') ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>">

<!-- begin page -->
<div id="page">

	<!-- begin header -->
	<?php $bcBaser->header() ?>
	<!-- end header -->
	
	<!-- begin contents -->
	<div id="contents" class="clearfix">
	
		<!-- begin alfa -->
		<div id="alfa" >
		
			<!-- begin contentsBody -->
			<div id="contentsBody" class="clearfix">
				<?php $bcBaser->flash() ?>
				<?php $bcBaser->content() ?>
				<?php $bcBaser->element('contents_navi') ?>
			</div>
			<!-- end contentsBody -->
			
		</div>
		<!-- end alfa -->
		
		<?php if(!$bcBaser->isHome()): ?>
		<!-- begin beta -->
		<?php $bcBaser->element('sidebar') ?>
		<!-- end beta -->
		<?php endif ?>
		
		<?php if(!$bcBaser->isHome()): ?>
		<div class="to-top"> <a href="#page">このページの先頭へ戻る</a> </div>
		<?php endif ?>
		
	</div>
	<!-- end contents -->
	
</div>
<!-- end page -->

<!-- begin footer -->
<?php $bcBaser->footer() ?>
<!-- end footer -->

<?php $bcBaser->func() ?>
</body>
</html>