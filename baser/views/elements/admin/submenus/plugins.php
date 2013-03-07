<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] プラグインメニュー
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


<tr>
	<th>プラグイン管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('プラグイン一覧', array('plugin' => null, 'controller' => 'plugins', 'action' => 'index')) ?></li>
		</ul>
	</td>
</tr>
