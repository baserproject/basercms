<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] サイドメニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<div class="side-navi">
	<h2>ログインメニュー</h2>
	<ul>
		<li><?php $bcBaser->link('管理者ログイン', array('plugin' => null, 'controller' => 'users', 'action' => 'login'), array('target' => '_blank')) ?></li>
	</ul>
</div>