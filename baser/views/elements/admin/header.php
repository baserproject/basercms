<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ヘッダー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(!empty($this->params['prefix'])) {
	$loginUrl = $this->params['prefix'].'/users/login';
} else {
	$loginUrl = '/users/login';
}
?>
<div id="Header" class="clearfix">
	<?php $bcBaser->element('toolbar') ?>
<?php if($this->name == 'Installations' || $this->params['url']['url'] == $loginUrl || $bcAdmin->isAdminGlobalmenuUsed()): ?>
	<div class="clearfix" id="HeaderInner">
	
	<?php if(!empty($user)): ?>
		<div id="GlobalMenu">
			<ul class="clearfix">
				<li><?php $bcBaser->link($bcBaser->getImg('admin/btn_header_menu_1.png', array('width' => 118, 'height' => 26, 'alt' => '固定ページ管理', 'class' => 'btn', 'title' => '固定ページ管理')), array('plugin' => '', 'controller' => 'pages', 'action' => 'index')) ?></li>
				<li><?php $bcBaser->link($bcBaser->getImg('admin/btn_header_menu_2.png', array('width' => 123, 'height' => 26, 'alt' => 'ウィジェット管理', 'class' => 'btn', 'title' => 'ウィジェット管理')), array('plugin' => '', 'controller' => 'widget_areas', 'action' => 'index')) ?></li>
				<li><?php $bcBaser->link($bcBaser->getImg('admin/btn_header_menu_4.png', array('width' => 98, 'height' => 26, 'alt' => 'テーマ管理', 'class' => 'btn', 'title' => 'テーマ管理')), array('plugin' => '', 'controller' => 'themes', 'action' => 'index')) ?></li>
				<li><?php $bcBaser->link($bcBaser->getImg('admin/btn_header_menu_3.png', array('width' => 120, 'height' => 26, 'alt' => 'プラグイン管理', 'class' => 'btn', 'title' => 'プラグイン管理')), array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')) ?></li>
				<li><?php $bcBaser->link($bcBaser->getImg('admin/btn_header_menu_5.png', array('width' => 104, 'height' => 26, 'alt' => 'システム管理', 'class' => 'btn', 'title' => 'システム管理')), array('plugin' => '', 'controller' => 'site_configs', 'action' => 'form')) ?></li>	
			</ul>
		</div>
	<?php endif ?>
	
		<div id="Logo">
	<?php if(!empty($user)): ?>
			<?php $bcBaser->link($bcBaser->getImg('admin/logo_header.png', array('width' => 153, 'height' => 30, 'alt' => 'baserCMS')), array('plugin' => null, 'controller' => 'dashboard', 'action' => 'index')) ?>
	<?php else: ?>
			<?php $bcBaser->img('admin/logo_header.png', array('width' => 153, 'height' => 30, 'alt' => 'baserCMS')) ?>
	<?php endif ?>
		</div>
	
	</div>
<?php endif ?>
<!-- / #Header .clearfix --></div>