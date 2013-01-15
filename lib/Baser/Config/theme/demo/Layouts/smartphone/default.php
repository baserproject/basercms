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
<?php $this->bcBaser->title() ?>
<?php $this->bcBaser->metaDescription() ?>
<?php $this->bcBaser->metaKeywords() ?>
<?php $this->bcBaser->css('smartphone/import') ?>
<?php $this->bcBaser->js(array(
	'jquery-1.6.4.min',
	'smartphone/startup.js'
)) ?>
<?php $bcBaser->scripts() ?>
<?php $this->bcBaser->element('google_analytics', null, false, false) ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>">
<div id="Page">

	<header>
		<div class="clearfix" id="BoxLogo">
			<div id="Logo"><?php echo $bcBaser->siteConfig['name'] ?></div>
		</div>
		<?php if($bcBaser->isTop()): ?>
		<?php $this->bcBaser->img('smartphone/img_main.png', array('alt' => $this->bcBaser->siteConfig['name'])) ?>
		<?php endif ?>
		<?php $this->bcBaser->element('global_menu') ?>
	</header>
	
	<div id="ContentsBody" class="contents-body clearfix">
		<?php $this->bcBaser->flash() ?>
		<?php $this->bcBaser->content() ?>
		<?php $this->bcBaser->element('contents_navi') ?>
	</div>

	<div>
		<?php if(!empty($widgetArea)): ?>
		<?php $this->bcBaser->element('widget_area',array('no'=>$widgetArea)) ?>
		<?php endif ?>
	</div>
	
	<section id="ToTop">
	<a href="#Page">PAGE TOP</a>
	</section>
	
	<footer>
		<?php $this->bcBaser->element('global_menu') ?>
		<address>Copyright(C) 2008 - 2011 <br />baserCMS All rights Reserved.</address>
		<div class="banner">
		<?php $this->bcBaser->link($bcBaser->getImg('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")),'http://basercms.net') ?>
		<?php $this->bcBaser->link($bcBaser->getImg('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")),'http://cakephp.jp') ?>
		</div>
	</footer>
	
</div>
<?php $this->bcBaser->func() ?>
</body>
</html>