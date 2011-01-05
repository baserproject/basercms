<?php
/* SVN FILE: $Id$ */
/**
 * 管理者レイアウト
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
<meta name="robots" content="noindex,nofollow" />
<?php $baser->charset() ?>
<?php $baser->title() ?>
<?php $baser->metaDescription() ?>
<?php $baser->metaKeywords() ?>
<?php $baser->icon() ?>
<?php $baser->css('font_small','stylesheet',array('title'=>'small')) ?>
<?php $baser->css('font_medium','stylesheet',array('title'=>'medium')) ?>
<?php $baser->css('font_large','stylesheet',array('title'=>'large')) ?>
<?php $baser->css('admin/import') ?>
<?php $baser->css(array('jquery-ui-1.7.2/ui.all','colorbox/colorbox')) ?>
<!--[if IE]><?php $baser->js(array('excanvas')) ?><![endif]-->
<?php $baser->js(array(
	'jquery-1.4.2.min',
	'jquery.dimensions.min',
	'jquery-ui-1.7.2.custom.min',
	'i18n/ui.datepicker-ja',
	'jquery.bt.min',
	'jquery.colorbox-min',
	'jquery.corner',
	'functions',
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
		<?php $baser->element('header'); ?>
		<!-- end header -->
		
		<!-- begin contents -->
		<div id="contents">

			<?php if($this->params['url']['url'] != 'admin/users/login' &&
					$this->params['url']['url'] != 'installations/update' &&
					$this->params['url']['url'] != 'installations/reset' &&
					($this->name != 'CakeError' || isset($_SESSION['Auth']['User']))): ?>
			<!-- begin navigation -->
			<div id="navigation" class="clearfix">
				<div id="pankuzu">
					<?php $baser->element('navi',array('title_for_element'=>$title_for_layout)); ?>
				</div>
				<div id="loginUser">
					<?php if(Configure::read('debug')>0): ?>
					<span>只今デバッグ中</span>
					<?php else: ?>
					<span>
					<?php $baser->link($user['real_name_1']." ".$user['real_name_2']."  様",array('plugin'=>null,'controller'=>'users','action'=>'edit',$user['id'])) ?>
					&nbsp;| &nbsp;
					<?php $baser->link('ログアウト',array('plugin'=>null,'controller'=>'users','action'=>'logout')) ?>
					</span>
					<?php endif; ?>
				</div>
			</div>
			<!-- end navigation -->
			<?php endif; ?>

			<?php if($this->params['controller']!='installations' && $this->action != 'update'): ?>
			<?php $baser->updateMessage() ?>
			<?php endif ?>

			<?php if($this->params['controller']!='installations' && Configure::read('Baser.firstAccess')): ?>
			<div id="FirstMessage">
				BaserCMSへようこそ。短くスマートなURLを実現する「スマートURL」の設定は、
				<?php $baser->link('システム設定', '/admin/site_configs/form') ?>より行えます。
			</div>
			<?php endif ?>
			
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
			<?php $baser->element('sidebar'); ?>
			<!-- end beta -->
			
			<div class="to-top"> <a href="#page">このページの先頭へ戻る</a> </div>
			
		</div>
		<!-- end contents -->
		
		<!-- begin footer -->
		<?php $baser->element('footer'); ?>
		<!-- end footer -->
		
	</div>
	<!-- end gradationShadow -->
	
</div>
<!-- end page -->

<?php echo $cakeDebug; ?>
</body>
</html>