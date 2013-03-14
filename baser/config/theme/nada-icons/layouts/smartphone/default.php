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
<?php $bcBaser->css(array('html5reset-1.6.1', 'smartphone/style')) ?>
<?php $bcBaser->js(array(
    'jquery-1.7.2.min',
	'smartphone/startup.js',
    'functions',
    'startup',
    'jquery.bxSlider.min',
    'jquery.easing.1.3',
    'nada-icons'
)) ?>
<?php $bcBaser->scripts() ?>
<?php $bcBaser->element('google_analytics', null, false, false) ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>">
<div id="Page">

	<header>
		<div class="clearfix" id="BoxLogo">
			<div id="Logo"><h1><?php $bcBaser->link($bcBaser->siteConfig['name'],'/') ?></h1></div>
		</div>
		<div class="clearfix" id="global_menu">
    		<?php $bcBaser->element('global_menu') ?>
        </div>
	</header>
	
    <?php if($bcBaser->isTop()): ?>
    <div id="top-main">
        <div id="slider">
          <div><?php $bcBaser->img('slider/01.jpg'); ?></div>
          <div><?php $bcBaser->img('slider/02.jpg'); ?></div>
          <div><?php $bcBaser->img('slider/03.jpg'); ?></div>
          <div><?php $bcBaser->img('slider/04.jpg'); ?></div>
        </div>
    </div>
    <?php 
    /*
    *スライダーは色々設定ができるので参考にして下さい  http://zxcvbnmnbvcxz.com/demonstration/bxslide.html 
    *設定ファイルは js/nada-icons です
    */
    ?>
    <?php endif ?>

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
		<div class="clearfix" id="global_menu">
    		<?php $bcBaser->element('global_menu') ?>
        </div>

		<address>Copyright(C) 2008 - <?php echo date('Y')?> <br />baserCMS All rights Reserved.</address>
		<div class="banner">
		<?php $bcBaser->link($bcBaser->getImg('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")),'http://basercms.net') ?>
		<?php $bcBaser->link($bcBaser->getImg('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")),'http://cakephp.jp') ?>
		</div>
	</footer>
	
</div>
<?php $bcBaser->func() ?>
</body>
</html>