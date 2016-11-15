<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーザー管理メニュー
 */
?>


<tr>
	<th>ユーザー管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('ユーザー一覧', array('controller' => 'users', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('ユーザー新規追加', array('controller' => 'users', 'action' => 'add')) ?></li>
			<li><?php $this->BcBaser->link('ユーザーグループ一覧', array('controller' => 'user_groups', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('ユーザーグループ新規登録', array('controller' => 'user_groups', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>