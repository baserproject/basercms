<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * サブサイト管理メニュー
 */
?>


<tr>
	<th>サブサイト管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('サブサイト一覧', array('plugin' => '', 'admin' => true, 'controller' => 'sites', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('サブサイト新規登録', array('plugin' => '', 'admin' => true, 'controller' => 'sites', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>
