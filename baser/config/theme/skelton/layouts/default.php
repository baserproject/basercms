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
<?php $bcBaser->css('style') ?>
<?php $bcBaser->js(array(
	'jquery-1.7.2.min',
	'functions',
	'startup')) ?>
<?php $bcBaser->scripts() ?>
<?php $bcBaser->element('google_analytics') ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>">

<div id="Page">

	<?php $bcBaser->header() ?>
	
	<div id="Wrap" class="clearfix">
	
		<div id="Alfa" >
			<div id="ContentsBody" class="clearfix">
				<?php $bcBaser->flash() ?>
				<?php $bcBaser->content() ?>
				<?php $bcBaser->element('contents_navi') ?>
			</div>
		</div>

		<div id="Beta">
			<?php $bcBaser->widgetArea() ?>
		</div>
		
	</div>

	<div class="to-top"> <a href="#Page">このページの先頭へ戻る</a> </div>
	
	<?php $bcBaser->footer() ?>
	
</div>

<?php $bcBaser->func() ?>
</body>
</html>