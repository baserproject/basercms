<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ツールバー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$publishTheme = $baser->HtmlEx->themeWeb;
if($this->name != 'Installations') {
	$baser->HtmlEx->themeWeb = 'themed/'.$baser->siteConfig['admin_theme'].'/';
	$baser->Javascript->themeWeb = 'themed/'.$baser->siteConfig['admin_theme'].'/';
}
$baser->js(array('outerClick','jquery.fixedMenu', 'yuga'));
?>
<script type="text/javascript">
$(function(){
	$('#UserMenu').fixedMenu();
});
</script>

<div id="ToolBar">		
	<div id="ToolbarInner" class="clearfix">
		<div id="ToolMenu">
			<ul>
				
				<?php if($this->name == 'Installations'): ?>
				<li><?php $baser->link('インストールマニュアル', 'http://basercms.net/manuals/introductions/4.html', array('target' => '_blank')) ?></li>
				<?php elseif(empty($this->params['admin'])): ?>
				<li><?php $baser->link($baser->getImg('admin/btn_logo.png', array('alt' => 'baserCMS管理システム', 'class' => 'btn')), '/admin', array('title' => 'baserCMS管理システム')) ?></li>
				<?php else: ?>
				<li><?php $baser->link($baser->siteConfig['name'], '/') ?></li>
				<?php endif ?>
				<?php if($baser->existsEditLink()): ?>
				<li><?php $baser->editLink() ?></li>
				<?php endif ?>
				<?php if($baser->existsPublishLink()): ?>
				<li><?php $baser->publishLink() ?></li>
				<?php endif ?>
				<?php if(!empty($this->params['admin'])): ?>
					<?php if(Configure::read('debug') == -1): ?>
				<li>&nbsp;&nbsp;<span class="corner5" id="DebugMode" title="インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。">インストールモード</span>&nbsp;&nbsp;</li>
					<?php elseif(Configure::read('debug') > 0): ?>
				<li>&nbsp;&nbsp;<span class="corner5" id="DebugMode" title="デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。">デバッグモード<?php echo mb_convert_kana(Configure::read('debug'), 'N') ?></span>&nbsp;&nbsp;</li>
					<?php endif; ?>
				<?php endif ?>
			</ul>
		</div>

		<div id="UserMenu">
			<ul class="clearfix">
				<li>
					<?php if(!empty($user)): ?>
					<?php $baser->link($user['real_name_1']." ".$user['real_name_2'].' '.$baser->getImg('admin/btn_dropdown.png', array('width' => 8, 'height' => 11, 'class' => 'btn')), 'javascript:void(0)', array('class' => 'title')) ?>
					<ul>
						<li><?php $baser->link('アカウント設定', array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id'])) ?></li>
						<li><?php $baser->link('ログアウト', array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'logout')) ?></li>
					</ul>
					<?php elseif($this->name != 'Installations' && $this->params['url']['url'] != 'admin/users/login'): ?>
					<?php $baser->link('ログインしていません '.$baser->getImg('admin/btn_dropdown.png', array('width' => 8, 'height' => 11, 'class' => 'btn')), 'javascript:void(0)', array('class' => 'title')) ?>
					<ul>
						<li><?php $baser->link('ログイン', array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'login')) ?></li>
					</ul>
					<?php endif ?>
				</li>
			</ul>
		</div>
	</div>
</div>

<?php $baser->HtmlEx->themeWeb = $publishTheme ?>