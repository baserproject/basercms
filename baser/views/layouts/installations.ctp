<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストーラー用レイアウト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
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
<meta name="robots" content="noindex,nofollow" />
<?php $baser->charset() ?>
<title><?php echo $title_for_layout ?>　コーポレートサイトにちょうどいいCMS - baserCMS -</title>
<?php echo $html->meta('description','baserCMSのインストーラー') ?>
<?php $baser->icon() ?>
<?php $baser->css('font_small','stylesheet',array('title'=>'Small')) ?>
<?php $baser->css('font_medium','stylesheet',array('title'=>'Medium')) ?>
<?php $baser->css('font_large','stylesheet',array('title'=>'Large')) ?>
<?php $baser->css('import') ?>
<?php $baser->js(array(
	'jquery-1.4.2.min',
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
		<?php $baser->element('installations_header') ?>
		<!-- end header -->
		
		<!-- begin contents -->
		<div id="contents">
		
			<!-- navigation -->
			<!--<div id="navigation">
            <?php $baser->element('navi',array('title_for_element'=>$title_for_layout)); ?>
            </div>-->

			<!-- begin alfa -->
			<div id="alfa" >
			
				<!-- begin contentsBody -->
				<div id="contentsBody">
					<?php if($this->name != 'CakeError'): ?>
						<?php if ($title_for_layout): ?>
					<h2><?php echo $title_for_layout; ?></h2>
						<?php endif ?>
					<?php endif; ?>
					<?php if ($session->check('Message.flash')): ?>
					<?php $session->flash() ?>
					<?php endif ?>
					<?php echo $content_for_layout; ?>
					<?php $resets = array('step3','step4') ?>
					<?php if(in_array($this->action,$resets)): ?>
					<p><small>
						<?php $baser->link('≫ インストールを完全に最初からやり直す場合はコチラをクリックしてください','/installations/reset') ?>
						</small></p>
					<?php endif ?>
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