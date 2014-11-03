<?php
/**
 * [ADMIN] ユーザー管理メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>ユーザー管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('ユーザー一覧', array('controller' => 'users', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('ユーザー新規追加', array('controller' => 'users', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>