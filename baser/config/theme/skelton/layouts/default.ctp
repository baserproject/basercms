<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $baser->xmlHeader() ?>
<?php $baser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<?php $baser->charset() ?>
<?php $baser->title() ?>
<?php $baser->metaDescription() ?>
<?php $baser->metaKeywords() ?>
<?php $baser->icon() ?>
<?php $baser->css(array('style','colorbox/colorbox')) ?>
<?php $baser->js(array(
	'jquery-1.4.2.min',
	'jquery.bt.min',
	'jquery.colorbox-min',
	'functions',
	'startup')) ?>
<?php $baser->scripts() ?>
<?php $baser->element('google_analytics') ?>
</head>
<body id="<?php $baser->contentsName() ?>">

<div id="Page">

	<?php $baser->element('header') ?>
	
	<div id="Wrap" class="clearfix">
	
		<div id="Alfa" >
			<div id="ContentsBody" class="clearfix">
				<?php $baser->flash() ?>
				<?php $baser->content() ?>
			</div>
		</div>

		<div id="Beta">
			<?php $baser->element('widget_area',array('no'=>$widgetArea)) ?>
		</div>
		
	</div>

	<div class="to-top"> <a href="#Page">このページの先頭へ戻る</a> </div>
	
	<?php $baser->element('footer') ?>
	
</div>

<?php echo $cakeDebug; ?>
</body>
</html>