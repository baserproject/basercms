<!DOCTYPE html>
<html dir="ltr" lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
<?php $this->BcBaser->charset() ?> 
<?php $this->BcBaser->title() ?>
<?php $this->BcBaser->metaDescription() ?>
<?php $this->BcBaser->metaKeywords() ?>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
<?php $this->BcBaser->icon() ?>
<?php $this->BcBaser->rss('ニュースリリース RSS 2.0', '/news/index.rss') ?>
<?php $this->BcBaser->css(array(
'style',
'camera')); ?>
<?php $this->BcBaser->js(array(
'admin/jquery-1.7.2.min',
'admin/functions',
'jquery.mousewheel.js',
'jquery.easing.1.3.js',
'jquery.easie.js',
'jquery.setting.js')); ?>
<!--[if lt IE 9]>
<script src="<?php $this->BcBaser->themeUrl() ?>js/html5shiv.js"></script>
<script src="<?php $this->BcBaser->themeUrl() ?>js/selectivizr.js"></script>
<![endif]-->
<!--[if lte IE 7]>
<script src="<?php $this->BcBaser->themeUrl() ?>js/jquery.ie6warning.js"></script>
<![endif]-->
<!--[if IE 6.0]>
<script src="<?php $this->BcBaser->themeUrl() ?>js/DD_belatedPNG.js"></script>
<![endif]-->
<?php $this->BcBaser->scripts() ?> 
<?php if ($this->BcBaser->isHome()): ?>
<script src="<?php $this->BcBaser->themeUrl() ?>js/camera.js"></script>
<script src="<?php $this->BcBaser->themeUrl() ?>js/jquery.mobile.customized.min.js"></script>
<script>
	$(function() {
	//camera.js
	$('.cameraWrap').camera({
		fx: 'random', //エフェクトタイプ
		loader: 'bar', //ローダーのタイプ
		barPosition: 'bottom', //ローダーのバーの位置
		loaderColor: '#6AA52E', //ローダーの色
		loaderBgColor: '#FFFFFF',　//ローダーの背景の色
		time: 4000, //スライドの表示時間
		transPeriod: 1500, //スライドアニメーションの時間
		thumbnails: false
	});
	
	//parallax
		var mainV = $('.mainVisual').offset().top;
		$('.mainVisual .cameraSlide').css({'top':-100});
		$(window).scroll(function() {
			var value = $(this).scrollTop();
		$('.mainVisual .cameraSlide').css({'top': mainV - 400 + value / 2});
	});
});
</script>
<?php endif ?>
<?php $this->BcBaser->element('google_analytics') ?>
</head>





<body>
<?php echo $this->BcBaser->element('header') ?>


<?php if ($this->BcBaser->isHome()): ?>
<div class="mainVisual">
<div class="camera_wrap camera_black_skin cameraWrap">


<div class="cameraList" data-src="<?php $this->BcBaser->themeUrl() ?>img/mainvisual/main_visual01.jpg">
<div class="camera_caption fadeIn">
<div class="circle visual1">
<div class="tableCell">
<?php $this->BcBaser->mainImage(array('num' => 1, 'link' => false, 'class' => 'mainImg')) ?> 
<?php $this->BcBaser->img('/img/mainvisual/visual_comment1.png',array('alt'=>'コーポレートサイトにちょうどいい国産CMS')) ?>
</div><!-- /tableCell -->
</div><!-- /circle -->
</div><!-- /camera_caption -->
</div><!-- /cameraList -->

<div class="cameraList" data-src="<?php $this->BcBaser->themeUrl() ?>img/mainvisual/main_visual02.jpg">
<div class="camera_caption fadeIn">
<div class="circle visual2">
<div class="tableCell">
<?php $this->BcBaser->mainImage(array('num' => 2, 'link' => false, 'class' => 'mainImg')) ?> 
<?php $this->BcBaser->img('/img/mainvisual/visual_comment2.png',array('alt'=>'全て日本語の国産CMSだから設置も更新も簡単、わかりやすい。')) ?>
<ul>
<li>マニュアルも日本語でらくらく設置</li>
<li>管理画面をシンプルにカスタマイズできる</li>
<li>Word並の操作で簡単更新</li>
<li>わかりやすいナビゲーション</li>
</ul>
</div><!-- /tableCell -->
</div><!-- /circle -->
</div><!-- /camera_caption -->
</div><!-- /cameraList -->

<div class="cameraList" data-src="<?php $this->BcBaser->themeUrl() ?>img/mainvisual/main_visual03.jpg">
<div class="camera_caption fadeIn">
<div class="circle visual3">
<div class="tableCell">
<?php $this->BcBaser->mainImage(array('num' => 3, 'link' => false, 'class' => 'mainImg')) ?> 
<?php $this->BcBaser->img('/img/mainvisual/visual_comment3.png',array('alt'=>'標準的なWEBサイトに必要な基本機能を全て装備')) ?>
<ol>
<li>複数のブログ設置機能</li>
<li>メールフォームもいくつでも</li>
<li>Googleマップでアクセスマップ表示</li>
<li>スマホ・ケータイ標準対応</li>
<li>基本的なSEO対策装備</li>
</ol>
</div><!-- /tableCell -->
</div><!-- /circle -->
</div><!-- /camera_caption -->
</div><!-- /cameraList -->

<div class="cameraList" data-src="<?php $this->BcBaser->themeUrl() ?>img/mainvisual/main_visual04.jpg">
<div class="camera_caption fadeIn">
<div class="circle visual4">
<div class="tableCell">
<?php $this->BcBaser->mainImage(array('num' => 4, 'link' => false, 'class' => 'mainImg')) ?> 
<?php $this->BcBaser->img('/img/mainvisual/visual_comment3.png',array('alt'=>'デザインも自由自在にカスタマイズ可能！')) ?>
<ul>
<li>複雑なレイアウトでも柔軟に対応！</li>
<li>コンテンツごとに違うデザインの適用可！</li>
<li>一部の機能だけの使用可！</li>
<li>スマホ用テーマも！</li>
</ul>
</div><!-- /tableCell -->
</div><!-- /circle -->
</div><!-- /camera_caption -->
</div><!-- /cameraList -->

</div><!-- /cameraWrap -->
</div><!-- /mainVisual -->

<script>
$(".cameraList").each(function() {
	$(this).attr('data-src', $(this).find('.mainImg').attr('src'));
});
</script>
<?php endif ?>


<div class="container">

<?php /* ページコンテンツ */ ?>
<?php $this->BcBaser->flash() ?>
<?php $this->BcBaser->content() ?>

<?php if ($this->BcBaser->isHome()): ?>
<?php /* 事業案内 */ ?>
<?php $this->BcBaser->page('/service') ?>

<?php /* 会社概要 */ ?>
<?php $this->BcBaser->page('/company') ?>

<?php /* 採用情報 */ ?>
<?php $this->BcBaser->page('/recruit') ?>

<?php /* お問い合わせ */ ?>
<div class="articleArea bgGray" id="contact">
<article class="mainWidth">
<h2 class="fontawesome-circle-arrow-down">CONTACT <span>お問い合わせ</span></h2>
<script>
$(function(){
	$.get($("#BaseUrl").html() + '/contact/index',
	function(result){
		$("#MailForm").html(result);
	});
});
</script>

<div id="MailForm"></div>
</article>
</div><!-- /articleArea -->


<?php /* ウィジェットエリア */ ?>
<?php $this->BcBaser->widgetArea() ?>
<?php endif ?>

</div><!-- /container -->



<?php echo $this->BcBaser->element('footer') ?>
<?php $this->BcBaser->func() ?>
</body>
</html>
