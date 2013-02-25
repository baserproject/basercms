<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] デフォルトレイアウト
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views.layout
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
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
<?php $bcBaser->css('font_small','stylesheet',array('title'=>'Small')) ?>
<?php $bcBaser->css('font_medium','stylesheet',array('title'=>'Medium')) ?>
<?php $bcBaser->css('font_large','stylesheet',array('title'=>'Large')) ?>
<?php $bcBaser->css('import') ?>
<?php $bcBaser->js(array(
	'jquery-1.6.2.min',
	'jquery.corner',
	'styleswitcher',
	'startup')) ?>
<?php $bcBaser->scripts() ?>
</head>
<body id="<?php $bcBaser->contentsName() ?>" class="normal">

<!-- begin page -->
<div id="page">

	<!-- begin gradationShadow -->
	<div id="gradationShadow">
	
		<!-- begin header -->
		<?php $bcBaser->element('header') ?>
		<!-- end header -->
		
		<!-- begin contents -->
		<div id="contents">
		
			<!-- navigation -->
			<div id="navigation">
				<?php $bcBaser->element('navi',array('title_for_element'=>$bcBaser->getContentsTitle())); ?>
			</div>
			
			<!-- begin alfa -->
			<div id="alfa" >
			
				<!-- begin contentsBody -->
				<div id="contentsBody">
					<?php $bcBaser->flash() ?>
					<?php $bcBaser->content() ?>
					<?php $bcBaser->element('contents_navi') ?>
				</div>
				<!-- end contentsBody -->
				
			</div>
			<!-- end alfa -->
			
			<!-- begin beta -->
			<?php $bcBaser->element('sidebar') ?>
			<!-- end beta -->
			
			<div class="to-top"> <a href="#page">このページの先頭へ戻る</a> </div>
			
		</div>
		<!-- end contents -->
		
		<!-- begin footer -->
		<?php $bcBaser->element('footer') ?>
		<!-- end footer -->
		
	</div>
	<!-- end gradationShadow -->
	
</div>
<!-- end page -->

<?php echo $cakeDebug; ?>
</body>
</html>