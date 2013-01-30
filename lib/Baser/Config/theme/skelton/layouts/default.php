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
<?php $this->bcBaser->css('style') ?>
<?php $this->bcBaser->js(array(
	'jquery-1.7.2.min',
	'functions',
	'startup')) ?>
<?php $this->bcBaser->scripts() ?>
<?php $this->bcBaser->element('google_analytics') ?>
</head>
<body id="<?php $this->bcBaser->contentsName() ?>">

<div id="Page">

	<?php $this->bcBaser->header() ?>
	
	<div id="Wrap" class="clearfix">
	
		<div id="Alfa" >
			<div id="ContentsBody" class="clearfix">
				<?php $this->bcBaser->flash() ?>
				<?php $this->bcBaser->content() ?>
				<?php $this->bcBaser->element('contents_navi') ?>
			</div>
		</div>

		<div id="Beta">
			<?php $this->bcBaser->widgetArea() ?>
		</div>
		
	</div>

	<div class="to-top"> <a href="#Page">このページの先頭へ戻る</a> </div>
	
	<?php $this->bcBaser->footer() ?>
	
</div>

<?php $this->bcBaser->func() ?>
</body>
</html>