<?php
/**
 * [ADMIN] ユーザーグループ管理メニュー
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
<?php if ($this->BcBaser->isAdminUser()): ?>
	<tr>
		<th>ユーザーグループ管理メニュー</th>
		<td>
			<ul class="cleafix">
				<li><?php $this->BcBaser->link('ユーザーグループ一覧', array('controller' => 'user_groups', 'action' => 'index')) ?></li>
				<li><?php $this->BcBaser->link('ユーザーグループ新規登録', array('controller' => 'user_groups', 'action' => 'add')) ?></li>
			</ul>
		</td>
	</tr>
<?php endif; ?>
