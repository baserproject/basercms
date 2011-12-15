<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] グローバルメニュー用のメニュー
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
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>グローバルメニュー<br />
		管理メニュー</h2>
	<ul>
		<li><?php $baser->link('グローバルメニュー一覧', array('controller' => 'global_menus', 'action' => 'index')) ?></li>
		<li><?php $baser->link('新規メニューを登録', array('controller' => 'global_menus', 'action' => 'add')) ?></li>
	</ul>
</div>
