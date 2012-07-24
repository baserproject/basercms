<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $this->bcBaser->xmlHeader() ?>
<?php $this->bcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<?php $this->bcBaser->charset() ?>
<?php $this->bcBaser->title() ?>
<?php $this->bcBaser->metaDescription() ?>
<?php $this->bcBaser->metaKeywords() ?>
<?php $this->bcBaser->icon() ?>
<?php $this->bcBaser->rss('ニュースリリース RSS 2.0', '/news/index.rss') ?>
<?php $this->bcBaser->css(array('import')) ?>
<?php if($this->bcBaser->isHome()): ?>
<?php $this->bcBaser->css(array('top')) ?>
<?php endif ?>
<?php $this->bcBaser->js(array(
	'jquery-1.7.2.min',
	'startup'
)) ?>
<?php $this->bcBaser->scripts() ?>
<?php $this->bcBaser->element('google_analytics') ?>
</head>
<body id="<?php $this->bcBaser->contentsName() ?>">

<!-- begin page -->
<div id="page">

	<!-- begin header -->
	<?php $this->bcBaser->header() ?>
	<!-- end header -->
	
	<!-- begin contents -->
	<div id="contents" class="clearfix">
	
		<!-- begin alfa -->
		<div id="alfa" >
		
			<!-- begin contentsBody -->
			<div id="contentsBody" class="clearfix">
				<?php $this->bcBaser->flash() ?>
				<?php $this->bcBaser->content() ?>
				<?php $this->bcBaser->element('contents_navi') ?>
			</div>
			<!-- end contentsBody -->
			
		</div>
		<!-- end alfa -->
		
		<?php if(!$this->bcBaser->isHome()): ?>
		<!-- begin beta -->
		<?php $this->bcBaser->element('sidebar') ?>
		<!-- end beta -->
		<?php endif ?>
		
		<?php if(!$this->bcBaser->isHome()): ?>
		<div class="to-top"> <a href="#page">このページの先頭へ戻る</a> </div>
		<?php endif ?>
		
	</div>
	<!-- end contents -->
	
</div>
<!-- end page -->

<!-- begin footer -->
<?php $this->bcBaser->footer() ?>
<!-- end footer -->

<?php $this->bcBaser->func() ?>
</body>
</html>