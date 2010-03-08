<?php
/* SVN FILE: $Id$ */
/**
 * メンバーマイページレイアウト
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
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
<?php $baser->css('font_small','stylesheet',array('title'=>'small')) ?>
<?php $baser->css('font_medium','stylesheet',array('title'=>'medium')) ?>
<?php $baser->css('font_large','stylesheet',array('title'=>'large')) ?>
<?php $baser->css('import') ?>
<?php $baser->js(array('jquery-1.3.1.min','functions','styleswitcher')) ?>
<?php $baser->scripts() ?>
</head>
<body id="<?php $baser->contentsName() ?>">


<!-- begin page -->
<div id="page">


	<!-- begin gradationShadow -->
	<div id="gradationShadow">
	
	
		<!-- begin header -->
		<?php echo $baser->element('member'.DS.'header') ?>
		<!-- end header -->
		
		
		<!-- begin contents -->
		<div id="contents">
		
		
			<!-- begin navigation -->
			<div id="navigation">
			
				<div id="pankuzu">
					<?php echo $baser->element('navi',array('title_for_element'=>$baser->getContentsTitle())); ?>
				</div>
				
				<?php if($this->params['url']['url'] != 'member/users/login'): ?>
					<div id="loginUser">
					<?php if(Configure::read('debug')>0): ?>
						<span>只今デバッグ中</span>
					<?php else: ?>
						<span><?php echo $user['real_name_1']." ".$user['real_name_2'] ?>&nbsp;&nbsp;様</span>
					<?php endif; ?>
					</div>
				<?php endif; ?>
			
			</div>
			<!-- end navigation -->
			
			
			<!-- begin alfa -->
			<div id="alfa" >
			
			
				<!-- begin contentsBody -->
				<div id="contentsBody">
				
				
					<?php $baser->flash() ?>
					<?php $baser->content() ?>
				
				
				</div>
				<!-- end contentsBody -->
			
			
			</div>
			<!-- end alfa -->
			
			
			<!-- begin beta -->
			<?php $baser->element('member'.DS.'sidebar') ?>
			<!-- end beta -->
		
		
		</div>
		<!-- end contents -->
		
		
		<!-- begin footer -->
		<?php $baser->element('member'.DS.'footer') ?>
		<!-- end footer -->
	
	
	</div>
	<!-- end gradationShadow -->


</div>
<!-- end page -->


<?php echo $cakeDebug; ?>
</body>
</html>