<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $baser->docType('html5') ?>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=320, user-scalable=no">
<?php $baser->title() ?>
<?php $baser->metaDescription() ?>
<?php $baser->metaKeywords() ?>
<?php $baser->css('smartphone/import') ?>
<?php $baser->js(array(
	'jquery-1.6.4.min',
	'smartphone/startup.js'
)) ?>
<?php $baser->scripts() ?>
<?php $baser->element('google_analytics', null, false, false) ?>
</head>
<body id="<?php $baser->contentsName() ?>">
<div id="Page">

	<header>
		<div class="clearfix" id="BoxLogo">
			<div id="Logo"><?php echo $baser->siteConfig['name'] ?></div>
		</div>
		<?php if($baser->isTop()): ?>
		<?php $baser->img('smartphone/img_main.png', array('alt' => $baser->siteConfig['name'])) ?>
		<?php endif ?>
		<?php $baser->element('global_menu') ?>
	</header>
	
	<div id="ContentsBody" class="contents-body clearfix">
		<?php $baser->flash() ?>
		<?php $baser->content() ?>
		<?php $baser->element('contents_navi') ?>
	</div>

	<div>
		<?php if(!empty($widgetArea)): ?>
		<?php $baser->element('widget_area',array('no'=>$widgetArea)) ?>
		<?php endif ?>
	</div>
	
	<section id="ToTop">
	<a href="#Page">PAGE TOP</a>
	</section>
	
	<footer>
		<?php $baser->element('global_menu') ?>
		<address>Copyright(C) 2008 - 2011 <br />baserCMS All rights Reserved.</address>
		<div class="banner">
		<?php $baser->link($baser->getImg('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")),'http://basercms.net') ?>
		<?php $baser->link($baser->getImg('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")),'http://cakephp.jp') ?>
		</div>
	</footer>
	
</div>
<?php echo $cakeDebug; ?>
</body>
</html>