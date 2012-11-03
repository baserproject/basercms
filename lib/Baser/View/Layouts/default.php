<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] デフォルトレイアウト
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views.layout
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php $this->BcBaser->xmlHeader() ?>
<?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<?php $this->BcBaser->charset() ?>
<?php $this->BcBaser->title() ?>
<?php $this->BcBaser->metaDescription() ?>
<?php $this->BcBaser->metaKeywords() ?>
<?php $this->BcBaser->icon() ?>
<?php $this->BcBaser->css('font_small', array('title'=>'Small')) ?>
<?php $this->BcBaser->css('font_medium', array('title'=>'Medium')) ?>
<?php $this->BcBaser->css('font_large', array('title'=>'Large')) ?>
<?php $this->BcBaser->css('import') ?>
<?php $this->BcBaser->js(array(
	'jquery-1.6.2.min',
	'jquery.corner',
	'styleswitcher',
	'startup')) ?>
<?php $this->BcBaser->scripts() ?>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>" class="normal">

<!-- begin page -->
<div id="page">

	<!-- begin gradationShadow -->
	<div id="gradationShadow">
	
		<!-- begin header -->
		<?php $this->BcBaser->element('header') ?>
		<!-- end header -->
		
		<!-- begin contents -->
		<div id="contents">
		
			<!-- navigation -->
			<div id="navigation">
				<?php $this->BcBaser->element('navi',array('title_for_element'=>$this->BcBaser->getContentsTitle())); ?>
			</div>
			
			<!-- begin alfa -->
			<div id="alfa" >
			
				<!-- begin contentsBody -->
				<div id="contentsBody">
					<?php $this->BcBaser->flash() ?>
					<?php $this->BcBaser->content() ?>
					<?php $this->BcBaser->element('contents_navi') ?>
				</div>
				<!-- end contentsBody -->
				
			</div>
			<!-- end alfa -->
			
			<!-- begin beta -->
			<?php $this->BcBaser->element('sidebar') ?>
			<!-- end beta -->
			
			<div class="to-top"> <a href="#page">このページの先頭へ戻る</a> </div>
			
		</div>
		<!-- end contents -->
		
		<!-- begin footer -->
		<?php $this->BcBaser->element('footer') ?>
		<!-- end footer -->
		
	</div>
	<!-- end gradationShadow -->
	
</div>
<!-- end page -->

<?php echo $cakeDebug; ?>
</body>
</html>