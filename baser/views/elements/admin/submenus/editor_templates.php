<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] エディタテンプレートメニュー
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
	<th>エディタテンプレートメニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $bcBaser->link('エディタテンプレート一覧', array('controller' => 'editor_templates', 'action' => 'index')) ?></li>
			<li><?php $bcBaser->link('エディタテンプレート登録', array('controller' => 'editor_templates', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>
