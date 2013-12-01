<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メニュー用のメニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>メニュー管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('メニュー一覧', array('controller' => 'menus', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('新規メニューを登録', array('controller' => 'menus', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>
