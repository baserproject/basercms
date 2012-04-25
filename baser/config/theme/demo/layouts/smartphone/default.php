<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $bcBaser->docType('html5') ?>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=320, user-scalable=no">
<?php $bcBaser->title() ?>
<?php $bcBaser->metaDescription() ?>
<?php $bcBaser->metaKeywords() ?>
<?php $bcBaser->css('smartphone/import') ?>
<?php $bcBaser->js(array(
	'jquery-1.6.4.min',
	'smartphone/startup.js'
)) ?>
<?php $bcBaser->scripts() ?>
<?php $bcBaser->element('google_analytics', null, false, false) ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>">
<div id="Page">

	<header>
		<div class="clearfix" id="BoxLogo">
			<div id="Logo"><?php echo $bcBaser->siteConfig['name'] ?></div>
		</div>
		<?php if($bcBaser->isTop()): ?>
		<?php $bcBaser->img('smartphone/img_main.png', array('alt' => $bcBaser->siteConfig['name'])) ?>
		<?php endif ?>
		<?php $bcBaser->element('global_menu') ?>
	</header>
	
	<div id="ContentsBody" class="contents-body clearfix">
		<?php $bcBaser->flash() ?>
		<?php $bcBaser->content() ?>
		<?php $bcBaser->element('contents_navi') ?>
	</div>

	<div>
		<?php if(!empty($widgetArea)): ?>
		<?php $bcBaser->element('widget_area',array('no'=>$widgetArea)) ?>
		<?php endif ?>
	</div>
	
	<section id="ToTop">
	<a href="#Page">PAGE TOP</a>
	</section>
	
	<footer>
		<?php $bcBaser->element('global_menu') ?>
		<address>Copyright(C) 2008 - 2011 <br />baserCMS All rights Reserved.</address>
		<div class="banner">
		<?php $bcBaser->link($bcBaser->getImg('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")),'http://basercms.net') ?>
		<?php $bcBaser->link($bcBaser->getImg('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")),'http://cakephp.jp') ?>
		</div>
	</footer>
	
</div>
<?php $bcBaser->func() ?>
</body>
</html>