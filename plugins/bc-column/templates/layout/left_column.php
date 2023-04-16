<?php
/**
 * デフォルトレイアウト
 */
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, user-scalable=yes">
		<?php $this->BcBaser->title() ?>
		<?php $this->BcBaser->metaDescription() ?>
		<?php $this->BcBaser->metaKeywords() ?>
		<?php $this->BcBaser->icon() ?>
		<?php $this->BcBaser->rss('ニュースリリース RSS 2.0', '/news/index.rss') ?>
		
<!--[if lt IE 9]>
	<script src="js/IE9.js"></script>
	<script src="js/html5shiv-printshiv.js"></script>
<![endif]-->

		<?php $this->BcBaser->css(['style', 'colorbox/colorbox-1.6.1']) ?>
<?php if($this->BcBaser->isHome()): ?>
		<?php $this->BcBaser->css('top') ?>
<?php else: ?>
		<?php $this->BcBaser->css('page') ?>
<?php endif ?>
		<?php $this->BcBaser->css('responsive') ?>

<?php $this->BcBaser->js(array(
			'jquery-1.7.2.min',
			'jquery.bxSlider.min',
			'jquery.colorbox-1.6.1.min',
			'baser.min',
			'startup'
)) ?>
<?php $this->BcBaser->scripts() ?>
<?php $this->BcBaser->googleAnalytics() ?>
</head>

<body id="<?php $this->BcBaser->contentsName(true) ?>">
<div id="Wrapper">	
<?php $this->BcBaser->header() ?>

	<?php if ($this->BcBaser->isHome()): ?>
    <div id="PCMainImage" class="forPC">
        <div id="PCMainImageWrap">
        <?php $this->BcBaser->mainImage(array('all' => true, 'num' => 4)) ?>
        </div>
    </div>
    <div id="SPMainImage" class="forSP">
        <?php $this->BcBaser->mainImage(array('all' => false, 'num' => 5)) ?>
    </div>
	<?php else: ?>
	
	<div id="PageTitle">
	    <div class="body-wrap">
	    <?php if (!empty($this->Blog)): ?>
	        <h1><?php echo h($this->Blog->getTitle()) ?></h1>
    	<?php else: ?>
	        <h1><?php $this->BcBaser->contentsTitle() ?></h1>
	    <?php endif ?>

	    <?php if (!empty($this->Blog)): ?>
		    <?php if ($this->Blog->descriptionExists()): ?>
				<p class="blog-description"><?php $this->Blog->description() ?></p>
			<?php endif; ?>
		<?php endif; ?>
	    </div>
	</div>

	<?php endif ?>

	<?php if (!$this->BcBaser->isHome()): ?>
	<div id="Breadcrumbs">
		<?php $this->BcBaser->crumbsList(); ?>
	</div>
	<?php endif; ?>

	<div class="contents">
		<?php if ($this->BcBaser->isHome()): ?>
					<?php $this->BcBaser->element('toppage') ?>
		<?php else: ?>
		<div class="body-wrap clearfix">
			<div id="MainRight" class="main">
					<?php $this->BcBaser->flash() ?>
					<?php $this->BcBaser->content() ?>
			</div>

			<div id="SideLeft" class="side">
					<?php $this->BcBaser->element('sidebar') ?>
			</div>
		</div>
		<?php endif ?>
	</div>
	
	<div id="TopLink"><?php $this->BcBaser->img('footer/btn_pagetop.png', array('alt' => 'PAGE TOP')) ?></div>

<?php $this->BcBaser->footer() ?>
</div>
<?php $this->BcBaser->func() ?>
	</body>
</html>
