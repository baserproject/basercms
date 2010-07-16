<?php
/* SVN FILE: $Id$ */
/**
 * デフォルトレイアウト
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
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
<?php $baser->css('font_small','stylesheet',array('title'=>'small')) ?>
<?php $baser->css('font_medium','stylesheet',array('title'=>'medium')) ?>
<?php $baser->css('font_large','stylesheet',array('title'=>'large')) ?>
<?php $baser->css('import') ?>
<?php $baser->css('colorbox/colorbox') ?>
<!--[if IE]><?php $baser->js('excanvas') ?><![endif]-->
<?php $baser->js(array(	'jquery-1.3.2.min',
									'jquery-ui-1.7.2.custom.min',
									'i18n/ui.datepicker-ja',
									'jquery.bt.min',
									'jquery.corner',
                  'jquery.colorbox-min',
									'DD_belatedPNG_0.0.8a-min',
									'functions',
									'styleswitcher',
									'startup')) ?>
<?php $baser->scripts() ?>
</head>
<body id="<?php $baser->contentsName() ?>">

<!-- begin page -->
<div id="page">


	<!-- begin header -->
	<?php $baser->element('header') ?>
	<!-- end header -->


	<!-- begin contents -->
	<div id="contents" class="clearfix">


		<!-- begin alfa -->
		<div id="alfa" >


			<!-- begin contentsBody -->
			<div id="contentsBody" class="clearfix">

				<?php $baser->flash() ?>
				<?php $baser->content() ?>

			</div>
			<!-- end contentsBody -->


		</div>
		<!-- end alfa -->


		<?php if(!$baser->isTop() || !empty($this->params['member'])): ?>
			<!-- begin beta -->
			<?php $baser->element('sidebar') ?>
			<!-- end beta -->
		<?php endif ?>


		<?php if(!$baser->isTop()): ?>
			<div class="to-top">
				<a href="#page">このページの先頭へ戻る</a>
			</div>
		<?php endif ?>


	</div>
	<!-- end contents -->


</div>
<!-- end page -->

<!-- begin footer -->
<?php $baser->element('footer') ?>
<!-- end footer -->

<?php echo $cakeDebug; ?>
</body>
</html>