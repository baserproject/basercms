<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリー管理メニュー
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


<tr>
	<th>固定ページカテゴリー管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('一覧を表示する', array('controller' => 'page_categories', 'action' => 'index')) ?></li>
<?php if($newCatAddable): ?>
			<li><?php $bcBaser->link('新規に登録する', array('controller'=> 'page_categories', 'action' => 'add')) ?></li>
<?php endif ?>
		</ul>
	</td>
</tr>
