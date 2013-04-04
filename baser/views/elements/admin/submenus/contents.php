<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ダッシュボードメニュー
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
	<th>検索インデックスメニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('コンテンツ一覧', array('controller' => 'contents', 'action' => 'index')) ?></li>
			<li><?php $bcBaser->link('コンテンツ登録', array('controller' => 'contents', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>
