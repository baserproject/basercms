<?php
/**
 * レイアウト（スマホ用）
 * 呼出箇所：全ページ
 */
?>
<?php $this->BcBaser->docType('html5') ?>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=320, user-scalable=no">
		<?php $this->BcBaser->title() ?>
		<?php $this->BcBaser->metaDescription() ?>
		<?php $this->BcBaser->metaKeywords() ?>
		<?php $this->BcBaser->icon() ?>
		<?php $this->BcBaser->webClipIcon() ?>
		<?php $this->BcBaser->css(array('smartphone/style', 'slicknav/slicknav')) ?>
		<?php $this->BcBaser->js(array(
			'jquery-1.11.3.min',
			'jquery.slicknav-1.0.6.min',
			'jquery.bxslider-4.12.min'
		)); ?>
		<script>
			$(function(){
				$("#MainImage").show();
				$("#MainImage").bxSlider({mode:"fade", auto:true});
				$("nav").slicknav({label:'MENU'});
			});
		</script>
		<?php $this->BcBaser->scripts() ?>
		<!-- /Elements/smartphone/google_analytics.php -->
		<?php $this->BcBaser->googleAnalytics() ?>
	</head>
	<body id="<?php $this->BcBaser->contentsName() ?>">
		<div id="Page">

			<header>
				<div id="Logo"><?php $this->BcBaser->logo() ?></div>
				<!-- /Elements/smartphone/global_menu.php -->
				<nav><?php $this->BcBaser->globalMenu() ?></nav>
			</header>

			<?php if ($this->BcBaser->isHome()): ?>
				<?php $this->BcBaser->mainImage(array('all' => true, 'num' => 5, 'width' => "100%")) ?>
			<?php endif ?>

			<div id="ContentsBody" class="contents-body clearfix">
				<?php $this->BcBaser->flash() ?>
				<?php $this->BcBaser->content() ?>
			</div>

			<!-- /Elements/smartphone/widget_area.php -->
			<div><?php $this->BcBaser->widgetArea() ?></div>

			<footer>
				<!-- /Elements/smartphone/global_menu.php -->
				<?php $this->BcBaser->globalMenu() ?>
				<address>Copyright(C) <?php $this->BcBaser->copyYear(2008) ?> <br>baserCMS All rights Reserved.</address>
				<div class="banner">
					<?php $this->BcBaser->link($this->BcBaser->getImg('baser.power.gif', array('alt' => 'baserCMS : Based Website Development Project', 'border' => "0")), 'http://basercms.net') ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('cake.power.gif', array('alt' => 'CakePHP(tm) : Rapid Development Framework', 'border' => "0")), 'http://cakephp.jp') ?>
				</div>
			</footer>

		</div>
		<?php $this->BcBaser->func() ?>
	</body>
</html>
