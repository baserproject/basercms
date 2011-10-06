<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] デフォルトレイアウト
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views.layout
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
<?php $baser->css('font_small','stylesheet',array('title'=>'Small')) ?>
<?php $baser->css('font_medium','stylesheet',array('title'=>'Medium')) ?>
<?php $baser->css('font_large','stylesheet',array('title'=>'Large')) ?>
<?php $baser->css('import') ?>
<?php $baser->js(array(
	'jquery-1.6.2.min',
	'jquery.corner',
	'styleswitcher',
	'startup')) ?>
<?php $baser->scripts() ?>
</head>
<body id="<?php $baser->contentsName() ?>" class="normal">

<!-- begin page -->
<div id="page">

	<!-- begin gradationShadow -->
	<div id="gradationShadow">
	
		<!-- begin header -->
		<?php $baser->element('header') ?>
		<!-- end header -->
		
		<!-- begin contents -->
		<div id="contents">
		
			<!-- navigation -->
			<div id="navigation">
				<?php $baser->element('navi',array('title_for_element'=>$baser->getContentsTitle())); ?>
			</div>
			
			<!-- begin alfa -->
			<div id="alfa" >
			
				<!-- begin contentsBody -->
				<div id="contentsBody">
					<?php $baser->flash() ?>
					<?php $baser->content() ?>
					<?php $baser->element('contents_navi') ?>
				</div>
				<!-- end contentsBody -->
				
			</div>
			<!-- end alfa -->
			
			<!-- begin beta -->
			<?php $baser->element('sidebar') ?>
			<!-- end beta -->
			
			<div class="to-top"> <a href="#page">このページの先頭へ戻る</a> </div>
			
		</div>
		<!-- end contents -->
		
		<!-- begin footer -->
		<?php $baser->element('footer') ?>
		<!-- end footer -->
		
	</div>
	<!-- end gradationShadow -->
	
</div>
<!-- end page -->

<?php echo $cakeDebug; ?>
</body>
</html>