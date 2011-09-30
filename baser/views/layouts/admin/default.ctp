<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] デフォルトレイアウト
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
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
$paramPrefix = '';
$paramUrl = $this->params['url']['url'];
if(isset($this->params['prefix'])) {
	$paramPrefix = $this->params['prefix'];
}
if($this->params['controller'] == 'updaters' ||
		$this->params['controller'] == 'installations' ||
		$paramUrl == ($paramPrefix.'/users/login')) {
	$useNavi = false;
} else {
	$useNavi = true;
}
if($this->name == 'CakeError'){
	$useNavi = false;
}
if(empty($_SESSION['Auth']['User']) && Configure::read('debug') == 0) {
	$useNavi = false;
}
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
<?php $baser->css('font_small','stylesheet',array('title'=>'Small')) ?>
<?php $baser->css('font_medium','stylesheet',array('title'=>'Medium')) ?>
<?php $baser->css('font_large','stylesheet',array('title'=>'Large')) ?>
<?php $baser->css('admin/import') ?>
<?php $baser->css(array('jquery-ui/ui.all','colorbox/colorbox')) ?>
<!--[if IE]><?php $baser->js(array('excanvas')) ?><![endif]-->
<!--[if IE 6]>
<?php $baser->js(array('fixed-1.8', 'DD_belatedPNG_0.0.8a', 'DD_belatedPNG_config')) ?>
<![endif]-->
<?php $baser->js(array(
	'jquery-1.6.2.min',
	'jquery.dimensions.min',
	'jquery-ui-1.8.14.custom.min',
	'i18n/ui.datepicker-ja',
	'jquery.bt.min',
	'jquery.colorbox-min',
	'jquery.corner-2.12',
	'functions',
	'styleswitcher',
	'admin/startup')) ?>
<?php $baser->scripts() ?>
</head>
<body id="<?php $baser->contentsName() ?>" class="normal">

<!-- loader -->
<div id="Waiting" class="waiting-box">
	<div class="corner5">
	<?php echo $html->image('ajax-loader.gif') ?><br />
    Waiting...
	</div>
</div>

<!-- begin page -->
<div id="page">

	<!-- begin gradationShadow -->
	<div id="gradationShadow">
	
		<!-- begin header -->
		<?php $baser->element('header', array('useNavi'=>$useNavi)); ?>
		<!-- end header -->
		
		<!-- begin contents -->
		<div id="contents">

			<?php if($useNavi): ?>
			<!-- begin navigation -->
			<div id="navigation" class="clearfix">
				<div id="pankuzu">
					<?php $baser->element('navi',array('title_for_element'=>$title_for_layout)); ?>
				</div>
				<div id="loginUser">
					<span>
					<?php if(!empty($user)): ?>
					<?php $baser->link($user['real_name_1']." ".$user['real_name_2']."  様",array('plugin'=>null,'controller'=>'users','action'=>'edit',$user['id'])) ?>
					<?php endif ?>
					<?php if(Configure::read('debug')>0): ?>
					&nbsp;[<?php echo Configure::read('debug') ?>]
					<?php endif; ?>
					</span>
					&nbsp;| &nbsp;
					<?php $baser->link('ログアウト',array('plugin'=>null,'controller'=>'users','action'=>'logout')) ?>
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