<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] コンテンツメニュー
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
?>


<?php if(!empty($user)): ?>
<div id="ContentsMenu">
	<ul class="clearfix">
<?php if(!empty($search)): ?>
		<li><?php $bcBaser->link($bcBaser->getImg('admin/btn_menu_search.png', array('alt' => '検索', 'width' => 50, 'height' => '18', 'class' => 'btn')), 'javascript:void(0)', array('id' => 'BtnMenuSearch')) ?></li>
<?php endif ?>
<?php if(!empty($help)): ?>
		<li><?php $bcBaser->link($bcBaser->getImg('admin/btn_menu_help.png', array('alt' => 'ヘルプ', 'width' => 60, 'height' => '18', 'class' => 'btn')), 'javascript:void(0)', array('id' => 'BtnMenuHelp')) ?></li>
<?php endif ?>
<?php if($bcBaser->isAdminUser()): ?>
		<li><?php $bcBaser->link($bcBaser->getImg('admin/btn_menu_permission.png', array('alt' => '制限設定', 'width' => 50, 'height' => '18', 'class' => 'btn')), 'javascript:void(0)', array('id' => 'BtnMenuPermission')) ?></li>
<?php endif ?>
	</ul>
</div>
<?php endif ?>